<?

namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use CIBlockElement;
use CIBlockSection;
use CFile;
use CUtil;

Loader::includeModule("iblock");

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

class Traveline
{
    private static $testDomain = 'naturalistbx.idemcloud.ru';
    private static $testHotels = [8866, 8867, 8617];

    private static $travelineApiURL = 'https://partner.tlintegration.com/api';
    private static $travelineApiKey = '5498c10a-4bfd-4728-8da2-e283ac52fda1';

    private static $travelineSectionPropEnumId = '1';
    private static $travelineElementPropEnumId = '5';

    private static $arErrors = [
        'Reservation already cancelled' => 'Бронирование во внешнем сервисе уже отменено.',
    ];

    // HL ID Особенности объекта
    private static $campingFeaturesHLId = 5;
    // HL ID Особенности номера
    private static $roomsFeaturesHLId = 8;
    // HL ID Питание
    private static $campingFoodHLId = 12;

    /* Получение списка свободных объектов в выбранный промежуток */
    public static function search($guests, $arChildrenAge, $dateFrom, $dateTo, $sectionIds = []) {
        $rsSections = CIBlockSection::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $sectionIds, "ACTIVE" => "Y", "!UF_EXTERNAL_ID" => false, "UF_EXTERNAL_SERVICE" => self::$travelineSectionPropEnumId), false, array("ID", "UF_EXTERNAL_ID"), false);
        $arSectionExternalIDs = array();
        while($arSection = $rsSections->Fetch()) {
            $arSectionExternalIDs[] = (string)$arSection["UF_EXTERNAL_ID"];
        }

        $url = self::$travelineApiURL.'/search/v1/properties/room-stays/search';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "propertyIds" => $arSectionExternalIDs,
            "adults" => $guests,
            "arrivalDate" => date('Y-m-d', strtotime($dateFrom)),
            "departureDate" => date('Y-m-d', strtotime($dateTo)),
            "include" => ""
        );
        if(count($arChildrenAge) > 0) {
            $data["childAges"] = $arChildrenAge;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($data)
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);

        $arHotelsIDs = array();
        foreach($arResponse["roomStays"] as $arItem) {
            if(empty($arHotelsIDs[$arItem["propertyId"]]) || $arItem["total"]["priceBeforeTax"] < $arHotelsIDs[$arItem["propertyId"]])
                $arHotelsIDs[$arItem["propertyId"]] = $arItem["total"]["priceBeforeTax"];
        }

        return $arHotelsIDs;
    }

    /* Получение списка свободных номеров объекта в выбранный промежуток */
    public static function searchRooms($sectionId, $externalId, $guests, $arChildrenAge, $dateFrom, $dateTo) {
        // Номера
        $rsElements = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                "ACTIVE"    => "Y",
                "SECTION_ID" => $sectionId,
            ),
            false,
            false,
            array("IBLOCK_ID", "ID", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID")
        );
        $arElementsIDs = array();
        while($arElement = $rsElements->Fetch()) {
            $arElementsIDs[$arElement["PROPERTY_EXTERNAL_ID_VALUE"]][$arElement["PROPERTY_EXTERNAL_CATEGORY_ID_VALUE"]] = $arElement["ID"];
        }

        $url = self::$travelineApiURL.'/search/v1/properties/'.$externalId.'/room-stays/';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "adults" => $guests,
            "arrivalDate" => date('Y-m-d', strtotime($dateFrom)),
            "departureDate" => date('Y-m-d', strtotime($dateTo)),
        );
        if(count($arChildrenAge) > 0) {
            $data["childAges"] = $arChildrenAge;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url.'?'.http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers
        ));
        $response = curl_exec($ch);
        $arItems = json_decode($response, true);
        curl_close($ch);

        $arRooms = array();
        foreach($arItems["roomStays"] as $arItem) {
            $externalId = $arItem['ratePlan']['id'];
            $externalCategoryId = $arItem['roomType']['id'];

            $elementId = $arElementsIDs[$externalId][$externalCategoryId];
            date_default_timezone_set('UTC');
            $arRooms[$elementId][$arItem['checksum']] = array(
                'price'              => $arItem['total']['priceBeforeTax'],
                'checksum'           => $arItem['checksum'],
                'fullPlacementsName' => $arItem['fullPlacementsName'],
                'cancelPossible'     => $arItem['cancellationPolicy']['freeCancellationPossible'],
                'cancelDate'         => date('d.m.Y H:i', strtotime($arItem['cancellationPolicy']['freeCancellationDeadlineUtc'])+10800),
                'cancelAmount'       => $arItem['cancellationPolicy']['penaltyAmount'] ?? 0,
                'includedServices'   => $arItem['includedServices']
            );
        }
        return $arRooms;
    }

    /* Выгрузка кемпингов и номеров */
    public static function update() {
        $url = self::$travelineApiURL.'/content/v1/properties';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "include" => "all"
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url.'?'.http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers,
        ));
        $response = curl_exec($ch);
        $arTravelineItems = json_decode($response, true);
        curl_close($ch);

        Debug::writeToFile(var_export($arTravelineItems, true), '$arTravelineItems');

        $iS = new CIBlockSection();
        $iE = new CIBlockElement();
        $campingFeaturesEntityClass = self::getEntityClass(self::$campingFeaturesHLId);
        $roomsFeaturesEntityClass   = self::getEntityClass(self::$roomsFeaturesHLId);

        $rsElements = CIBlockElement::GetList(false, array("IBLOCK_ID" => SERVICES_IBLOCK_ID), false, false, array("IBLOCK_ID", "ID", "CODE"));
        while ($arServiceItem = $rsElements->Fetch()) {
            $arServiceItems[$arServiceItem['CODE']] = $arServiceItem;
        }

        $rsSectionObjects = CIBlockSection::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID), false, array('UF_*'));
        while ($arSectionItem = $rsSectionObjects->Fetch()){
            $arSectionItems[$arSectionItem['UF_EXTERNAL_ID']] = $arSectionItem;
        }
        foreach($arTravelineItems["properties"] as $arSection) {
            $sectionName = $arSection["name"];
            $sectionCode = CUtil::translit($sectionName, "ru");
            $arImages = array();

            // HL Особенности номера
            $arAmenities = array();
            foreach($arSection["roomTypes"] as $arElement) {
                if($arElement["amenities"]) {
                    foreach($arElement["amenities"] as $arItem) {
                        $name = stripslashes($arItem["name"]);
                        $code = $arItem["code"];

                        $rsData = $roomsFeaturesEntityClass::getList([
                            "select" => ["*"],
                            "filter" => [
                                "UF_XML_ID" => $code
                            ],
                            "order" => ["UF_SORT" => "ASC"],
                        ]);
                        $arEntity = $rsData->Fetch();

                        if(!$arEntity) {
                            $arValues = array(
                                'UF_NAME' => $name,
                                'UF_XML_ID' => $code,
                                'UF_SORT' => 500,
                                'UF_TRAVELINE' => true,
                            );
                            $result = $roomsFeaturesEntityClass::add($arValues);
                            $entityId = $result->getId();

                        } else {
                            $arValues = array(
                                'UF_NAME' => $name,
                                'UF_TRAVELINE' => true,
                            );
                            $entityId = $arEntity["ID"];
                            $roomsFeaturesEntityClass::update($entityId, $arValues);
                        }
                    }
                }
            }

            // ИБ Услуги
            /*$arServices = array();
            if($arSection["services"]) {
                foreach($arSection["services"] as $arItem) {
                    $name = $arItem["name"];
                    $code = "travel_service_". CUtil::translit($arItem["name"], "ru");

                    $arFields = array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => SERVICES_IBLOCK_ID,
                        "NAME" => $name,
                        "CODE" => $code,
                        "PROPERTY_VALUES" => array(
                            "TRAVELINE" => getEnumIdByXml(SERVICES_IBLOCK_ID, 'TRAVELINE', 'Y')
                        )
                    );

                    if (array_key_exists($code, $arServiceItems)) {
                        $elementId = $arServiceItems[$code]["ID"];
                        $res = $iE->Update($elementId, $arFields);

                    } else {
                        $elementId = $iE->Add($arFields);
                    }

                    if($elementId) {
                        $arServices[] = $elementId;
                    }
                }
            }*/

            // Кол-во номеров
            $roomsCount = 0;
            foreach($arSection["ratePlans"] as $arRatePlan) {
                foreach($arSection["roomTypes"] as $arRoomType) {
                    $roomsCount++;
                }
            }

            // Поля раздела
            $arFields = array(
                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                //"ACTIVE" => "Y",
                //"NAME" => $sectionName,
                //"CODE" => $sectionCode,
                //"DESCRIPTION" => $arSection["description"],
                "UF_EXTERNAL_ID" => $arSection["id"],
                "UF_EXTERNAL_SERVICE" => self::$travelineSectionPropEnumId,
                "UF_PHOTOS" => $arImages,
                "UF_ADDRESS" => $arSection["contactInfo"]["address"]["addressLine"],
                //"UF_COORDS" => $arSection["contactInfo"]["address"]["latitude"].",".$arSection["contactInfo"]["address"]["longitude"],
                "UF_TIME_FROM" => $arSection["policy"]["checkInTime"],
                "UF_TIME_TO" => $arSection["policy"]["checkOutTime"],
                //"UF_SERVICES" => $arServices,
                "UF_ROOMS_COUNT" => $roomsCount,
            );

            //$arExistSection = CIBlockSection::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "UF_EXTERNAL_ID" => $arSection["id"]), false, array('UF_*'))->Fetch();
            if(array_key_exists($arSection["id"], $arSectionItems)) {
                $arExistSection = $arSectionItems[$arSection["id"]];
                // Проверка чекбоксов полей
                if($arExistSection["UF_CHECKBOX_1"]) {
                    unset($arFields["UF_PHOTOS"]);
                } else {
                    $arImages = self::getImages($arSection["images"]);
                    $arFields["UF_PHOTOS"] = $arImages;
                }
                if($arExistSection["UF_CHECKBOX_2"]) {
                    unset($arFields["UF_SERVICES"]);
                }
                if($arExistSection["UF_CHECKBOX_3"]) {
                    unset($arFields["UF_FEATURES"]);
                }
                if($arExistSection["UF_CHECKBOX_4"]) {
                    unset($arFields["UF_ROOMS_COUNT"]);
                }
                if($arExistSection["UF_CHECKBOX_5"]) {
                    unset($arFields["UF_FOOD"]);
                }
                unset($arFields["UF_ADDRESS"]);

                $sectionId = $arExistSection["ID"];
                $res = $iS->Update($sectionId, $arFields);

                if($res)
                    echo date('Y-M-d H:i:s'). " Обновлен раздел (".$sectionId.") \"".$sectionName."\"<br>\r\n";

            } else {
                $arFields["ACTIVE"] = "N";
                $arFields["NAME"] = $sectionName;
                $arFields["CODE"] = $sectionCode;
                $arFields["DESCRIPTION"] = $arSection["description"];
                $arFields["UF_COORDS"] = $arSection["contactInfo"]["address"]["latitude"].",".$arSection["contactInfo"]["address"]["longitude"];

                $arImages = self::getImages($arSection["images"]);
                $arFields["UF_PHOTOS"] = $arImages;
                $sectionId = $iS->Add($arFields);

                if($sectionId)
                    echo "Добавлен раздел (".$sectionId.") \"".$sectionName."\"<br>\r\n";
            }

            // Номера
            foreach($arSection["ratePlans"] as $arRatePlan) {
                foreach($arSection["roomTypes"] as $arRoomType) {
                    $elementName = $arRatePlan["name"]." (".$arRoomType["name"].")";
                    $elementCode = CUtil::translit($elementName, "ru");

                    $arElementImages = array();
                    $arElementImages = self::getImages($arRoomType["images"]);

                    // Amenities
                    $arAmenities = array();
                    foreach($arRoomType["amenities"] as $arItem) {
                        $rsData = $roomsFeaturesEntityClass::getList([
                            "select" => ["*"],
                            "filter" => [
                                "UF_XML_ID" => $arItem["code"]
                            ],
                            "order" => ["UF_SORT" => "ASC"],
                        ]);
                        $arEntity = $rsData->Fetch();
                        if($arEntity) {
                            $arAmenities[] = $arEntity["UF_XML_ID"];
                        }
                    }

                    // Поля элемента
                    $arExistElement = CIBlockElement::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "PROPERTY_EXTERNAL_ID" => $arRatePlan['id'], "PROPERTY_EXTERNAL_CATEGORY_ID" => $arRoomType["id"]))->Fetch();
                    if($arExistElement) {
                        $elementId = $arExistElement["ID"];
                        $arFields = array(
                            //"NAME" => $elementName,
                            "CODE" => $elementCode,
                            //"DETAIL_TEXT" => nl2br($arRoomType["description"]),
                            //"DETAIL_TEXT_TYPE" => 'html'
                        );

                        $res = $iE->Update($elementId, $arFields);
                        CIBlockElement::SetPropertyValuesEx($elementId, CATALOG_IBLOCK_ID, array(
                            //"PHOTOS" => $arElementImages,
                            "FEATURES" => $arAmenities,
                            "SQUARE" => $arRoomType['size']['value'],
                        ));

                        if($res)
                            echo "Обновлен номер (".$elementId.") \"".$elementName."\" в отеле (".$sectionId.") \"".$sectionName."\"<br>\r\n";

                    } else {
                        $arFields = array(
                            "ACTIVE" => "Y",
                            "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                            "IBLOCK_SECTION_ID" => $sectionId,
                            "NAME" => $elementName,
                            "CODE" => $elementCode,
                            "DETAIL_TEXT" => nl2br($arRoomType["description"]),
                            "DETAIL_TEXT_TYPE" => 'html',
                            "PROPERTY_VALUES" => array(
                                "PHOTOS" => $arElementImages,
                                "EXTERNAL_ID" => $arRatePlan["id"],
                                "EXTERNAL_CATEGORY_ID" => $arRoomType["id"],
                                "EXTERNAL_SERVICE" => self::$travelineElementPropEnumId,
                                "FEATURES" => $arAmenities,
                                "SQUARE" => $arRoomType['size']['value'],
                            )
                        );
                        $elementId = $iE->Add($arFields);

                        if($elementId)
                            echo date('Y-M-d H:i:s') . " Добавлен номер (".$elementId.") \"".$elementName."\" в отель (".$sectionId.") \"".$sectionName."\"<br>\r\n";
                    }
                }
            }
        }
    }

    public static function getImages($arImagesUrl) {
        $arImages = array();
        foreach ($arImagesUrl as $key => $arImage) {
            $arFile = CFile::MakeFileArray($arImage["url"]);

            if ($arFile) {
                $arImages[] = $arFile;
            }
        }

        return $arImages;
    }

    /* Обновление HL Питание */
    public static function updateFood() {
        $url = self::$travelineApiURL.'/content/v1/meal-plans';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers,
        ));
        $response = curl_exec($ch);
        $arFood = json_decode($response, true);
        curl_close($ch);

        $campingFoodEntityClass = self::getEntityClass(self::$campingFoodHLId);
        foreach($arFood as $arItem) {
            $name = stripslashes($arItem["name"]);
            $code = stripslashes($arItem["code"]);

            $rsData = $campingFoodEntityClass::getList([
                "select" => ["*"],
                "filter" => [
                    "UF_CODE" => $code
                ],
                "order" => ["UF_SORT" => "ASC"],
            ]);
            $arEntity = $rsData->Fetch();

            if(!$arEntity) {
                $arValues = array(
                    'UF_NAME' => $name,
                    'UF_CODE' => $code,
                    'UF_SORT' => 500,
                    'UF_TRAVELINE' => true,
                    'UF_SHOW_FILTER' => false,
                );
                $result = $campingFoodEntityClass::add($arValues);
                $entityId = $result->getId();

            } else {
                $arValues = array(
                    'UF_NAME' => $name,
                    'UF_TRAVELINE' => true,
                    'UF_SHOW_FILTER' => $arEntity["UF_SHOW_FILTER"],
                );
                $entityId = $arEntity["ID"];
                $campingFoodEntityClass::update($entityId, $arValues);
            }
        }
    }

    /* Проверка возможности бронирование объекта из заказа */
    public static function verifyReservation($externalSectionId, $externalElementId, $externalCategoryId, $guests, $arChildrenAge, $dateFrom, $dateTo, $price, $checksum, $arGuestList, $arUser) {
        // Получение объекта номера для запроса бронирования
        $url = self::$travelineApiURL.'/search/v1/properties/'.$externalSectionId.'/room-stays/';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "adults" => $guests,
            "arrivalDate" => date('Y-m-d', strtotime($dateFrom)),
            "departureDate" => date('Y-m-d', strtotime($dateTo)),
        );
        if(isset($arChildrenAge) && count($arChildrenAge) > 0) {
            $data["childAges"] = $arChildrenAge;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url.'?'.http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers
        ));
        $response = curl_exec($ch);
        $arItemsResponse = json_decode($response, true);
        curl_close($ch);
        if($arItemsResponse['roomStays']) {
            $arExternalData = array();
            foreach ($arItemsResponse['roomStays'] as $key => $arItem) {
                if ($arItem['ratePlan']['id'] == $externalElementId && $arItem['roomType']['id'] == $externalCategoryId && $checksum == $arItem["checksum"]) {
                    $arExternalData = $arItem;
                    break;
                }
            }

            $arConditionsError['warnings'][0]['code'] = 'ConditionsChanged';
            if($arExternalData) {
                // Проверка на актуальность данных
                if($arExternalData["checksum"] != $checksum) {
                    return $arConditionsError;
                }
                if($arExternalData['total']['priceBeforeTax'] != (int)$price) {
                    return $arConditionsError;
                }

                // Запрос на возможность бронирования номера
                $url = self::$travelineApiURL.'/reservation/v1/bookings/verify';
                $headers = array(
                    "X-API-KEY: ".self::$travelineApiKey,
                    "Content-Type: application/json"
                );

                $arGuests = array();
                foreach ($arGuestList as $value) {
                    $e = explode(' ', $value);
                    if (!empty($e[0]) || !empty($e[1]) || !empty($e[2])) {
                        $arGuests[] = array(
                            "firstName" => $e[1],
                            "lastName" => $e[0],
                            "middleName" => $e[2]
                        );
                    }
                }
                $data = array(
                    "booking" => [
                        "propertyId" => $arExternalData["propertyId"],
                        "roomStays" => [
                            [
                                "stayDates" => $arExternalData["stayDates"],
                                "ratePlan" => $arExternalData["ratePlan"],
                                "roomType" => $arExternalData["roomType"],
                                "guests" => $arGuests,
                                "guestCount" => $arExternalData["guestCount"],
                                "checksum" => $arExternalData["checksum"]
                            ]
                        ],
                        "customer" => [
                            "firstName" => !empty($arUser["NAME"]) ? $arUser["NAME"] : $arGuests[0]["firstName"],
                            "lastName" => !empty($arUser["LAST_NAME"]) ? $arUser["LAST_NAME"] : $arGuests[0]["lastName"],
                            "middleName" => !empty($arUser["SECOND_NAME"]) ? $arUser["SECOND_NAME"] : $arGuests[0]["middleName"],
                            "contacts" => [
                                "phones" => [
                                    [
                                        "phoneNumber" => "+".preg_replace('![^0-9]+!', '', $arUser["PERSONAL_PHONE"])
                                    ]
                                ],
                                "emails" => [
                                    [
                                        "emailAddress" => $arUser["EMAIL"]
                                    ]
                                ]
                            ]
                        ],
                        "prepayment" => [
                            "remark" => "Полная оплата",
                            "paymentType" => "PrePay",
                            "prepaidSum" => (int)$price
                        ]
                    ]
                );

                //file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log.txt', serialize($data) . PHP_EOL, FILE_APPEND);
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POST           => 1,
                    CURLOPT_HTTPHEADER     => $headers,
                    CURLOPT_POSTFIELDS     => json_encode($data)
                ));
                $response = curl_exec($ch);
                $arVerifyResponse = json_decode($response, true);
                curl_close($ch);
                //file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log.txt', serialize($arVerifyResponse) . PHP_EOL, FILE_APPEND);
                $arResponse = $arVerifyResponse;

            } else {
                return $arConditionsError;
            }

        } else {
            $arResponse = $arItemsResponse;
        }

        return $arResponse;
    }

    /* Бронирование объекта из заказа */
    public static function makeReservation($orderId, $arOrder, $arUser, $reservationPropId) {
        $externalSectionId = $arOrder['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_ID'];
        $externalElementId = $arOrder['ITEMS'][0]['ITEM']['PROPERTIES']['EXTERNAL_ID']['VALUE'];
        $externalCategoryId = $arOrder['ITEMS'][0]['ITEM']['PROPERTIES']['EXTERNAL_CATEGORY_ID']['VALUE'];
        $dateFrom = $arOrder['PROPS']['DATE_FROM'];
        $dateTo = $arOrder['PROPS']['DATE_TO'];
        $guests = $arOrder['PROPS']['GUESTS_COUNT'];
        $arChildrenAge = $arOrder['PROPS']['CHILDREN_AGE'];
        $price = $arOrder['FIELDS']['BASE_PRICE'];
        $checksum = $arOrder['PROPS']['CHECKSUM'];
        $arGuestList = $arOrder['PROPS']['GUEST_LIST'];

        $arVerifyResponse = self::verifyReservation($externalSectionId, $externalElementId, $externalCategoryId, $guests, $arChildrenAge, $dateFrom, $dateTo, $price, $checksum, $arGuestList, $arUser);
        if($arVerifyResponse["booking"]) {
            if($arVerifyResponse["booking"]['roomStays']) {
                foreach ($arVerifyResponse["booking"]['roomStays'] as $key => &$arItem) {
                    $arItem['services'] = [];
                }
            }

            $url = self::$travelineApiURL.'/reservation/v1/bookings';
            $headers = array(
                "X-API-KEY: ".self::$travelineApiKey,
                "Content-Type: application/json"
            );
            $data = array(
                "booking" => $arVerifyResponse["booking"]
            );

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST           => 1,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_POSTFIELDS     => json_encode($data)
            ));
            $response = curl_exec($ch);
            $arResponse = json_decode($response, true);
            curl_close($ch);
            //file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log.txt', serialize($data) . PHP_EOL, FILE_APPEND);
            if($arResponse['booking']['status'] == "Confirmed" && $arResponse['booking']['number']) {
                // Сохраняем ID бронирования в заказе
                $reservationId = $arResponse['booking']['number'];

                $order = Order::load($orderId);
                $propertyCollection = $order->getPropertyCollection();
                $propertyValue = $propertyCollection->getItemByOrderPropertyId($reservationPropId);
                $propertyValue->setValue($reservationId);
                $res = $order->save();

                if($res->isSuccess()) {
                    return $reservationId;

                } else {
                    return [
                        "ERROR" => "Ошибка сохранения ID бронирования."
                    ];
                }

            } else {
                $errorText = $arResponse['warnings'][0]['code'] ?? $arResponse['errors'][0]['message'];
                //file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log.txt', serialize($arResponse) . PHP_EOL, FILE_APPEND);
                return [
                    "ERROR" => "Ошибка запроса бронирования. ".$errorText
                ];
            }

        } else {
            $errorText = $arVerifyResponse['warnings'][0]['code'] ?? $arVerifyResponse['errors'][0]['message'];
            return [
                "ERROR" => "Ошибка верификации бронирования. ".$errorText
            ];
        }
    }

    /* Отмена бронирования объекта из заказа - перед отменой  */
    public static function beforeCancelReservation($arOrder) {
        $reservationId = $arOrder['PROPS']['RESERVATION_ID'];
        //file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log.txt', $reservationId . PHP_EOL, FILE_APPEND);

        $url = self::$travelineApiURL.'/reservation/v1/bookings/'.$reservationId.'/calculate-cancellation-penalty';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "cancellationDateTimeUtc" => date('Y-m-d\TH:i:s\Z')
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url.'?'.http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers,
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);
        //file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log.txt', serialize($arResponse) . PHP_EOL, FILE_APPEND);
        return $arResponse["penaltyAmount"] ?? false;
    }

    /* Отмена бронирования объекта из заказа  */
    public static function cancelReservation($arOrder) {
        $reservationId = $arOrder['PROPS']['RESERVATION_ID'];

        $url = self::$travelineApiURL.'/reservation/v1/bookings/'.$reservationId.'/cancel';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "reason" => "Отмена поездки из ЛК"
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($data)
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);

        if($arResponse['booking']['cancellation']) {
            return true;

        } else {
            $errorText = $arResponse['errors'][0]['message'];
            return [
                "ERROR" => "Ошибка запроса отмены бронирования во внешнем сервисе. ".self::$arErrors[$errorText]
            ];
        }
    }

    /* Установка минимальных цен для объектов Traveline */
    public static function setMinPrices() {
        $rsSections = CIBlockSection::GetList(array(), array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "!UF_EXTERNAL_ID" => false, "UF_EXTERNAL_SERVICE" => self::$travelineSectionPropEnumId), false, array("ID", "UF_EXTERNAL_ID"), false);
        $arSectionExternalIDs = array();
        while($arSection = $rsSections->Fetch()) {
            $arSectionExternalIDs[] = (string)$arSection["UF_EXTERNAL_ID"];
        }

        $url = self::$travelineApiURL.'/search/v1/properties/room-stays/search';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "propertyIds" => $arSectionExternalIDs,
            "adults" => 1,
            "arrivalDate" => date('Y-m-d'),
            "departureDate" => date('Y-m-d'),
            "include" => ""
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($data)
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);

        $arMinPrices = array();
        foreach($arResponse["roomStays"] as $arItem) {
            if(empty($arMinPrices[$arItem["propertyId"]]) || $arItem["total"]["priceBeforeTax"] < $arMinPrices[$arItem["propertyId"]])
                $arMinPrices[$arItem["propertyId"]] = $arItem["total"]["priceBeforeTax"];
        }

        $iS = new CIBlockSection();
        foreach($arMinPrices as $externalSectionId => $price) {
            $arExistSection = CIBlockSection::GetList(array(), array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "UF_EXTERNAL_ID" => $externalSectionId))->Fetch();
            if($arExistSection) {
                $sectionId = $arExistSection["ID"];
                $iS->Update($sectionId, array(
                    "UF_MIN_PRICE" => $price,
                ));
            }
        }
    }

    /* Получение штрафа за отмену */
    public static function getCancellationAmount($externalId, $guests, $arChildrenAge, $dateFrom, $dateTo, $checksum, $externalElementId, $externalCategoryId) {
        $url = self::$travelineApiURL.'/search/v1/properties/'.$externalId.'/room-stays/';
        $headers = array(
            "X-API-KEY: ".self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "adults" => $guests,
            "arrivalDate" => date('Y-m-d', strtotime($dateFrom)),
            "departureDate" => date('Y-m-d', strtotime($dateTo)),
        );
        if(isset($arChildrenAge) && count($arChildrenAge) > 0) {
            $data["childAges"] = $arChildrenAge;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url.'?'.http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);

        date_default_timezone_set('UTC');
        foreach($arResponse["roomStays"] as $arItem) {
            if($externalElementId == $arItem['ratePlan']['id'] && $externalCategoryId == $arItem['roomType']['id'] && $checksum == $arItem['checksum']) {
                return [
                    'possible' => $arItem['cancellationPolicy']['freeCancellationPossible'],
                    'date' => date('d.m.Y H:i', strtotime($arItem['cancellationPolicy']['freeCancellationDeadlineUtc'])+10800),
                    'cancelAmount' => $arItem['cancellationPolicy']['penaltyAmount'] ?? 0
                ];
            }
        }

        return false;
    }

    /* Объект HL */
    private static function getEntityClass($hlId) {
        Loader::IncludeModule('highloadblock');
        $hlblock = HighloadBlockTable::getById($hlId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}