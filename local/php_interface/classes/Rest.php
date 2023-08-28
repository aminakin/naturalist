<?

namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use CIBlockSection;
use CIBlockElement;

Loader::IncludeModule("iblock");
Loader::IncludeModule("catalog");

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

class Rest
{
    private $catalogIBlockID     = CATALOG_IBLOCK_ID;
    private $roomTypesIBlockID   = CATEGORIES_IBLOCK_ID;
    private $tariffsIBlockID     = TARIFFS_IBLOCK_ID;
    private $occupanciesIBlockID = OCCUPANCIES_IBLOCK_ID;

    private $bnovoSectionPropEnumId = '2';

    // HL ID возраста
    private static $childrenAgesHLId = CHILDREN_HL_ID;

    public $clientsIBlockID = 12;
    public $tokensIBlockID  = 13;

    private $authSalt = "cqDC7cnhD";
    private $refreshSalt = "ISB1QyPGP";

    public function getToken($clientId, $clientSecret) {
        if(!$clientId || !$clientSecret) {
            return array('code' => 401, 'error' => 'client_id и/или client_secret не были переданы.');
        }

        $arClient = CIBlockElement::GetList(
            false,
            array(
                "IBLOCK_ID" => $this->clientsIBlockID,
                "ACTIVE"    => "Y",
                "PROPERTY_CLIENT_ID"     => $clientId,
                "PROPERTY_CLIENT_SECRET" => $clientSecret,
            ),
            false,
            false,
            array(
                "IBLOCK_ID",
                "ID",
                "NAME",
                "PROPERTY_CLIENT_ID"
            )
        )->Fetch();

        if($arClient) {
            $arToken = CIBlockElement::GetList(
                false,
                array(
                    "IBLOCK_ID" => $this->tokensIBlockID,
                    "ACTIVE"    => "Y",
                    "PROPERTY_CLIENT_ID" => $clientId
                ),
                false,
                false,
                array(
                    "IBLOCK_ID",
                    "ID",
                    "NAME",
                    "PROPERTY_CLIENT_ID",
                    "PROPERTY_AUTH_TOKEN",
                    "PROPERTY_IP",
                    "PROPERTY_LAST_REQUEST",
                )
            )->Fetch();

            $authToken = $this->generateToken($this->authSalt);
            $refreshToken = $this->generateToken($this->refreshSalt);

            if($arToken) {
                $arProps = array(
                    "AUTH_TOKEN"    => $authToken,
                    "REFRESH_TOKEN" => $refreshToken,
                    "IP"            => $this->getIP(),
                    "LAST_REQUEST"  => date("d.m.Y H:i:s")
                );
                CIBlockElement::SetPropertyValuesEx($arToken["ID"], $this->tokensIBlockID, $arProps);

            } else {
                $el = new CIBlockElement();
                $el->Add(array(
                    "IBLOCK_ID" => $this->tokensIBlockID,
                    "ACTIVE"    => "Y",
                    "NAME"      => $arClient["NAME"],
                    "PROPERTY_VALUES"   => array(
                        "CLIENT_ID"     => $clientId,
                        "AUTH_TOKEN"    => $authToken,
                        "REFRESH_TOKEN" => $refreshToken,
                        "IP"            => $this->getIP(),
                        "LAST_REQUEST"  => date("d.m.Y H:i:s")
                    )
                ));
            }

            return [
                "access_token"  => $authToken,
                //"refresh_token" => $refreshToken
            ];
        } else {
            return array('code' => 401, 'error' => 'Клиента с такими client_id и client_secret не существует.');
        }
    }
    public function login($token = null) {
        if($token) {
            $arToken = CIBlockElement::GetList(
                false,
                array(
                    "IBLOCK_ID" => $this->tokensIBlockID,
                    "ACTIVE"    => "Y",
                    "PROPERTY_AUTH_TOKEN" => trim($token)
                ),
                false,
                false,
                array(
                    "IBLOCK_ID",
                    "ID",
                    "NAME",
                    "PROPERTY_CLIENT_ID",
                    "PROPERTY_AUTH_TOKEN",
                    "PROPERTY_IP",
                    "PROPERTY_LAST_REQUEST",
                )
            )->Fetch();

            // Проверяем, есть ли такой токен в ИБ и, если есть, то записываем в соответствующий элемент IP и LAST_REQUEST
            if($arToken) {
                $arProps = array(
                    "IP"           => $this->getIP(),
                    "LAST_REQUEST" => date("d.m.Y H:i:s")
                );
                CIBlockElement::SetPropertyValuesEx($arToken["ID"], $this->tokensIBlockID, $arProps);

                return true;
            } else {
                return array('code' => 401, 'error' => 'Передан неверный токен.');
            }

        } else {
            return array('code' => 401, 'error' => 'Передан пустой токен.');
        }
    }
    public function refreshToken($refreshToken) {
        $arRefreshToken = CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => $this->tokensIBlockID,
                "ACTIVE"    => "Y",
                "PROPERTY_REFRESH_TOKEN" => trim($refreshToken)
            ),
            false,
            false,
            array(
                "IBLOCK_ID",
                "ID",
                "NAME",
                "PROPERTY_CLIENT_ID",
                "PROPERTY_AUTH_TOKEN",
                "PROPERTY_REFRESH_TOKEN",
                "PROPERTY_IP",
                "PROPERTY_LAST_REQUEST",
            )
        )->Fetch();

        if($arRefreshToken) {
            $authToken = $this->generateToken($this->authSalt);

            $arProps = array(
                "AUTH_TOKEN"   => $authToken,
                "IP"           => $this->getIP(),
                "LAST_REQUEST" => date("d.m.Y H:i:s")
            );
            CIBlockElement::SetPropertyValuesEx($arRefreshToken["ID"], $this->tokensIBlockID, $arProps);

            return $authToken;

        } else {
            return array('code' => 401, 'error' => 'Передан неверный токен.');
        }
    }
    private function checkToken($token) {
        $arToken = CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => $this->tokensIBlockID,
                "ACTIVE"    => "Y",
                "PROPERTY_AUTH_TOKEN" => trim($token)
            ),
            false,
            false,
            array(
                "IBLOCK_ID",
                "ID",
                "NAME",
                "PROPERTY_CLIENT_ID",
                "PROPERTY_AUTH_TOKEN",
                "PROPERTY_IP",
                "PROPERTY_LAST_REQUEST",
            )
        )->Fetch();

        if($arToken) {
            if(strtotime($arToken["PROPERTY_LAST_REQUEST_VALUE"]) + 60*60*24 > time()) {
                $arProps = array(
                    "LAST_REQUEST" => date("d.m.Y H:i:s")
                );
                CIBlockElement::SetPropertyValuesEx($arToken["ID"], $this->tokensIBlockID, $arProps);

                return true;

            } else {
                return array('code' => 401, 'description' => 'Время токена истекло.');
            }

        } else {
            return array('code' => 401, 'description' => 'Передан неверный токен.');
        }
    }

    private function generateToken($salt) {
        $token = md5(microtime() . $salt . time());
        return $token;
    }
    private function getIP() {
        if(!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }

        return $ip;
    }

    /* Получение каталога */
    public function getCatalog($hotelId) {
        /*
        {
            "hotel_id": 1,
            "hotel_title": "Test Hotel",
            "rooms": {
                "1": {
                    "title": "Стандарт",
                    "occupancies": {
                        "1_a.1": "1 взросл.",
                        "1_a.2": "2 взросл."
                    }
                },
                "2": {
                    "title": "Люкс",
                    "occupancies": {
                        "2_a.1": "1 взросл.",
                        "2_a.2": "2 взросл.",
                        "2_a.2_e.1": "2 взросл. + 1 взрослый на доп. месте",
                        "2_a.2_x.1.163.1": "2 взросл. + 1 ребенок (3-10 лет) с местом",
                        "2_a.2_x.1.163.1_m.2": "2 взросл. + 1 ребенок (3-10 лет) с местом, с макс. кол-вом доп. мест равным 2"
                    }
                },
                ...
            },
            "plans": {
                "1": {
                    "title": "BAR",
                    "permitted_data": "all",
                },
                "2": {
                    "title": "Breakfast included",
                    "permitted_data": "all",
                },
                ...
            },
            "ages": {
                "163": {
                    "min": "3",
                    "max": "10"
                },
                ...
            }
        },
        ...
        */

        if(!$hotelId) {
            return array('code' => 404, 'error' => 'Параметр hotel_id не был передан.');
        }

        // Отели
        $rsSections = CIBlockSection::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->catalogIBlockID,
                //"ACTIVE"    => "Y",
                "ID"        => $hotelId,
                "UF_EXTERNAL_SERVICE" => $this->bnovoSectionPropEnumId,
            ),
            false,
            array("IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"),
            false
        );
        $arSectionIDs = array();
        $arSections = array();
        while($arSection = $rsSections->Fetch()) {
            $arSectionIDs[] = $arSection["ID"];
            $arSectionExternalIds[] = $arSection["UF_EXTERNAL_ID"];
            $arSections[] = array(
                "hotel_id" => $arSection["ID"],
                "hotel_title" => $arSection["NAME"],
                "rooms" => [],
                "plans" => []
            );
        }

        if(empty($arSections)) {
            return array('code' => 404, 'error' => 'Отель с ID '.$hotelId.' не найден.');
        }

        // Тарифы
        $rsTariffs = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->tariffsIBlockID,
                "ACTIVE" => "Y",
            ),
            false,
            false,
            array("ID", "NAME")
        );
        $arTariffsAll = array();
        while($arTariff = $rsTariffs->Fetch()) {
            $arTariffsAll[$arTariff["ID"]] = $arTariff["NAME"];
        }

        // Номера
        $rsElements = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->catalogIBlockID,
                "IBLOCK_SECTION_ID" => $arSectionIDs,
                "ACTIVE" => "Y"
            ),
            false,
            false,
            array("IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME", "PROPERTY_CATEGORY", "PROPERTY_TARIFF", "PROPERTY_EXTERNAL_ID", "PROPERTY_PARENT_ID")
        );
        $arRooms = array();
        $arRoomsTariffs = array();
        $arRoomsChildrens = array();
        $arRoomsParentIdsBx = array();
        while($arElement = $rsElements->Fetch()) {
            $arRooms[$arElement["ID"]] = $arElement;
            if((int)$arElement["PROPERTY_PARENT_ID_VALUE"] > 0){
                $arRoomsChildrens[$arElement["PROPERTY_CATEGORY_VALUE"][0]] = $arElement["PROPERTY_PARENT_ID_VALUE"];
            } else {
                $arRoomsTariffs[$arElement["ID"]] = $arElement;
                $arRoomsParentIdsBx[$arElement["PROPERTY_EXTERNAL_ID_VALUE"]] = $arElement["PROPERTY_CATEGORY_VALUE"][0];
            }
        }

        foreach ($arRoomsChildrens as $id => $arChildren) {
            $arChildrenIdsBx[$id] = $arRoomsParentIdsBx[$arChildren];
        }

        // Размещение номеров
        $arSectionOccupancies = CIBlockSection::GetList(
            false,
            array(
                "IBLOCK_ID" => OCCUPANCIES_IBLOCK_ID,
                "ACTIVE" => "Y",
                "UF_EXTERNAL_ID" => $arSectionExternalIds
            ),
            false,
            array("IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"),
            false
        )->Fetch();
        $sectionIdOccupancies = $arSectionOccupancies['ID'];

        // HL Возрастные интервалы
        $arAgesAll = array();
        $childrenAgesEntityClass   = self::getEntityClass(self::$childrenAgesHLId);

        $rsData = $childrenAgesEntityClass::getList([
            "select" => ["*"],
            "filter" => [
                "UF_HOTEL_ID" => $hotelId
            ]
        ]);

        while ($arEntity = $rsData->Fetch()){
            $arAgesAll[$arEntity['ID']] = [
                "min" => $arEntity["UF_MIN_AGE"],
                "max" => $arEntity["UF_MAX_AGE"]
            ];
        }

        $rsOccupancies = CIBlockElement::GetList(
            false,
            array(
                "IBLOCK_ID" => $this->occupanciesIBlockID,
                "ACTIVE" => "Y",
                "IBLOCK_SECTION_ID" => $sectionIdOccupancies,
            ),
            false,
            false,
            array("ID", "NAME", "CODE", "PROPERTY_CATEGORY_ID", "PROPERTY_CHILDREN_MIN_AGE", "PROPERTY_CHILDREN_MAX_AGE")
        );
        $arOccupanciesAll = array();

        while($arOccupancy = $rsOccupancies->Fetch()) {
            /*if(!empty($arOccupancy["PROPERTY_CHILDREN_MIN_AGE_VALUE"]) && !empty($arOccupancy["PROPERTY_CHILDREN_MAX_AGE_VALUE"])) {
                $arAgesAll[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]] = array(
                    "min" => $arOccupancy["PROPERTY_CHILDREN_MIN_AGE_VALUE"],
                    "max" => $arOccupancy["PROPERTY_CHILDREN_MAX_AGE_VALUE"]
                );
            }*/

            if(isset($arChildrenIdsBx[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]])){
                $arOccupancy["PROPERTY_CATEGORY_ID_VALUE"] = $arChildrenIdsBx[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]];
            }
            $arOccupanciesAll[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]][$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]."_".$arOccupancy["CODE"]] = $arOccupancy["NAME"];
        }

        // Категории номеров
        $rsRoomTypes = CIBlockElement::GetList(
            false,
            array(
                "IBLOCK_ID" => $this->roomTypesIBlockID,
                "ACTIVE" => "Y"
            ),
            false,
            false,
            array("ID", "NAME")
        );
        $arRoomTypesAll = array();
        while($arRoomType = $rsRoomTypes->Fetch()) {
            if(!empty($arOccupanciesAll[$arRoomType["ID"]])){
                $arRoomTypesAll[$arRoomType["ID"]] = array(
                    "title" => $arRoomType["NAME"],
                    "occupancies" => $arOccupanciesAll[$arRoomType["ID"]] ?? []
                );
            }
        }

        // Номера
        $arElementIDs = array();
        $arElementsSectionLinks = array();

        $arTariffs = array();
        $arRoomTypes = array();
        $arAges = array();
        //while($arElement = $rsElements->Fetch()) {
        foreach ($arRoomsTariffs as $arElement) {
            $arElementIDs[] = $arElement["ID"];
            $arElementsSectionLinks[$arElement["ID"]] = $arElement["IBLOCK_SECTION_ID"];

            // Тарифы
            //xprint($arTariffsAll);
            //xprint($arTariffs);
            //xprint($arElement["PROPERTY_TARIFF_VALUE"]);

            //die();
            foreach ($arElement["PROPERTY_TARIFF_VALUE"] as $tariffId){
                if(!isset($arTariffs[$arElement["IBLOCK_SECTION_ID"]][$tariffId]) && $tariffId) {
                    $arTariffs[$arElement["IBLOCK_SECTION_ID"]][$tariffId] = array(
                        'title' => $arTariffsAll[$tariffId],
                        'permitted_data' => 'all',
                        'rooms' => array(),
                    );
                }

                if($arTariffs[$arElement["IBLOCK_SECTION_ID"]][$tariffId]) {
                    if(!empty($arElement["PROPERTY_CATEGORY_VALUE"][0])){
                        $arTariffs[$arElement["IBLOCK_SECTION_ID"]][$tariffId]["rooms"][] = $arElement["PROPERTY_CATEGORY_VALUE"][0];
                    }
                }
            }

            // Категории
            foreach($arElement["PROPERTY_CATEGORY_VALUE"] as $categoryId) {
                if((empty($arRoomTypes[$arElement["IBLOCK_SECTION_ID"]]) || !in_array($categoryId, $arRoomTypes[$arElement["IBLOCK_SECTION_ID"]])) && !empty($arRoomTypesAll[$categoryId])) {
                    $arRoomTypes[$arElement["IBLOCK_SECTION_ID"]][$categoryId] = $arRoomTypesAll[$categoryId];
                }
            }
        }

        foreach($arSections as &$arSection) {
            $arSection['rooms'] = $arRoomTypes[$arSection["hotel_id"]];
            $arSection['plans'] = $arTariffs[$arSection["hotel_id"]];
            $arSection['ages']  = $arAgesAll;
        }

        return $arSections[0];
    }

    /* Изменение каталога */
    public function updateCatalog($params) {
        /*
        {
            "hotel_id": "12345", //id отеля в OTA
            "account_id": "123", //id аккаунта в CM "Bnovo"
            //сопоставления категорий
            //1,2 - id категорий в CM "Bnovo", 11,12 - id категорий в OTA
            "roomtypes": {
                "1":["11"],
                "2":["12"]
            },
            //сопоставления размещений
            //1,2,3 - id категорий в CM "Bnovo", 11_1,11_2,12_1,12_2 - id размещений в OTA
            "occupancies": {
                "1":["11_1", "11_2"],
                "2":["12_1"],
                "3":["12_2"],
            },
            //сопоставления тарифов
            //1,2 - id тарифов в CM "Bnovo", 31,32,33 - id тарифов в OTA
            "rates": {
                "1": ["31"],
                "2": ["33"]
            }
        }
        */
        $hotelId    = $params["hotel_id"];
        $externalId = $params["account_id"];

        $arSectionHotel = CIBlockSection::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->catalogIBlockID,
                //"ACTIVE"    => "Y",
                "ID"        => $hotelId,
                "UF_EXTERNAL_SERVICE" => $this->bnovoSectionPropEnumId,
            ),
            false,
            array("IBLOCK_ID", "ID"),
            false
        )->Fetch();

        if(empty($arSectionHotel["ID"])) {
            return array('code' => 404, 'error' => 'Объект не найден');
        }

        $iS = new CIBlockSection();
        $iS->Update($hotelId, array(
            "UF_EXTERNAL_ID" => $externalId
        ));

        // Тарифы
        $arRates = $params["rates"];
        foreach($arRates as $externalRateId => $arRate) {
            $arExistElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $this->tariffsIBlockID, "ID" => $arRate[0]))->Fetch();
            if($arExistElement) {
                $elementId = $arExistElement["ID"];
                CIBlockElement::SetPropertyValuesEx($elementId, $this->tariffsIBlockID, array(
                    "EXTERNAL_ID" => $externalRateId
                ));
            }
        }

        // Категории
        $arRoomTypes = $params["roomtypes"];
        foreach($arRoomTypes as $externalRoomTypeId => $arRoomType) {
            $arExistElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $this->roomTypesIBlockID, "ID" => $arRoomType[0]))->Fetch();
            if($arExistElement) {
                $elementId = $arExistElement["ID"];
                CIBlockElement::SetPropertyValuesEx($elementId, $this->roomTypesIBlockID, array(
                    "EXTERNAL_ID" => $externalRoomTypeId
                ));
            }
        }

        // Размещение номеров
        $arOccupanciesAll = $params["occupancies"];
        foreach($arOccupanciesAll as $externalCategoryId => $arOccupanciesIDs) {
            $arExistElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $this->roomTypesIBlockID, "PROPERTY_EXTERNAL_ID" => $externalCategoryId))->Fetch();
            if($arExistElement) {
                $elementId = $arExistElement["ID"];
                foreach($arOccupanciesIDs as $occupancyId) {
                    CIBlockElement::SetPropertyValuesEx($occupancyId, $this->occupanciesIBlockID, array(
                        "CATEGORY_ID" => $occupancyId
                    ));
                }
            }
        }

        return array('code' => 200, 'message' => 'Ok');
    }

    /* Изменение цен, наличия, ограничений */
    public function updatePrices($params) {
        /*
        {
            "hotel_id" : 1, //id отеля в OTA
            "account_id": 123, //id аккаунта в CM "Bnovo"
            "data": {
                // rooms - уведомление об изменении наличия
                // 1 - id категории в CM "Bnovo"
                "rooms":{
                    "1":[
                        "2019-01-27"
                        "2019-01-30",
                        "2019-01-29",
                        "2019-01-28"
                    ]
                },
                // prices - уведомление об изменении цен/ограничений
                // 2 - id тарифа в CM "Bnovo"
                // 1 - id категории в CM "Bnovo"
                "prices":{
                    "2":{
                        "1":[
                            "2019-01-27",
                            "2019-01-30",
                            "2019-01-29",
                            "2019-01-28"
                        ]
                    }
                }
            }
        }
        */
        $hotelId = $params["hotel_id"];
        $externalId = $params["account_id"];

        $arSectionHotel = CIBlockSection::GetList(
            false,
            array(
                "IBLOCK_ID" => $this->catalogIBlockID,
                //"ACTIVE"    => "Y",
                "ID"        => $hotelId,
                "UF_EXTERNAL_SERVICE" => $this->bnovoSectionPropEnumId,
            ),
            false,
            array("IBLOCK_ID", "ID"),
            false
        )->Fetch();

        if(empty($arSectionHotel["ID"])) {
            return array('code' => 404, 'error' => 'Объект не найден');
        }

        $bnovo = new Bnovo();

        $arPrices = $params["data"]["prices"];
        if($arPrices) {
            foreach($arPrices as $tariffId => $arCategories) {
                foreach($arCategories as $categoryId => $arCategoryDates) {
                    $bnovo->updateReservationData($externalId, $tariffId, $categoryId, $arCategoryDates);
                }
            }
        }

        $arRooms = $params["data"]["rooms"];
        if($arRooms) {
            foreach($arRooms as $categoryId => $arCategoryDates) {
                $bnovo->updateAvailabilityData($externalId, $categoryId, $arCategoryDates);
            }
        }

        if(!$arPrices && !$arRooms) {
            if(!$arPrices) {
                return array('code' => 404, 'error' => 'Параметр prices не был передан.');

            } else {
                return array('code' => 404, 'error' => 'Параметр rooms не был передан.');
            }

        } else {
            return array('code' => 200, 'message' => 'Ok');
        }
    }

    private function getEntityClass($hlId = 11)
    {
        Loader::IncludeModule('highloadblock');
        $hlblock = HighloadBlockTable::getById($hlId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}