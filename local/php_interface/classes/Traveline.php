<?

namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Session\Handlers\Table\UserSessionTable;
use CIBlockElement;
use CIBlockSection;
use CFile;
use CUtil;
use Naturalist\Products;
use Naturalist\CustomFunctions;

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

    private static $travellineAmenitiesRequestLink = '/content/v1/room-amenity-categories';
    private static $travellineHotelsRequestLink = '/content/v1/properties';

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
    public static function search($guests, $arChildrenAge, $dateFrom, $dateTo, $sectionIds = [])
    {
        $rsSections = CIBlockSection::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $sectionIds, "ACTIVE" => "Y", "!UF_EXTERNAL_ID" => false, "UF_EXTERNAL_SERVICE" => self::$travelineSectionPropEnumId), false, array("ID", "UF_EXTERNAL_ID", 'UF_MIN_CHIELD_AGE'), false);
        $arSectionExternalIDs = array();
        while ($arSection = $rsSections->Fetch()) {
            $noChield = false;
            if (is_array($arChildrenAge) && count($arChildrenAge) && $arSection['UF_MIN_CHIELD_AGE'] != '') {
                foreach ($arChildrenAge as $age) {
                    if ($age < $arSection['UF_MIN_CHIELD_AGE']) {                        
                        $noChield = true;
                    }
                }
            }   

            if (!$noChield) {
                $arSectionExternalIDs[] = (string)$arSection["UF_EXTERNAL_ID"];            
            }
        }

        $url = self::$travelineApiURL . '/search/v1/properties/room-stays/search';
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "propertyIds" => $arSectionExternalIDs,
            "adults" => $guests,
            "arrivalDate" => date('Y-m-d', strtotime($dateFrom)),
            "departureDate" => date('Y-m-d', strtotime($dateTo)),
            "include" => ""
        );
        
        if (count($arChildrenAge) > 0) {
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
        foreach ($arResponse["roomStays"] as $arItem) {
            if (empty($arHotelsIDs[$arItem["propertyId"]]) || $arItem["total"]["priceBeforeTax"] < $arHotelsIDs[$arItem["propertyId"]])
                $arHotelsIDs[$arItem["propertyId"]] = $arItem["total"]["priceBeforeTax"];
        }

        return $arHotelsIDs;
    }

    /* Получение списка свободных номеров объекта в выбранный промежуток */
    public static function searchRooms($sectionId, $externalId, $guests, $arChildrenAge, $dateFrom, $dateTo, $minChildAge = 0)
    {        
        $error = '';

        // Проверка на минимально разрешённый возраст детей
        if (is_array($arChildrenAge) && count($arChildrenAge) && $minChildAge != 0) {
            foreach ($arChildrenAge as $age) {
                if ($age < $minChildAge) {
                    $error = 'Заезд возможен только с детьми от '.$minChildAge.' лет';
                }
            }
        }        

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
        while ($arElement = $rsElements->Fetch()) {
            $arElementsIDs[$arElement["PROPERTY_EXTERNAL_ID_VALUE"]][$arElement["PROPERTY_EXTERNAL_CATEGORY_ID_VALUE"]] = $arElement["ID"];
        }

        $url = self::$travelineApiURL . '/search/v1/properties/' . $externalId . '/room-stays/';
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "adults" => $guests,
            "arrivalDate" => date('Y-m-d', strtotime($dateFrom)),
            "departureDate" => date('Y-m-d', strtotime($dateTo)),
        );
        if (count($arChildrenAge) > 0) {
            $data["childAges"] = $arChildrenAge;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers
        ));
        $response = curl_exec($ch);
        $arItems = json_decode($response, true);
        curl_close($ch);

        $arRooms = array();
        
        foreach ($arItems["roomStays"] as $arItem) {
            $externalId = $arItem['ratePlan']['id'];
            $externalCategoryId = $arItem['roomType']['id'];

            $elementId = $arElementsIDs[$externalId][$externalCategoryId];
            date_default_timezone_set('UTC');
            $arRooms[$elementId][$arItem['checksum']] = array(
                'price'              => $arItem['total']['priceBeforeTax'],
                'checksum'           => $arItem['checksum'],
                'fullPlacementsName' => $arItem['fullPlacementsName'],
                'cancelPossible'     => $arItem['cancellationPolicy']['freeCancellationPossible'],
                'cancelDate'         => date('d.m.Y H:i', strtotime($arItem['cancellationPolicy']['freeCancellationDeadlineUtc']) + 10800),
                'cancelAmount'       => $arItem['cancellationPolicy']['penaltyAmount'] ?? 0,
                'includedServices'   => $arItem['includedServices']
            );
        }

        if ($error != '') {
            $arRooms = [];
        }

        return [
            'arItems' => $arRooms,
            'error' => $error,
        ];
    }

    /**
     * Обновляет активность разделов (отелей)     
     * 
     */
    public static function updateActivity()
    {
        $iS = new CIBlockSection();
        // Получаем разделы сайта и ID отелей от сервиса
        $arSectionItems = self::getSections();
        $arTravelineItems = self::getResponse(self::$travellineHotelsRequestLink, ["include" => ""]);

        // Преобразуем пришедшие данные
        if (is_array($arTravelineItems) && count($arTravelineItems)) {
            foreach ($arTravelineItems['properties'] as $travel) {
                $arTravellineExternal[] = $travel['id'];
            }
        }

        // Активация/деактивация
        if ($arTravellineExternal) {
            foreach ($arSectionItems as $externalId => $section) {
                if (array_search($externalId, $arTravellineExternal) !== null) {
                    $res = $iS->Update($section['ID'], ['ACTIVE' => 'Y']);
                } else {
                    $res = $iS->Update($section['ID'], ['ACTIVE' => 'N']);
                }
            }
        }
    }

    /**
     * Загружает картинки для разделов
     * 
     */
    public static function downloadSectionImages()
    {
        $travelineSectionOffset = Option::get("main", "traveline_section_offset");
        $travelineSectionLimit = Option::get("main", "traveline_section_limit");
        $travelineSectionCount = self::getSections(1, 0, 1, true)->getCount();
        $iS = new CIBlockSection();

        Option::set("main", "traveline_section_count", $travelineSectionCount);

        if ($travelineSectionOffset == '') {
            $travelineSectionOffset = 0;
            Option::set("main", "traveline_section_offset", $travelineSectionOffset);
        }

        if ($travelineSectionLimit == '') {
            $travelineSectionLimit = 20;
            Option::set("main", "traveline_section_limit", $travelineSectionLimit);
        }

        $sections = self::getSections($travelineSectionLimit, $travelineSectionOffset, 1, true)->fetchAll();

        foreach ($sections as $section) {
            if ($section["UF_CHECKBOX_1"] != 1) {
                $arImages = self::getImages(json_decode($section["UF_PHOTO_ARRAY"], true));
                if (count($arImages)) {
                    $arFields["UF_PHOTOS"] = $arImages;
                    $res = $iS->Update($section['ID'], $arFields);

                    if ($res) {
                        echo date('Y-M-d H:i:s') . " Загружены фото для раздела (" . $section['ID'] . ") \"" . $section['NAME'] . "\"<br>\r\n";
                    }
                }
            }
        }

        $nextOffset = $travelineSectionOffset + $travelineSectionLimit;

        if ($nextOffset >= $travelineSectionCount) {
            $nextOffset = 0;
        }

        Option::set("main", "traveline_section_offset", $nextOffset);
    }

    /**
     * Загружает картинки для элементов
     * 
     */
    public static function downloadElementImages()
    {
        $travelineElementOffset = Option::get("main", "traveline_element_offset");
        $travelineElementLimit = Option::get("main", "traveline_element_limit");
        $travelineElementCount = self::getRooms(1, 0, 1, true)->getCount();
        $iE = new CIBlockElement();

        Option::set("main", "traveline_element_count", $travelineElementCount);

        if ($travelineElementOffset == '') {
            $travelineElementOffset = 0;
            Option::set("main", "traveline_element_offset", $travelineElementOffset);
        }

        if ($travelineElementLimit == '') {
            $travelineElementLimit = 50;
            Option::set("main", "traveline_element_limit", $travelineElementLimit);
        }

        $rooms = self::getRooms($travelineElementLimit, $travelineElementOffset, 1, true)->fetchAll();

        foreach ($rooms as $room) {
            $arImages = self::getImages(json_decode(unserialize($room["PHOTO_ARRAY_VALUE"])['TEXT'], true));

            if (count($arImages)) {
                CIBlockElement::SetPropertyValuesEx($room['ID'], CATALOG_IBLOCK_ID, array(
                    "PHOTOS" => $arImages,
                ));

                echo date('Y-M-d H:i:s') . " Загружены фото для элемента (" . $room['ID'] . ") \"" . $room['NAME'] . "\"<br>\r\n";
            }
        }

        $nextOffset = $travelineElementOffset + $travelineElementLimit;

        if ($nextOffset >= $travelineElementCount) {
            $nextOffset = 0;
        }

        Option::set("main", "traveline_element_offset", $nextOffset);
    }

    /* Выгрузка кемпингов и номеров */
    public static function update()
    {
        // Сначала обновляем список возможных удобств
        // self::updateAmenities();

        // Все отели Travelline
        $arSectionItems = self::getSections();

        // Все номера Travelline
        $arCatalogItems = self::getRooms();

        // Все удобства в номерах
        // $arUpdatedAmenities = self::getAmenities();

        // Ответ от эндпоинта
        $arTravelineItems = self::getResponse(self::$travellineHotelsRequestLink, ["include" => "all"]);

        $iS = new CIBlockSection();
        $iE = new CIBlockElement();

        foreach ($arTravelineItems["properties"] as $arSection) {
            $sectionName = $arSection["name"];
            $sectionCode = CUtil::translit($sectionName, "ru");

            // Поля раздела
            $arFields = array(
                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                "UF_EXTERNAL_ID" => $arSection["id"],
                "UF_EXTERNAL_SERVICE" => self::$travelineSectionPropEnumId,
                "UF_ADDRESS" => $arSection["contactInfo"]["address"]["addressLine"],
                "UF_TIME_FROM" => $arSection["policy"]["checkInTime"],
                "UF_TIME_TO" => $arSection["policy"]["checkOutTime"],
                "UF_PHOTO_ARRAY" => json_encode($arSection["images"]),
            );

            if (array_key_exists($arSection["id"], $arSectionItems)) {
                $arExistSection = $arSectionItems[$arSection["id"]];
                // Проверка чекбоксов полей

                if ($arExistSection["UF_CHECKBOX_2"]) {
                    unset($arFields["UF_SERVICES"]);
                }
                if ($arExistSection["UF_CHECKBOX_3"]) {
                    unset($arFields["UF_FEATURES"]);
                }
                if ($arExistSection["UF_CHECKBOX_4"]) {
                    unset($arFields["UF_ROOMS_COUNT"]);
                }
                if ($arExistSection["UF_CHECKBOX_5"]) {
                    unset($arFields["UF_FOOD"]);
                }
                unset($arFields["UF_ADDRESS"]);

                $sectionId = $arExistSection["ID"];
                $res = $iS->Update($sectionId, $arFields);

                if ($res)
                    echo date('Y-M-d H:i:s') . " Обновлен раздел (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
            } else {
                $arFields["ACTIVE"] = "N";
                $arFields["NAME"] = $sectionName;
                $arFields["CODE"] = $sectionCode;
                $arFields["DESCRIPTION"] = $arSection["description"];
                $arFields["UF_COORDS"] = $arSection["contactInfo"]["address"]["latitude"] . "," . $arSection["contactInfo"]["address"]["longitude"];
                $arFields["UF_PHOTO_ARRAY"] = json_encode($arSection["images"]);

                $sectionId = $iS->Add($arFields);

                if ($sectionId)
                    echo "Добавлен раздел (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
            }

            // Номера
            foreach ($arSection["ratePlans"] as $arRatePlan) {
                foreach ($arSection["roomTypes"] as $arRoomType) {
                    $elementName = $arRatePlan["name"] . " (" . $arRoomType["name"] . ")";
                    $elementCode = CUtil::translit($elementName, "ru");

                    // Amenities
                    // $arAmenities = array();
                    // foreach ($arRoomType["amenities"] as $arItem) {
                    //     $arEntity = $arUpdatedAmenities[$arItem["code"]];
                    //     if ($arEntity) {
                    //         $arAmenities[] = $arEntity["UF_XML_ID"];
                    //     }
                    // }

                    // Поля элемента                    
                    $arExistElement = $arCatalogItems[$arRatePlan['id'] . '_' . $arRoomType["id"]];
                    if ($arExistElement) {
                        $elementId = $arExistElement["ID"];
                        $arFields = array(
                            "CODE" => $elementCode,
                        );

                        $res = $iE->Update($elementId, $arFields);
                        CIBlockElement::SetPropertyValuesEx($elementId, CATALOG_IBLOCK_ID, array(
                            "PHOTO_ARRAY" => json_encode($arRoomType['images']),
                            // "FEATURES" => $arAmenities,
                            "SQUARE" => $arRoomType['size']['value'],
                        ));

                        if ($res)
                            echo "Обновлен номер (" . $elementId . ") \"" . $elementName . "\" в отеле (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
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
                                "PHOTO_ARRAY" => json_encode($arRoomType['images']),
                                "EXTERNAL_ID" => $arRatePlan["id"],
                                "EXTERNAL_CATEGORY_ID" => $arRoomType["id"],
                                "EXTERNAL_SERVICE" => self::$travelineElementPropEnumId,
                                // "FEATURES" => $arAmenities,
                                "SQUARE" => $arRoomType['size']['value'],
                            )
                        );
                        $elementId = $iE->Add($arFields);

                        if ($elementId) {
                            Products::setQuantity($elementId);
                            Products::setPrice($elementId);
                            echo date('Y-M-d H:i:s') . " Добавлен номер (" . $elementId . ") \"" . $elementName . "\" в отель (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
                        }
                    }
                }
            }
            unset($arRatePlan);
            unset($arRoomType);
        }
    }

    /**
     * Возвращает все разделы (отели) Travelline
     * 
     * @param string $limit Ограничение элементов.
     * @param string $offset С какого элемента выбирать.
     * @param string $countTotal Подсчёт общего количества.
     * @param boolean $raw Вернуть не обработанный объект.
     *
     * @return array
     * 
     */
    private static function getSections($limit = '', $offset = '', $countTotal = '', $raw = false)
    {
        $entity = \Bitrix\Iblock\Model\Section::compileEntityByIblock(CATALOG_IBLOCK_ID);
        $rsSectionObjects = $entity::getList(
            [
                'filter' => ['IBLOCK_ID' => CATALOG_IBLOCK_ID, 'UF_EXTERNAL_SERVICE' => self::$travelineSectionPropEnumId],
                'select' => ['*', 'UF_*'],
                'limit' => $limit,
                'offset' => $offset,
                'count_total' => $countTotal
            ]
        );

        if ($raw) {
            return $rsSectionObjects;
        }

        while ($arSectionItem = $rsSectionObjects->Fetch()) {
            $arSectionItems[$arSectionItem['UF_EXTERNAL_ID']] = $arSectionItem;
        }
        return $arSectionItems;
    }

    /**
     * Возвращает все элементы (номера) Travelline
     * 
     * @param string $limit Ограничение элементов.
     * @param string $offset С какого элемента выбирать.
     * @param string $countTotal Подсчёт общего количества.
     * @param boolean $raw Вернуть не обработанный объект.
     *
     * @return array
     * 
     */
    public static function getRooms($limit = '', $offset = '', $countTotal = '', $raw = false)
    {
        $arRoomsRequest = \Bitrix\Iblock\Elements\ElementGlampingsTable::getList([
            'select' => ['ID', 'NAME', 'EXTERNAL_SERVICE_' => 'EXTERNAL_SERVICE', 'EXTERNAL_CATEGORY_ID_' => 'EXTERNAL_CATEGORY_ID', 'EXTERNAL_ID_' => 'EXTERNAL_ID', 'PHOTO_ARRAY_' => 'PHOTO_ARRAY'],
            'filter' => ['EXTERNAL_SERVICE.VALUE' => self::$travelineElementPropEnumId],
            'limit' => $limit,
            'offset' => $offset,
            'count_total' => $countTotal
        ]);

        if ($raw) {
            return $arRoomsRequest;
        }

        // Формируем массив элементов, где ключём будет сочетание значений 2-х свойств
        while ($room = $arRoomsRequest->Fetch()) {
            $arRooms[$room['EXTERNAL_ID_VALUE'] . '_' . $room['EXTERNAL_CATEGORY_ID_VALUE']] = $room;
        }

        return $arRooms;
    }

    /**
     * Возвращает все возможные удобства в номерах Travelline
     *
     * @return array
     * 
     */
    public static function getAmenities()
    {
        $roomsFeaturesEntityClass = self::getEntityClass(self::$roomsFeaturesHLId);
        $rsData = $roomsFeaturesEntityClass::getList(
            [
                "select" => ["*"],
                "filter" => [],
                "order" => ["UF_SORT" => "ASC"],
            ]
        );
        while ($amenity = $rsData->Fetch()) {
            $arAmenities[$amenity['UF_XML_ID']] = $amenity;
        }

        return $arAmenities;
    }

    /**
     * Обновляет все возможные удобства в номерах Travelline      
     */
    public static function updateAmenities()
    {
        $roomsFeaturesEntityClass = self::getEntityClass(self::$roomsFeaturesHLId);
        $arInternalAmenities = self::getAmenities();
        $apiResponse = self::getResponse(self::$travellineAmenitiesRequestLink);
        foreach ($apiResponse as $amenityCategory) {
            foreach ($amenityCategory['amenities'] as $amenity) {
                $arAmenitiesExternal[] = $amenity;
            }
        }

        foreach ($arAmenitiesExternal as $arItem) {
            $name = stripslashes($arItem["name"]);
            $code = $arItem["code"];

            $arEntity = $arInternalAmenities[$code];

            if (!$arEntity) {
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

    /**
     * Возвращает ответ от сервиса Travelline
     *
     * @param string $requestUrl Адрес запроса.
     * @param array $params Параметры запроса.
     * 
     * @return array
     * 
     */
    private static function getResponse($requestUrl, $params = [])
    {
        $url = self::$travelineApiURL . $requestUrl;
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
            "Content-Type: application/json"
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers,
        ));
        $response = curl_exec($ch);

        return json_decode($response, true);
    }

    public static function getImages($arImagesUrl)
    {
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
    public static function updateFood()
    {
        $url = self::$travelineApiURL . '/content/v1/meal-plans';
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
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
        foreach ($arFood as $arItem) {
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

            if (!$arEntity) {
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
    public static function verifyReservation($externalSectionId, $externalElementId, $externalCategoryId, $guests, $arChildrenAge, $dateFrom, $dateTo, $price, $checksum, $arGuestList, $arUser, $adults)
    {
        // Получение объекта номера для запроса бронирования
        $url = self::$travelineApiURL . '/search/v1/properties/' . $externalSectionId . '/room-stays/';
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "adults" => $adults,
            "arrivalDate" => date('Y-m-d', strtotime($dateFrom)),
            "departureDate" => date('Y-m-d', strtotime($dateTo)),
        );
        if (isset($arChildrenAge) && count($arChildrenAge) > 0) {
            $data["childAges"] = $arChildrenAge;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers
        ));
        $response = curl_exec($ch);
        $arItemsResponse = json_decode($response, true);

        Debug::writeToFile($arItemsResponse, 'TRAVELINE SEARCH RESPONSE VERIFY ' . date('Y-m-d H:i:s'), '__bx_log.log');

        curl_close($ch);
        if ($arItemsResponse['roomStays']) {
            $arExternalData = array();
            foreach ($arItemsResponse['roomStays'] as $key => $arItem) {
                if ($arItem['ratePlan']['id'] == $externalElementId && $arItem['roomType']['id'] == $externalCategoryId && $checksum == $arItem["checksum"]) {
                    $arExternalData = $arItem;
                    break;
                }
            }            

            $arConditionsError['warnings'][0]['code'] = 'ConditionsChanged';
            if ($arExternalData) {
                // Проверка на актуальность данных
                if ($arExternalData["checksum"] != $checksum) {
                    Debug::writeToFile('Не та чексумма', 'TRAVELINE VERIFY ERROR ' . date('Y-m-d H:i:s'), '__bx_log.log');
                    return $arConditionsError;
                }
                if ($arExternalData['total']['priceBeforeTax'] != (int)$price) {
                    Debug::writeToFile('Не совпадает цена', 'TRAVELINE VERIFY ERROR ' . date('Y-m-d H:i:s'), '__bx_log.log');
                    return $arConditionsError;
                }

                // Запрос на возможность бронирования номера
                $url = self::$travelineApiURL . '/reservation/v1/bookings/verify';
                $headers = array(
                    "X-API-KEY: " . self::$travelineApiKey,
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
                                        "phoneNumber" => "+" . preg_replace('![^0-9]+!', '', $arUser["PERSONAL_PHONE"])
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

                Debug::writeToFile($data, 'TRAVELINE DATA BEFORE BOOKING VERIFY ' . date('Y-m-d H:i:s'), '__bx_log.log');
                
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

                Debug::writeToFile($arVerifyResponse, 'TRAVELINE RESPONSE AFTER BOOKING VERIFY ' . date('Y-m-d H:i:s'), '__bx_log.log');
                
                $arResponse = $arVerifyResponse;
            } else {
                Debug::writeToFile('Что-то не то с ответом по апи', 'TRAVELINE VERIFY ERROR ' . date('Y-m-d H:i:s'), '__bx_log.log');
                return $arConditionsError;
            }
        } else {
            $arResponse = $arItemsResponse;
        }

        return $arResponse;
    }

    /* Получаем чексумму из сессии, которая хранится в БД */
    private static function getChecksum($sessionId)
    {
        if (!$sessionId) {
            return '';
        }

        $session = UserSessionTable::getRow([
            'select' => ['*'],
            'filter' => ['=SESSION_ID' => $sessionId],
        ]);

        $parsedSessionData = CustomFunctions::unserialize_php(base64_decode($session['SESSION_DATA']));

        if (!is_array($parsedSessionData) && !isset($parsedSessionData['traveline_checksum'])) {
            return '';
        }

        return $parsedSessionData['traveline_checksum'];
    }

    /* Бронирование объекта из заказа */
    public static function makeReservation($orderId, $arOrder, $arUser, $reservationPropId)
    {
        $externalSectionId = $arOrder['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_ID'];
        $externalElementId = $arOrder['ITEMS'][0]['ITEM']['PROPERTIES']['EXTERNAL_ID']['VALUE'];
        $externalCategoryId = $arOrder['ITEMS'][0]['ITEM']['PROPERTIES']['EXTERNAL_CATEGORY_ID']['VALUE'];
        $dateFrom = $arOrder['PROPS']['DATE_FROM'];
        $dateTo = $arOrder['PROPS']['DATE_TO'];
        $guests = $arOrder['PROPS']['GUESTS_COUNT'];
        $arChildrenAge = $arOrder["ITEMS"][0]["ITEM_BAKET_PROPS"]["CHILDREN"]['VALUE'] ? explode(',', $arOrder["ITEMS"][0]["ITEM_BAKET_PROPS"]["CHILDREN"]['VALUE']) : [];
        $price = $arOrder['FIELDS']['BASE_PRICE'];
        $checksum = self::getChecksum($arOrder["ITEMS"][0]["ITEM_BAKET_PROPS"]["SESSION_ID"]['VALUE']);
        $arGuestList = $arOrder['PROPS']['GUEST_LIST'];
        $adults = $arOrder["ITEMS"][0]["ITEM_BAKET_PROPS"]["GUESTS_COUNT"]['VALUE'];

        $arVerifyResponse = self::verifyReservation($externalSectionId, $externalElementId, $externalCategoryId, $guests, $arChildrenAge, $dateFrom, $dateTo, $price, $checksum, $arGuestList, $arUser, $adults);
        if ($arVerifyResponse["booking"]) {
            if ($arVerifyResponse["booking"]['roomStays']) {
                foreach ($arVerifyResponse["booking"]['roomStays'] as $key => &$arItem) {
                    $arItem['services'] = [];
                }
            }

            $url = self::$travelineApiURL . '/reservation/v1/bookings';
            $headers = array(
                "X-API-KEY: " . self::$travelineApiKey,
                "Content-Type: application/json"
            );
            $data = array(
                "booking" => $arVerifyResponse["booking"]
            );

            Debug::writeToFile($data, 'TRAVELINE DATA BEFORE BOOKING ' . date('Y-m-d H:i:s'), '__bx_log.log');            

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
            
            Debug::writeToFile($arResponse, 'TRAVELINE DATA AFTER BOOKING ' . date('Y-m-d H:i:s'), '__bx_log.log');
            
            if ($arResponse['booking']['status'] == "Confirmed" && $arResponse['booking']['number']) {
                // Сохраняем ID бронирования в заказе
                $reservationId = $arResponse['booking']['number'];

                $order = Order::load($orderId);
                $propertyCollection = $order->getPropertyCollection();
                $propertyValue = $propertyCollection->getItemByOrderPropertyId($reservationPropId);
                $propertyValue->setValue($reservationId);
                $res = $order->save();

                if ($res->isSuccess()) {
                    return $reservationId;
                } else {
                    Debug::writeToFile('Не записался ID бронирования', 'TRAVELINE BOOKING ERROR ' . date('Y-m-d H:i:s'), '__bx_log.log');
                    return [
                        "ERROR" => "Ошибка сохранения ID бронирования."
                    ];
                }
            } else {
                $errorText = $arResponse['warnings'][0]['code'] ?? $arResponse['errors'][0]['message'];
                Debug::writeToFile("Ошибка запроса бронирования. " . $errorText, 'TRAVELINE BOOKING ERROR ' . date('Y-m-d H:i:s'), '__bx_log.log');
                return [
                    "ERROR" => "Ошибка запроса бронирования. " . $errorText
                ];
            }
        } else {
            $errorText = $arVerifyResponse['warnings'][0]['code'] ?? $arVerifyResponse['errors'][0]['message'];
            Debug::writeToFile('Что-то не так с верификацией', 'TRAVELINE BOOKING ERROR ' . date('Y-m-d H:i:s'), '__bx_log.log');
            return [
                "ERROR" => "Ошибка верификации бронирования. " . $errorText
            ];
        }
    }

    /* Отмена бронирования объекта из заказа - перед отменой  */
    public static function beforeCancelReservation($arOrder)
    {
        $reservationId = $arOrder['PROPS']['RESERVATION_ID'];
        //file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log.txt', $reservationId . PHP_EOL, FILE_APPEND);

        $url = self::$travelineApiURL . '/reservation/v1/bookings/' . $reservationId . '/calculate-cancellation-penalty';
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "cancellationDateTimeUtc" => date('Y-m-d\TH:i:s\Z')
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url . '?' . http_build_query($data),
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
    public static function cancelReservation($arOrder)
    {
        $reservationId = $arOrder['PROPS']['RESERVATION_ID'];

        $url = self::$travelineApiURL . '/reservation/v1/bookings/' . $reservationId . '/cancel';
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
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

        if ($arResponse['booking']['cancellation']) {
            return true;
        } else {
            $errorText = $arResponse['errors'][0]['message'];
            return [
                "ERROR" => "Ошибка запроса отмены бронирования во внешнем сервисе. " . self::$arErrors[$errorText]
            ];
        }
    }

    /* Установка минимальных цен для объектов Traveline */
    public static function setMinPrices()
    {
        $rsSections = CIBlockSection::GetList(array(), array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "!UF_EXTERNAL_ID" => false, "UF_EXTERNAL_SERVICE" => self::$travelineSectionPropEnumId), false, array("ID", "UF_EXTERNAL_ID"), false);
        $arSectionExternalIDs = array();
        while ($arSection = $rsSections->Fetch()) {
            $arSectionExternalIDs[] = (string)$arSection["UF_EXTERNAL_ID"];
        }

        $url = self::$travelineApiURL . '/search/v1/properties/room-stays/search';
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
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
        foreach ($arResponse["roomStays"] as $arItem) {
            if (empty($arMinPrices[$arItem["propertyId"]]) || $arItem["total"]["priceBeforeTax"] < $arMinPrices[$arItem["propertyId"]])
                $arMinPrices[$arItem["propertyId"]] = $arItem["total"]["priceBeforeTax"];
        }

        $iS = new CIBlockSection();
        foreach ($arMinPrices as $externalSectionId => $price) {
            $arExistSection = CIBlockSection::GetList(array(), array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "UF_EXTERNAL_ID" => $externalSectionId))->Fetch();
            if ($arExistSection) {
                $sectionId = $arExistSection["ID"];
                $iS->Update($sectionId, array(
                    "UF_MIN_PRICE" => $price,
                ));
            }
        }
    }

    /* Получение штрафа за отмену */
    public static function getCancellationAmount($externalId, $guests, $arChildrenAge, $dateFrom, $dateTo, $checksum, $externalElementId, $externalCategoryId)
    {
        $url = self::$travelineApiURL . '/search/v1/properties/' . $externalId . '/room-stays/';
        $headers = array(
            "X-API-KEY: " . self::$travelineApiKey,
            "Content-Type: application/json"
        );
        $data = array(
            "adults" => $guests,
            "arrivalDate" => date('Y-m-d', strtotime($dateFrom)),
            "departureDate" => date('Y-m-d', strtotime($dateTo)),
        );
        if (isset($arChildrenAge) && count($arChildrenAge) > 0) {
            $data["childAges"] = $arChildrenAge;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);

        date_default_timezone_set('UTC');
        foreach ($arResponse["roomStays"] as $arItem) {
            if ($externalElementId == $arItem['ratePlan']['id'] && $externalCategoryId == $arItem['roomType']['id'] && $checksum == $arItem['checksum']) {
                return [
                    'possible' => $arItem['cancellationPolicy']['freeCancellationPossible'],
                    'date' => date('d.m.Y H:i', strtotime($arItem['cancellationPolicy']['freeCancellationDeadlineUtc']) + 10800),
                    'cancelAmount' => $arItem['cancellationPolicy']['penaltyAmount'] ?? 0
                ];
            }
        }

        return false;
    }

    /* Объект HL */
    private static function getEntityClass($hlId)
    {
        Loader::IncludeModule('highloadblock');
        $hlblock = HighloadBlockTable::getById($hlId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}
