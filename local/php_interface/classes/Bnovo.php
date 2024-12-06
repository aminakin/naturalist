<?

namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Sale\Order;
use CIBlockSection;
use CIBlockElement;
use DatePeriod;
use DateTime;
use DateInterval;
use http\Params;
use Naturalist\Products;
use Bitrix\Main\Grid\Declension;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;

Loader::IncludeModule("iblock");
Loader::IncludeModule("catalog");

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
class Bnovo
{
    private $hlDataMonthCount = 6;
    private $catalogIBlockID = CATALOG_IBLOCK_ID;
    private $tariffsIBlockID = TARIFFS_IBLOCK_ID;
    private $roomTypesIBlockID = CATEGORIES_IBLOCK_ID;
    private $occupanciesIBlockID = OCCUPANCIES_IBLOCK_ID;
    // HL ID Особенности номера
    private static $roomsFeaturesHLId = 8;
    // HL ID возраста
    private static $childrenAgesHLId = CHILDREN_HL_ID;

    private static $pricesHlCode = 'RoomOffers';
    private $bnovoSectionPropEnumId = '2';
    private $sendEventName = 'BNOVO_IMPORT_NOTICE';

    /*
    private $bnovoApiURL = 'https://api.sandbox.reservationsteps.ru/v1/api';
    private $bnovoApiUser = 'naturalist-tech@yandex.ru';
    private $bnovoApiPassword = 'oj52t5j9Nv9h0sqJ2db6TNEjniiosQW3';
    */

    private $bnovoApiURL = 'https://api.reservationsteps.ru/v1/api';
    private $bnovoApiPublicURL = 'https://public-api.reservationsteps.ru/v1/api';
    private $bnovoApiUser = 'lubov@naturalist.travel';
    private $bnovoApiPassword = 'hDlKqnWeTUZtfG7Hr1nlGebIqH0vePnx';

    private $token;

    public function __construct()
    {
        $url = $this->bnovoApiURL . '/auth';
        $headers = array(
            "Content-Type: application/json"
        );
        $data = array(
            "username" => $this->bnovoApiUser,
            "password" => $this->bnovoApiPassword
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($data)
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);

        $token = $arResponse['token'];
        $this->token = $token;
    }

    /* Получение списка свободных объектов в выбранный промежуток */
    public function search($guests, $arChildrenAge, $dateFrom, $dateTo, $sectionIds = [])
    {
        $arDates = array();
        $period = new DatePeriod(
            new DateTime($dateFrom),
            new DateInterval('P1D'),
            new DateTime(date('d.m.Y', strtotime($dateTo . '+1 day')))
        );
        foreach ($period as $value) {
            $arDates[] = $value->format('d.m.Y');
        }
        $daysCount = count($arDates) - 1;

        //Проверка на детей без мест и размещения без детей
        $arHotel = CIBlockSection::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID), false, array("ID", "UF_EXTERNAL_ID", "UF_MIN_AGE", "UF_NO_CHILDREN_PLACE"), false);

        while ($hotelRes = $arHotel->Fetch()) {
            if (!empty($hotelRes['UF_MIN_AGE'])) {
                $arChildrenAges = $arChildrenAge;
                foreach ($arChildrenAges as $key => $age) {
                    if ($age <= $hotelRes['UF_MIN_AGE']) {
                        unset($arChildrenAge[$key]);
                    }
                }
            }
            if ($hotelRes['UF_NO_CHILDREN_PLACE'] == 1) {
                $guests += count($arChildrenAge);
                $children = 0;
            } else {
                $children = count($arChildrenAge);
            }
        }

        $entityClass = $this->getEntityClass();
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "filter" => [
                "UF_DATE" => $arDates,
                "UF_RESERVED" => 0,
                "!UF_CLOSED" => "1",
                [
                    "LOGIC" => "OR",
                    ["<=UF_MIN_STAY" => $daysCount],
                    ["=UF_MIN_STAY" => 0]
                ],
                [
                    "LOGIC" => "OR",
                    [">=UF_MAX_STAY" => $daysCount],
                    ["=UF_MAX_STAY" => 0],
                ],
                "=UF_CLOSED_ARRIVAL" => "0",
                "=UF_CLOSED_DEPARTURE" => "0"
            ],
            "order" => ["UF_PRICE" => "ASC"],
        ]);
        $arData = $rsData->FetchAll();

        // Категории номеров
        $rsRoomTypes = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->roomTypesIBlockID,
                "ACTIVE" => "Y",
                "!PROPERTY_EXTERNAL_ID" => false,
            ),
            false,
            false,
            array("ID", "NAME", "PROPERTY_EXTERNAL_ID")
        );
        $arRoomTypesIDs = array();
        while ($arRoomType = $rsRoomTypes->Fetch()) {
            $arRoomTypesIDs[$arRoomType["PROPERTY_EXTERNAL_ID_VALUE"]] = $arRoomType["ID"];
        }

        $arHotelsIDs = array();
        $arCategoriesIDs = array();

        $arDataGrouped = array();
        foreach ($arData as $arItem) {
            $arDataGrouped[$arItem["UF_TARIFF_ID"] . "-" . $arItem["UF_CATEGORY_ID"]][] = $arItem;
        }

        foreach ($arDataGrouped as $key => $arItems) {
            if (count($arItems) < count($arDates)) {
                unset($arDataGrouped[$key]);
            }
        }

        foreach ($arDataGrouped as $arData) {
            foreach ($arData as $arEntity) {
                $categoryId = $arRoomTypesIDs[$arEntity['UF_CATEGORY_ID']];
                if (isset($categoryId) && !in_array($categoryId, $arCategoriesIDs)) {
                    $arCategoriesIDs[] = $categoryId;

                    if (empty($arHotelsIDs[$categoryId][$arEntity['UF_HOTEL_ID']]) || $arEntity['UF_PRICE'] < intval($arHotelsIDs[$categoryId][$arEntity['UF_HOTEL_ID']])) {
                        $arHotelsIDs[$categoryId][$arEntity['UF_HOTEL_ID']] = intval($arEntity['UF_PRICE']);
                    }
                }
            }
        }

        // Размещение номеров
        $rsOccupancies = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->occupanciesIBlockID,
                "ACTIVE" => "Y",
                "PROPERTY_CATEGORY_ID" => $arCategoriesIDs
            ),
            false,
            false,
            array("ID", "NAME", "PROPERTY_CATEGORY_ID", "PROPERTY_GUESTS_COUNT", "PROPERTY_CHILDREN_COUNT", "PROPERTY_CHILDREN_AGES")
        );
        $arCategoriesFilterredIDs = array();

        // HL Возрастные интервалы
        $arAges = self::getAges();
        $backOccupancies = [];
        while ($arOccupancy = $rsOccupancies->Fetch()) {
            $backOccupancies[] = $arOccupancy;
            if ($arOccupancy["PROPERTY_GUESTS_COUNT_VALUE"] >= $guests) {
                if (!empty($children)) {
                    $childrenStatus = false;
                    foreach ($arOccupancy["PROPERTY_CHILDREN_AGES_VALUE"] as $key => $idAge) {
                        foreach ($arChildrenAge as $age) {
                            if ($arAges[$idAge]['UF_MIN_AGE'] <= $age && $arAges[$idAge]['UF_MAX_AGE'] >= $age && $arOccupancy["PROPERTY_CHILDREN_AGES_DESCRIPTION"][$key] == $children) {
                                $childrenStatus = true;
                            }
                        }

                        if ($childrenStatus == true) {
                            break;
                        }
                    }
                } elseif (!empty($arOccupancy["PROPERTY_CHILDREN_AGES_VALUE"])) {
                    //continue;
                }

                if (!isset($childrenStatus) || $childrenStatus != false) {
                    $arCategoriesFilterredIDs[] = $arOccupancy["PROPERTY_CATEGORY_ID_VALUE"];
                }
            }
        }

        if (empty($arCategoriesFilterredIDs)) {
            $guests += count($arChildrenAge);
            foreach ($backOccupancies as $arOccupancy) {
                if ($arOccupancy["PROPERTY_GUESTS_COUNT_VALUE"] >= $guests) {
                    $arCategoriesFilterredIDs[] = $arOccupancy["PROPERTY_CATEGORY_ID_VALUE"];
                }
            }
        }

        $arHotelsIDsOutput = array();

        foreach ($arHotelsIDs as $categoryId => $arHotelData) {
            if (in_array($categoryId, $arCategoriesFilterredIDs)) {
                $idHotel = array_key_first($arHotelData);
                $price = array_shift($arHotelData);
                if (!isset($arHotelsIDsOutput[$idHotel]) || $price < $arHotelsIDsOutput[$idHotel]) {
                    $arHotelsIDsOutput[$idHotel] = $price;
                }
            }
        }

        return $arHotelsIDsOutput ?? [];
    }

    /* Получение списка свободных номеров объекта в выбранный промежуток */
    public function searchRooms($sectionId, $externalId, $guests, $arChildrenAge, $dateFrom, $dateTo)
    {
        // Приравниваются ли дети к взрослым
        $childrenIsAdults = false;

        $error = '';
        $daysDeclension = new Declension('ночь', 'ночи', 'ночей');
        $arDates = array();
        $period = new DatePeriod(
            new DateTime($dateFrom),
            new DateInterval('P1D'),
            new DateTime(date('d.m.Y', strtotime($dateTo . '+1 day')))
        );
        foreach ($period as $value) {
            $arDates[] = $value->format('d.m.Y');
        }
        $daysCount = count($arDates) - 1;
        //Проверка на детей без мест и размещения без детей
        $arHotel = CIBlockSection::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $sectionId), false, array("ID", "UF_EXTERNAL_ID", "UF_MIN_AGE", "UF_NO_CHILDREN_PLACE"), false)->Fetch();
        if (!empty($arHotel['UF_MIN_AGE'])) {
            $arChildrenAges = $arChildrenAge;
            foreach ($arChildrenAges as $key => $age) {
                if ($age <= $arHotel['UF_MIN_AGE']) {
                    unset($arChildrenAge[$key]);
                }
            }
        }

        if ($arHotel['UF_NO_CHILDREN_PLACE'] == 1) {
            $guests += count($arChildrenAge);
            $childrenIsAdults = true;
            $children = 0;
        } else {
            $children = count($arChildrenAge);
        }

        if ($childrenIsAdults) {
            $totalPeople = $guests;
        } else {
            $totalPeople = $guests + count($arChildrenAge);
        }

        // Запрос по выбранным датам в HL
        $entityClass = $this->getEntityClass();
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "filter" => [
                "UF_HOTEL_ID" => $externalId,
                "UF_DATE" => $arDates,
                "!UF_RESERVED" => '1',
                "!UF_CLOSED" => "1",
                // [
                //     "LOGIC" => "OR",
                //     ["<=UF_MIN_STAY" => $daysCount],
                //     ["=UF_MIN_STAY" => 0]
                // ],
                [
                    "LOGIC" => "OR",
                    [">=UF_MAX_STAY" => $daysCount],
                    ["=UF_MAX_STAY" => 0],
                ],
                "=UF_CLOSED_ARRIVAL" => "0",
                "=UF_CLOSED_DEPARTURE" => "0"
            ],
            "order" => ["UF_DATE" => "ASC"],
        ]);
        $arData = $rsData->FetchAll();

        foreach ($arData as $arDataItem) {
            $arTemp[$arDataItem['ID']] = $arDataItem;
        }
        $arData = $arTemp;

        $arDataGrouped = array();
        foreach ($arData as $arItem) {
            $arDataGrouped[$arItem["UF_TARIFF_ID"] . "-" . $arItem["UF_CATEGORY_ID"]][] = $arItem;
        }

        // Удаляем из сгруппированного массива цен размещения с ограничением по минимальному заезду
        foreach ($arDataGrouped as $key => $arItems) {
            foreach ($arItems as $arItem) {
                if (intval($arItem['UF_MIN_STAY_ARRIVAL']) > $daysCount || intval($arItem['UF_MIN_STAY']) > $daysCount) {
                    $minDays = $arItem['UF_MIN_STAY_ARRIVAL'] ? $arItem['UF_MIN_STAY_ARRIVAL'] : $arItem['UF_MIN_STAY'];
                    $error = 'На выбранные даты возможно бронирование минимум на ' . $minDays . ' ' . $daysDeclension->get($minDays);

                    // Удаляем элементы из первоначального массива дат, т.к. он далее будет использоваться для поиска цен                    
                    foreach ($arDataGrouped[$key] as $toDel) {
                        unset($arData[$toDel['ID']]);
                    }
                    unset($arDataGrouped[$key]);
                    break;
                }
            }
        }

        unset($key);
        unset($arItem);
        unset($arItems);

        // Создаём новый массив дат для сравнения по дням с результатами выборки из таблицы цен
        // Отбрасываем последнюю дату, т.к. она не влияет на возможность заселения
        $arDatesToCompare = $arDates;
        array_pop($arDatesToCompare);

        // Сравниваем дату каждого элемента сгруппированного массива с датой по индексу.
        // Если будет хотя бы одно несовпадение, удаляем весь массив
        foreach ($arDataGrouped as $key => $arItems) {
            if (count($arItems) < count($arDatesToCompare)) {
                unset($arDataGrouped[$key]);
                continue;
            }
            if (strpos($key, '_')) {
                unset($arDataGrouped[$key]);
            }

            foreach ($arItems as $keyCurDate => $curDate) {
                if ($keyCurDate == count($arDatesToCompare)) {
                    break;
                }
                if ($curDate['UF_DATE']->format('d.m.Y') != $arDatesToCompare[$keyCurDate]) {
                    unset($arDataGrouped[$key]);
                    break;
                }
            }
        }

        $arDataGroupedValues = array_reduce($arDataGrouped, 'array_merge', array());
        $arExternalTariffIDs = array_unique(array_column($arDataGroupedValues, 'UF_TARIFF_ID'));
        $arExternalCategoryIDs = array_unique(array_column($arDataGroupedValues, 'UF_CATEGORY_ID'));

        // Категории номеров
        $rsRoomTypes = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->roomTypesIBlockID,
                "ACTIVE" => "Y",
                "!PROPERTY_EXTERNAL_ID" => false,
            ),
            false,
            false,
            array("ID", "NAME", "PROPERTY_EXTERNAL_ID")
        );
        $arRoomTypesIDs = array();
        while ($arRoomType = $rsRoomTypes->Fetch()) {
            $arRoomTypesIDs[$arRoomType["PROPERTY_EXTERNAL_ID_VALUE"]] = $arRoomType["ID"];
        }

        $arCategoriesIDs = array();
        foreach ($arExternalCategoryIDs as $externalId) {
            $arCategoriesIDs[] = $arRoomTypesIDs[$externalId];
        }

        // Тарифы
        $rsTariffs = CIBlockElement::GetList(
            false,
            array(
                "IBLOCK_ID" => $this->tariffsIBlockID,
                "ACTIVE" => "Y",
                //"!PROPERTY_EXTERNAL_ID" => false,
                "PROPERTY_EXTERNAL_ID" => $arExternalTariffIDs,
            ),
            false,
            false,
            array("IBLOCK_ID", "ID", "NAME", "PROPERTY_EXTERNAL_ID", "PROPERTY_CANCELLATION_RULES", "PROPERTY_CANCELLATION_DEADLINE", "PROPERTY_CANCELLATION_FINE_TYPE", "PROPERTY_CANCELLATION_FINE_AMOUNT", "PROPERTY_NAME_DETAIL")
        );
        $arTariffsExternalIDs = array();
        while ($arTariff = $rsTariffs->Fetch()) {
            $arTariffsExternalIDs[$arTariff["PROPERTY_EXTERNAL_ID_VALUE"]] = $arTariff["ID"];
            $arTariffsValue[$arTariff["ID"]] = $arTariff;
        }

        $arTariffsIDs = array();
        foreach ($arExternalTariffIDs as $externalId) {
            if (isset($arTariffsExternalIDs[$externalId])) {
                $arTariffsIDs[] = $arTariffsExternalIDs[$externalId];
            }
        }

        // Размещение номеров
        // HL Возрастные интервалы
        $arAges = self::getAges($sectionId);

        $rsOccupancies = CIBlockElement::GetList(
            ["PROPERTY_GUESTS_COUNT" => "ASC"],
            array(
                "IBLOCK_ID" => $this->occupanciesIBlockID,
                "ACTIVE" => "Y",
                "PROPERTY_CATEGORY_ID" => $arCategoriesIDs
            ),
            false,
            false,
            array(
                "ID",
                "NAME",
                "CODE",
                "SORT",
                "PROPERTY_CATEGORY_ID",
                "PROPERTY_GUESTS_COUNT",
                "PROPERTY_CHILDREN_COUNT",
                "PROPERTY_CHILDREN_AGES",
                "PROPERTY_CHILDREN_MIN_AGE",
                "PROPERTY_CHILDREN_MAX_AGE",
                "PROPERTY_MAIN_BEDS",
                "PROPERTY_MARKUP_EXTERNAL_ID",
                "PROPERTY_IS_MARKUP",
            )
        );
        $arCategoriesFilterredIDs = array();
        $backOccupancies = [];
        // наценки
        $markups = [];
        // отфильтрованные категории возрастов детей (согласно поисковому запросу)
        $filteredChildrenAgesId = [];
        // массив категорий номеров, где есть размещения детей без предосатвления места
        $roomsWithNoSeatForChild = [];

        // Вычисляем возрастные интервалы согласно возрасту детей из поискового запроса
        if (!empty($children)) {
            foreach ($arAges as $arAge) {
                foreach ($arChildrenAge as $age) {
                    if ($arAge['UF_MIN_AGE'] <= $age && $arAge['UF_MAX_AGE'] >= $age) {
                        if (isset($filteredChildrenAgesId[$arAge['ID']]['COUNT'])) {
                            $filteredChildrenAgesId[$arAge['ID']]['COUNT'] += $filteredChildrenAgesId[$arAge['ID']]['COUNT'];
                        } else {
                            $filteredChildrenAgesId[$arAge['ID']] = $arAge;
                            $filteredChildrenAgesId[$arAge['ID']]['COUNT'] = 1;
                        }
                    }
                }
            }
        }

        if (empty($filteredChildrenAgesId) && !empty($children)) {
            $guests += $children;
            $childrenIsAdults = true;
            $children = 0;
        }

        while ($arOccupancy = $rsOccupancies->Fetch()) {
            // Складываем в отедльный массив все наценки            
            if ($arOccupancy['PROPERTY_IS_MARKUP_VALUE'] == 'Да') {
                if (!empty($children) && isset($filteredChildrenAgesId[$arOccupancy["PROPERTY_CHILDREN_AGES_VALUE"][0]])) {
                    $markups[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]][$arOccupancy["CODE"]] = $arOccupancy;
                }
                if (str_contains($arOccupancy['CODE'], 'e.') && $arOccupancy['PROPERTY_MAIN_BEDS_VALUE'] < $guests) {
                    $markups[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]][$arOccupancy["CODE"]] = $arOccupancy;
                }
                if (str_contains($arOccupancy['CODE'], $arOccupancy["PROPERTY_CHILDREN_AGES_VALUE"][0] . '.0') && isset($filteredChildrenAgesId[$arOccupancy["PROPERTY_CHILDREN_AGES_VALUE"][0]])) {
                    $roomsWithNoSeatForChild[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]] = 'Y';
                }
                continue;
            }
            if (isset($roomsWithNoSeatForChild[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]])) {
                $arOccupancy["PROPERTY_GUESTS_COUNT_VALUE"] += 1;
            }
            $backOccupancies[] = $arOccupancy;
            if ($arOccupancy["PROPERTY_GUESTS_COUNT_VALUE"] >= $totalPeople) {
                if (!empty($children)) {
                    $childrenStatus = false;
                    foreach ($arOccupancy["PROPERTY_CHILDREN_AGES_VALUE"] as $key => $idAge) {
                        foreach ($arChildrenAge as $age) {
                            if ($arAges[$idAge]['UF_MIN_AGE'] <= $age && $arAges[$idAge]['UF_MAX_AGE'] >= $age && $arOccupancy["PROPERTY_CHILDREN_AGES_DESCRIPTION"][$key] == $children) {
                                $childrenStatus = true;
                            }
                        }

                        if ($childrenStatus == true) {
                            break;
                        }
                    }
                } elseif (!empty($arOccupancy["PROPERTY_CHILDREN_AGES_VALUE"]) && empty($arOccupancy['PROPERTY_MAIN_BEDS_VALUE'])) {
                    continue;
                }

                if (!isset($childrenStatus) || $childrenStatus != false) {
                    $arCategoriesFilterredIDs[] = $arOccupancy["PROPERTY_CATEGORY_ID_VALUE"];
                    $occupancySeatsSettings[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]] = $arOccupancy;
                }
            }
        }

        if (empty($arCategoriesFilterredIDs)) {
            foreach ($backOccupancies as $arOccupancy) {
                if ($arOccupancy["PROPERTY_GUESTS_COUNT_VALUE"] >= $totalPeople) {
                    $arCategoriesFilterredIDs[] = $arOccupancy["PROPERTY_CATEGORY_ID_VALUE"];
                    $occupancySeatsSettings[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]] = $arOccupancy;
                }
            }
        }

        $arCategoriesFilterredIDs = array_unique($arCategoriesFilterredIDs);

        // Номера        
        if ($arCategoriesFilterredIDs && $arTariffsIDs) {
            $rsElements = CIBlockElement::GetList(
                false,
                array(
                    "IBLOCK_ID" => $this->catalogIBlockID,
                    "ACTIVE" => "Y",
                    "PROPERTY_CATEGORY" => $arCategoriesFilterredIDs,
                ),
                false,
                false,
                array("IBLOCK_ID", "ID", "NAME", "PROPERTY_CATEGORY", "PROPERTY_TARIFF", "PROPERTY_EXTERNAL_ID", "PROPERTY_MANUAL_TARIF")
            );
            $arElementsFilterred = array();
            while ($arElement = $rsElements->Fetch()) {
                if ($arElement['PROPERTY_MANUAL_TARIF_VALUE'] == 'Y') {
                    foreach ($arElement["PROPERTY_CATEGORY_VALUE"] as $categoryId) {
                        foreach ($arTariffsIDs as $tariffId) {
                            if (in_array($tariffId, $arElement['PROPERTY_TARIFF_VALUE'])) {
                                $arElementsFilterred[$tariffId][$categoryId][] = $arElement["ID"];
                            }
                        }
                    }
                } else {
                    foreach ($arElement["PROPERTY_CATEGORY_VALUE"] as $categoryId) {
                        foreach ($arTariffsIDs as $tariffId) {
                            $arElementsFilterred[$tariffId][$categoryId][] = $arElement["ID"];
                        }
                    }
                }
            }

            array_pop($arDates);
            $arItems = array();

            foreach ($arElementsFilterred as $tariffId => $arCategories) {
                foreach ($arCategories as $categoryId => $arRooms) {
                    foreach ($arRooms as $elementId) {
                        $arPrices = array();
                        foreach ($arDates as $date) {
                            foreach ($arData as $arEntity) {
                                if ($arEntity["UF_DATE"]->format('d.m.Y') == $date && $arTariffsExternalIDs[$arEntity["UF_TARIFF_ID"]] == $tariffId && $arRoomTypesIDs[$arEntity["UF_CATEGORY_ID"]] == $categoryId) {
                                    $arPrices[$arEntity["UF_DATE"]->format('Y-m-d')] = (float)$arEntity["UF_PRICE"];
                                    break;
                                }
                            }
                        }

                        if ($occupancySeatsSettings[$categoryId]['PROPERTY_CHILDREN_COUNT_VALUE'] === '0' && $children) {
                            continue;
                        }

                        $variantsSummary = [];
                        $markupVariants = [];
                        $seatDispence = [];
                        $filteredChildrenVariants = [];

                        // Если по номеру присутствуют наценки
                        if (isset($markups[$categoryId])) {
                            $seatDispence['main'] = $guests;
                            $extraSeats = $occupancySeatsSettings[$categoryId]['PROPERTY_MAIN_BEDS_VALUE'] - $guests;
                            if ($extraSeats < 0) {
                                $seatDispence['extra'] = 0 - $extraSeats;
                            }
                            if ($childrenIsAdults) {
                                $seatDispence['childrenIsAdults'] = count($arChildrenAge);
                            }

                            foreach ($filteredChildrenAgesId as $arAge => $value) {
                                if ($extraSeats > 0) {
                                    $filteredChildrenVariants[] = 'c.1.' . $arAge;
                                }

                                $filteredChildrenVariants[] = 'x.1.' . $arAge . '.1';
                                $filteredChildrenVariants[] = 'x.1.' . $arAge . '.0';
                            }

                            $test = $this->combine($filteredChildrenVariants, $children);

                            foreach ($test as $key => $var) {
                                foreach ($var as $acc) {
                                    if (!isset($markups[$categoryId][$acc]) && !str_contains($acc, 'c.1')) {
                                        unset($test[$key]);
                                        break;
                                    }
                                }
                            }

                            foreach ($filteredChildrenAgesId as $value) {
                                foreach ($test as $key => $var) {
                                    $countDouble = 0;
                                    $countMainBed = 0;
                                    $countExtraBed = 0;
                                    foreach ($var as $acc) {
                                        if ($occupancySeatsSettings[$categoryId]['PROPERTY_GUESTS_COUNT_VALUE'] == $guests) {
                                            unset($test[$key]);
                                            break;
                                        }
                                        // убираем дубли                                    
                                        if (str_contains($acc, $value['ID'])) {
                                            $countDouble += 1;
                                            if ($countDouble > $value['COUNT']) {
                                                unset($test[$key]);
                                                break;
                                            }
                                        }
                                        // убираем основные места, если все заняты
                                        if ($extraSeats > 0 && str_contains($acc, 'c.1')) {
                                            $countMainBed += 1;
                                            if ($countMainBed > $extraSeats) {
                                                unset($test[$key]);
                                                break;
                                            }
                                        }
                                        // убираем варианты с лишними доп. местами
                                        if (str_contains($acc, 'x.1') && !str_contains($acc, '.0')) {
                                            $countExtraBed += 1;
                                            if ($countExtraBed > $occupancySeatsSettings[$categoryId]['PROPERTY_CHILDREN_COUNT_VALUE']) {
                                                unset($test[$key]);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }

                            if (count($test)) {
                                $markupVariants = $test;
                            } else if (isset($seatDispence['extra'])) {
                                $markupVariants[] = ['e.1'];
                            }

                            if (empty($test) && $children) {
                                continue;
                            }

                            foreach ($markups[$categoryId] as $tempVariant) {
                                if ($tempVariant['PROPERTY_MAIN_BEDS_VALUE'] <= $guests && str_contains($tempVariant['CODE'], 'c.1')) {
                                    continue;
                                }
                            }

                            // вычисление мест                            
                            foreach ($markupVariants as $variant) {
                                $arMarkupPrices = [];
                                $variantName = '';
                                if ($extraSeats < 0 && array_search('e.1', $variant) !== 0) {
                                    array_unshift($variant, 'e.1');
                                }
                                $sortedVar = $this->sortVariant($variant);
                                foreach ($sortedVar as $markupCode) {
                                    $markup = $markups[$categoryId][$markupCode];

                                    if ($extraSeats < 0) {
                                        $extraAdultsMultiplicator = $seatDispence['extra'];
                                    } else {
                                        $extraAdultsMultiplicator = 1;
                                    }
                                    $extraChildMultiplicator = 1;

                                    if (empty($markup) && str_contains($markupCode, 'c.1')) {
                                        $ageCode = str_replace('c.1.', '', $markupCode);
                                        $markup = [
                                            'NAME' => plural_form($extraChildMultiplicator, array('ребёнок', 'детей', 'детей')) . ' (' . $filteredChildrenAgesId[$ageCode]['UF_MIN_AGE'] . '-' . $filteredChildrenAgesId[$ageCode]['UF_MAX_AGE'] . ' лет) на основном месте',
                                            'PRICE' => 0,
                                        ];
                                    } else {
                                        if (!str_contains($markupCode, 'e.1')) {
                                            $start = plural_form($extraChildMultiplicator, array('ребёнок', 'детей', 'детей'));
                                            $markup['NAME'] = str_replace('1 детей', $start, $markup['NAME']);
                                        } else {
                                            $start = plural_form($extraAdultsMultiplicator, array('взрослый', 'взрослых', 'взрослых'));
                                            $markup['NAME'] = str_replace('1 взрослых', $start, $markup['NAME']);
                                        }
                                    }

                                    $variantName .= $markup['NAME'] . '<br> ';
                                    foreach ($arDates as $date) {
                                        foreach ($arData as $arEntity) {
                                            if ($arEntity["UF_DATE"]->format('d.m.Y') == $date && $arTariffsExternalIDs[$arEntity["UF_TARIFF_ID"]] == $tariffId && $arEntity["UF_CATEGORY_ID"] == $markup['PROPERTY_MARKUP_EXTERNAL_ID_VALUE']) {
                                                $arMarkupPrices[$arEntity["UF_DATE"]->format('Y-m-d') . '-' . $markup['ID']] = (float)$arEntity["UF_PRICE"] * (str_contains($markup['CODE'], 'e.') ? $extraAdultsMultiplicator : $extraChildMultiplicator);
                                                break;
                                            }
                                        }
                                    }
                                }

                                $variantsSummary[] = [
                                    'NAME' => mb_substr($variantName, 0, mb_strlen($variantName) - 5),
                                    'PRICE' => array_sum($arMarkupPrices) + array_sum($arPrices),
                                ];
                            }

                            uasort($variantsSummary, function ($a, $b) {
                                return ($a['PRICE'] - $b['PRICE']);
                            });

                            $variantsSummary = array_values($variantsSummary);
                        }

                        if (!empty($arPrices)) {
                            $arItems[$elementId][] = array(
                                'tariffId' => array_search($tariffId, $arTariffsExternalIDs),
                                'categoryId' => array_search($categoryId, $arRoomTypesIDs),
                                'prices' => $arPrices,
                                'price' => count($variantsSummary) ? $variantsSummary[0]['PRICE'] : array_sum($arPrices),
                                'value' => $arTariffsValue[$tariffId],
                                'variants' => $variantsSummary,
                                'seatDispence' => $seatDispence,
                            );

                            if (empty($arItems[$elementId]['minPrice']) || array_sum($arPrices) < $arItems[$elementId]['minPrice']) {
                                $arItems[$elementId]['minPrice'] = array_sum($arPrices);
                            }
                        }
                    }
                }
            }
        }

        // Сортировка номеров по убыванию цены
        if (!empty($arItems)) {
            uasort($arItems, function ($a, $b) {
                return ($a['minPrice'] - $b['minPrice']);
            });
        }

        if (empty($arItems) && $error == '') {
            $error = 'Не найдено номеров на выбранные даты';
        }

        //xprint($arItems);

        return [
            'arItems' => $arItems,
            'error' => $error,
        ];
    }

    private function sortVariant($var)
    {
        $sortedVar = [];
        $sortedVar = array_merge($sortedVar, $this->getElementFromArray('e.1', $var, false));
        $sortedVar = array_merge($sortedVar, $this->getElementFromArray('c.1', $var, false));
        $sortedVar = array_merge($sortedVar, $this->getElementFromArray('x.1', $var, true));
        $sortedVar = array_merge($sortedVar, $this->getElementFromArray('.0', $var, false));
        return $sortedVar;
    }

    private function getElementFromArray($acc, $arr, $removeNoSeat)
    {
        $result = [];
        foreach ($arr as $value) {
            if ($removeNoSeat) {
                if (str_contains($value, $acc) && !str_contains($value, '.0')) {
                    $result[] = $value;
                }
            } else {
                if (str_contains($value, $acc)) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    private function combine($arr, $k)
    {
        $combs = [];
        $comb = range(0, $k - 1);

        while ($comb) {
            $combs[] = array_map(function ($i) use ($arr) {
                return $arr[$i];
            }, $comb);

            for ($i = $k - 1, $max = count($arr) - 1; $i > -1; $i--, $max--) {
                if ($comb[$i] < $max) {
                    $comb[$i]++;
                    for ($j = $i + 1; $j < $k; $j++) {
                        $comb[$j] = $comb[$j - 1] + 1;
                    }
                    break;
                }
            }
            if ($i < 0) $comb = null;
        }

        return $combs;
    }

    // HL Возрастные интервалы
    public static function getAges($sectionId = '')
    {
        $childrenAgesEntityClass = self::getEntityClass(self::$childrenAgesHLId);

        if ($sectionId) {
            $arFilter = [
                "select" => ["*"],
                "filter" => [
                    "UF_HOTEL_ID" => $sectionId
                ]
            ];
        } else {
            $arFilter = [
                "select" => ["*"],
            ];
        }

        $rsData = $childrenAgesEntityClass::getList($arFilter);

        while ($arEntity = $rsData->Fetch()) {
            $arAges[$arEntity['ID']] = $arEntity;
        }

        return $arAges;
    }

    /* Установка минимальных цен для объектов Bnovo */
    public function setMinPrices()
    {
        $rsSections = CIBlockSection::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "!UF_EXTERNAL_ID" => false, "UF_EXTERNAL_SERVICE" => $this->bnovoSectionPropEnumId), false, array("ID", "UF_EXTERNAL_ID"), false);
        $arSectionExternalIDs = array();
        while ($arSection = $rsSections->Fetch()) {
            $arSectionExternalIDs[] = (string)$arSection["UF_EXTERNAL_ID"];
        }

        $entityClass = $this->getEntityClass();
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "filter" => [
                "UF_DATE" => [date('d.m.Y'), date('d.m.Y', strtotime('+1 day'))],
                "UF_RESERVED" => 0,
                "UF_CLOSED" => 0,
                [
                    "LOGIC" => "OR",
                    ["<=UF_MIN_STAY" => 1],
                    ["UF_MIN_STAY" => "0"]
                ],
                [
                    "LOGIC" => "OR",
                    [">=UF_MAX_STAY" => 1],
                    ["UF_MAX_STAY" => "0"],
                ],
                [
                    "LOGIC" => "OR",
                    ["!UF_CLOSED_ARRIVAL" => date('d.m.Y')],
                    ["UF_CLOSED_ARRIVAL" => "0"],
                ],
                [
                    "LOGIC" => "OR",
                    ["!UF_CLOSED_DEPARTURE" => date('d.m.Y', strtotime('+1 day'))],
                    ["UF_CLOSED_DEPARTURE" => "0"],
                ]
            ],
            "order" => ["UF_PRICE" => "ASC"],
        ]);
        $arData = $rsData->FetchAll();

        $arMinPrices = array();
        foreach ($arData as $arItem) {
            if (empty($arMinPrices[$arItem["UF_HOTEL_ID"]]) || $arItem["UF_PRICE"] < $arMinPrices[$arItem["UF_HOTEL_ID"]])
                $arMinPrices[$arItem["UF_HOTEL_ID"]] = $arItem["UF_PRICE"];
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

    /* Выгрузка цен и броней */
    public function update()
    {
        // Отели
        $rsSections = CIBlockSection::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->catalogIBlockID,
                "ACTIVE" => "Y",
                "UF_EXTERNAL_SERVICE" => $this->bnovoSectionPropEnumId,
                "!UF_EXTERNAL_ID" => false
            ),
            false,
            array("IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"),
            false
        );
        $arSectionIDs = array();
        while ($arSection = $rsSections->Fetch()) {
            $arSectionIDs[$arSection["ID"]] = $arSection["UF_EXTERNAL_ID"];
        }

        // Тарифы
        $rsTariffs = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->tariffsIBlockID,
                "ACTIVE" => "Y",
                "!PROPERTY_EXTERNAL_ID" => false,
            ),
            false,
            false,
            array("IBLOCK_ID", "ID", "NAME", "PROPERTY_EXTERNAL_ID")
        );
        $arTariffsExternalIDs = array();
        while ($arTariff = $rsTariffs->Fetch()) {
            $arTariffsExternalIDs[$arTariff["ID"]] = $arTariff["PROPERTY_EXTERNAL_ID_VALUE"];
        }

        // Категории номеров
        $rsRoomTypes = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->roomTypesIBlockID,
                "ACTIVE" => "Y",
                "!PROPERTY_EXTERNAL_ID" => false,
            ),
            false,
            false,
            array("ID", "NAME", "PROPERTY_EXTERNAL_ID", "PROPERTY_GUESTS_COUNT", "PROPERTY_CHILDREN_COUNT")
        );
        $arRoomTypesExternalIDs = array();
        while ($arRoomType = $rsRoomTypes->Fetch()) {
            $arRoomTypesExternalIDs[$arRoomType["ID"]] = $arRoomType["PROPERTY_EXTERNAL_ID_VALUE"];
        }

        // Размещение номеров
        $rsOccupancies = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->occupanciesIBlockID,
                "ACTIVE" => "Y",
            ),
            false,
            false,
            array("ID", "NAME", "PROPERTY_CATEGORY_ID", "PROPERTY_GUESTS_COUNT", "PROPERTY_CHILDREN_COUNT")
        );
        $arOccupancies = array();
        while ($arOccupancy = $rsOccupancies->Fetch()) {
            $arOccupancies[$arOccupancy["PROPERTY_CATEGORY_ID_VALUE"]][$arOccupancy["ID"]] = array(
                'guests' => $arOccupancy["PROPERTY_GUESTS_COUNT_VALUE"],
                'children' => $arOccupancy["PROPERTY_CHILDREN_COUNT_VALUE"]
            );
        }

        // Номера
        $rsElements = CIBlockElement::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->catalogIBlockID,
                "ACTIVE" => "Y",
                "!PROPERTY_CATEGORY_ID" => false,
                "!PROPERTY_TARIFF_ID" => false,
            ),
            false,
            false,
            array("IBLOCK_ID", "ID", "NAME", "PROPERTY_CATEGORY", "PROPERTY_TARIFF")
        );
        $arElements = array();
        while ($arElement = $rsElements->Fetch()) {
            foreach ($arElement["PROPERTY_CATEGORY_VALUE"] as $categoryId) {
                if (!empty($arTariffsExternalIDs[$arElement["PROPERTY_TARIFF_VALUE"]]) && !empty($arRoomTypesExternalIDs[$categoryId])) {
                    $arElements[$arTariffsExternalIDs[$arElement["PROPERTY_TARIFF_VALUE"]]][$arRoomTypesExternalIDs[$categoryId]] = $arElement["ID"];
                }
            }
        }

        $url = $this->bnovoApiURL . '/plans_data';
        $headers = array(
            "Content-Type: application/json"
        );

        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d', strtotime('+' . $this->hlDataMonthCount . ' months'));

        $entityClass = $this->getEntityClass();
        foreach ($arSectionIDs as $hotelId) {
            $data = array(
                "token" => $this->token,
                "account_id" => $hotelId,
                "dfrom" => $dateFrom,
                "dto" => $dateTo
            );

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url . '?' . http_build_query($data),
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => $headers
            ));
            $response = curl_exec($ch);
            $arData = json_decode($response, true);
            curl_close($ch);

            foreach ($arData["plans_data"] as $tariffId => $arCategories) {
                foreach ($arCategories as $categoryId => $arCategoryDates) {
                    foreach ($arCategoryDates as $date => $arDate) {
                        $price = $arDate['price'];
                        $isReserved = ($arDate['closed']) ? 1 : 0;
                        $minStay = $arDate['min_stay'];
                        $maxStay = $arDate['max_stay'];
                        $closedArrival = $arDate['closed_arrival'];
                        $closedDeparture = $arDate['closed_departure'];

                        $rsData = $entityClass::getList([
                            "select" => ["*"],
                            "filter" => [
                                "UF_HOTEL_ID" => $hotelId,
                                "UF_TARIFF_ID" => $tariffId,
                                "UF_CATEGORY_ID" => $categoryId,
                                "UF_DATE" => date('d.m.Y', strtotime($date))
                            ],
                            "order" => ["ID" => "ASC"],
                        ]);
                        $arEntity = $rsData->Fetch();
                        if ($arEntity) {
                            $entityId = $arEntity['ID'];
                            $arFields = array(
                                "UF_PRICE" => $price,
                                "UF_CLOSED" => $isReserved,
                                //"UF_RESERVED" => $isReserved,
                                "UF_MIN_STAY" => $minStay,
                                "UF_MAX_STAY" => $maxStay,
                                "UF_CLOSED_ARRIVAL" => $closedArrival,
                                "UF_CLOSED_DEPARTURE" => $closedDeparture,
                            );
                            $entityClass::update($entityId, $arFields);
                        } else {
                            $arFields = array(
                                "UF_HOTEL_ID" => $hotelId,
                                "UF_TARIFF_ID" => $tariffId,
                                "UF_CATEGORY_ID" => $categoryId,
                                "UF_DATE" => date('d.m.Y', strtotime($date)),
                                "UF_CLOSED" => $isReserved,
                                "UF_PRICE" => $price,
                                //"UF_RESERVED" => $isReserved,
                                "UF_MIN_STAY" => $minStay,
                                "UF_MAX_STAY" => $maxStay,
                                "UF_CLOSED_ARRIVAL" => $closedArrival,
                                "UF_CLOSED_DEPARTURE" => $closedDeparture,
                            );
                            $entityClass::add($arFields);
                        }
                    }
                }
            }
        }

        // Удаление старых записей
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "filter" => [
                "<UF_DATE" => date('d.m.Y')
            ],
            "order" => ["ID" => "DESC"],
        ]);
        $arEntity = $rsData->Fetch();
        if ($arEntity) {
            $entityId = $arEntity['ID'];
            $entityClass::Delete($entityId);
        }
    }

    /* Получение объекта по UID и дальнейшее получение данных */
    public function updatePublicObject($uid, $onlyRooms = false, $onlyTariffs = false, $onlyData = false)
    {
        $arSection = [];
        // Отели
        $arSection = CIBlockSection::GetList(
            array("ID" => "ASC"),
            array(
                "IBLOCK_ID" => $this->catalogIBlockID,
                "ACTIVE" => "Y",
                "UF_EXTERNAL_SERVICE" => $this->bnovoSectionPropEnumId,
                "UF_EXTERNAL_UID" => $uid
            ),
            false,
            array("IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"),
            false
        )->Fetch();

        $arResult = [];
        $arResult["MESSAGE"]["ERRORS"] = '';
        $arResult["MESSAGE"]["SUCCESS"] = '';

        $url = $this->bnovoApiPublicURL . '/accounts';
        $headers = array(
            "Content-Type: application/json"
        );

        $data = array(
            "uid" => $uid,
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($ch);
        $arData = json_decode($response, true);
        curl_close($ch);

        if ($onlyData) {
            return $arData;
        }

        if (empty($arSection)) {
            $arSection = $arData['account'];

            $iS = new CIBlockSection();
            $sectionName = $arSection["name"];
            $sectionCode = \CUtil::translit($sectionName, "ru");
            $arImages = array();

            // Поля раздела
            $arFields = array(
                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                "ACTIVE" => "Y",
                "NAME" => $sectionName,
                "CODE" => $sectionCode,
                "UF_EXTERNAL_ID" => $arSection["id"],
                "UF_EXTERNAL_SERVICE" => $this->bnovoSectionPropEnumId,
                "UF_ADDRESS" => $arSection['address'],
                "UF_EMAIL" => $arSection['email'],
                "UF_PHONE" => $arSection['phone'],
                "UF_TIME_FROM" => $arSection['checkin'],
                "UF_TIME_TO" => $arSection['checkout'],
                "UF_COORDS" => str_replace(['(', ')'], '', $arSection['geo_data']),
                "UF_EXTERNAL_UID" => $arSection['uid'],
            );

            $sectionId = $iS->Add($arFields);

            if ($sectionId) {
                // echo "Добавлен раздел (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
                $arSection['UF_EXTERNAL_ID'] = $arSection["id"];
                $arSection['ID'] = $sectionId;

                // echo 'Добавлен объект с ID ' . $uid . ': ' . $sectionName;
                $arResult["MESSAGE"]["SUCCESS"] = 'Добавлен объект с ID ' . $uid . ': ' . $sectionName;
            } else {
                $arResult["MESSAGE"]["ERRORS"] = $iS->LAST_ERROR;
            }
        } else {
            if (is_array($arData['account'])) {
                $arSection = array_merge($arData['account'], $arSection);
            }
            // echo 'Объект с ID = ' . $arSection['ID'] . ' уже существует. Данные по объекту были обновлены.'."<br>\r\n";
            $arResult["MESSAGE"]["ERRORS"] = 'Объект с указанным ID уже существует. Данные по объекту были обновлены.';
        }

        if (!empty($arSection['UF_EXTERNAL_ID'])) {
            $childrenAgesId = self::updatePublicChildrenAges($arSection);
            $tariffsId = self::updatePublicTariffs($arSection);
            if (!$onlyTariffs) {
                $this->updatePublicRoomtypes($arSection, $tariffsId, $childrenAgesId, $onlyRooms);
            }
        }

        return $arResult;
    }

    /* Возрастные интервалы отеля */
    public function updatePublicChildrenAges($arSection)
    {
        // HL Возрастные интервалы
        $childrenAgesId = array();
        $childrenAgesEntityClass   = self::getEntityClass(self::$childrenAgesHLId);
        foreach ($arSection["children_ages"] as $arElement) {
            if (!empty($arElement)) {
                $rsData = $childrenAgesEntityClass::getList([
                    "select" => ["*"],
                    "filter" => [
                        'UF_HOTEL_ID' => $arSection['ID'],
                        "UF_XML_ID" => $arElement['id']
                    ]
                ]);
                $arEntity = $rsData->Fetch();

                if (!$arEntity) {
                    $arValues = array(
                        'UF_HOTEL_ID' => $arSection['ID'],
                        'UF_XML_ID' => $arElement['id'],
                        'UF_MIN_AGE' => $arElement['min_age'],
                        'UF_MAX_AGE' => $arElement['max_age'],
                    );
                    $result = $childrenAgesEntityClass::add($arValues);
                    $entityId = $result->getId();

                    $childrenAgesId[$arElement['id']] = $entityId;
                } else {
                    $arValues = array(
                        'UF_HOTEL_ID' => $arSection['ID'],
                        'UF_MIN_AGE' => $arElement['min_age'],
                        'UF_MAX_AGE' => $arElement['max_age'],
                    );
                    $entityId = $arEntity["ID"];
                    $childrenAgesEntityClass::update($entityId, $arValues);

                    $childrenAgesId[$arElement['id']] = $entityId;
                }
            }
        }

        return $childrenAgesId;
    }

    /* Тарифы отеля */
    public function updatePublicTariffs($arSection)
    {
        $isSectionJustCreated = false;
        $sectionName = $arSection["NAME"] ?? $arSection["name"];

        // Тарифы
        $url = $this->bnovoApiPublicURL . '/plans';
        $headers = array(
            "Content-Type: application/json"
        );

        $data = array(
            "account_id" => $arSection['UF_EXTERNAL_ID'],
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($ch);
        $arData = json_decode($response, true);
        curl_close($ch);
        $arTarrifs = $arData['plans'];

        $iE = new CIBlockElement();

        //Секция объекта для тарифов
        $arSectionTarriffs = CIBlockSection::GetList(
            false,
            array(
                "IBLOCK_ID" => TARIFFS_IBLOCK_ID,
                "ACTIVE" => "Y",
                "UF_EXTERNAL_ID" => $arSection['UF_EXTERNAL_ID']
            ),
            false,
            array("IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"),
            false
        )->Fetch();

        if (empty($arSectionTarriffs)) {
            $iS = new CIBlockSection();
            $sectionCode = \CUtil::translit($sectionName, "ru");

            // Поля раздела
            $arFields = array(
                "IBLOCK_ID" => TARIFFS_IBLOCK_ID,
                "ACTIVE" => "Y",
                "NAME" => $sectionName,
                "CODE" => $sectionCode,
                "UF_EXTERNAL_ID" => $arSection["UF_EXTERNAL_ID"],
            );

            $sectionId = $iS->Add($arFields);

            if ($sectionId) {
                $isSectionJustCreated = true;
                // echo "Добавлен раздел тарифов (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
            }
        } else {
            $sectionId = $arSectionTarriffs['ID'];
        }

        $tariffsIds = [];

        // Если раздел для тарифов был создан только что, добавляем стандартный тариф без свойств
        if ($isSectionJustCreated) {
            $arFields = [
                "ACTIVE" => "Y",
                "IBLOCK_ID" => TARIFFS_IBLOCK_ID,
                "IBLOCK_SECTION_ID" => $sectionId,
                "NAME" => "Стандартный тариф",
            ];
            $elementId = $iE->Add($arFields);

            if ($elementId) {
                $tariffsIds[] = $elementId;
            }

            // echo "Добавлен стандартный тариф (".$elementId.") в отеле (".$sectionId.") \"".$sectionName."\"<br>\r\n";
        }

        foreach ($arTarrifs as $arTarrif) {
            if ($arTarrif['enabled_ota'] != 1) {
                continue;
            }
            $elementName = $arTarrif["name"];
            $elementCode = \CUtil::translit($elementName, "ru");

            // Поля элемента
            $arElementTariff = CIBlockElement::GetList(false, array(
                "IBLOCK_ID" => TARIFFS_IBLOCK_ID,
                "PROPERTY_EXTERNAL_ID" => $arTarrif['id'],
            ))->Fetch();

            if (isset($arElementTariff)) {
                $elementId = $arElementTariff['ID'];

                $res = CIBlockElement::SetPropertyValuesEx($elementId, TARIFFS_IBLOCK_ID, array(
                    "CANCELLATION_RULES" => nl2br($arTarrif['cancellation_rules']),
                    "CANCELLATION_DEADLINE" => nl2br($arTarrif['cancellation_deadline']),
                    "CANCELLATION_FINE_TYPE" => $arTarrif['cancellation_fine_type'],
                    "CANCELLATION_FINE_AMOUNT" => $arTarrif['cancellation_fine_amount'],
                ));

                // echo "Обновлен тариф (".$elementId.") \"".$elementName."\" в отеле (".$sectionId.") \"".$sectionName."\"<br>\r\n";

            } else {
                continue;
            }

            $tariffsIds[] = $elementId;
        }

        return $tariffsIds;
    }

    /* Категории отеля */
    public function updatePublicRoomtypes($arSection, $tariffsIds, $childrenAgesId, $onlyRooms)
    {
        $sectionName = $arSection["NAME"] ?? $arSection["name"];

        $noCildren = true;
        $isMarkup = false;
        $childAgeWithNoSeat = 0;

        // Номера
        $url = $this->bnovoApiPublicURL . '/roomtypes';
        $headers = array(
            "Content-Type: application/json"
        );

        $data = array(
            "account_id" => $arSection['UF_EXTERNAL_ID'],
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($ch);
        $arData = json_decode($response, true);
        curl_close($ch);
        $arRooms = $arData['rooms'];

        $iE = new CIBlockElement();

        //Секция объекта для номеров
        $arSectionCategory = CIBlockSection::GetList(
            false,
            array(
                "IBLOCK_ID" => CATEGORIES_IBLOCK_ID,
                "ACTIVE" => "Y",
                "UF_EXTERNAL_ID" => $arSection['UF_EXTERNAL_ID']
            ),
            false,
            array("IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"),
            false
        )->Fetch();

        if (empty($arSectionCategory)) {
            $iS = new CIBlockSection();
            $sectionName = $arSection["NAME"] ?? $arSection["name"];
            $sectionCode = \CUtil::translit($sectionName, "ru");

            // Поля раздела
            $arFields = array(
                "IBLOCK_ID" => CATEGORIES_IBLOCK_ID,
                "ACTIVE" => "Y",
                "NAME" => $sectionName,
                "CODE" => $sectionCode,
                "UF_EXTERNAL_ID" => $arSection["UF_EXTERNAL_ID"],
            );

            $sectionId = $iS->Add($arFields);

            if ($sectionId) {
                // echo "Добавлен раздел (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
            }
        } else {
            $sectionId = $arSectionCategory['ID'];
        }

        //Секция объекта для размещения
        $arSectionOccupancies = CIBlockSection::GetList(
            false,
            array(
                "IBLOCK_ID" => OCCUPANCIES_IBLOCK_ID,
                "ACTIVE" => "Y",
                "UF_EXTERNAL_ID" => $arSection['UF_EXTERNAL_ID']
            ),
            false,
            array("IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"),
            false
        )->Fetch();

        if (empty($arSectionOccupancies)) {
            $iS = new CIBlockSection();
            $sectionCode = \CUtil::translit($sectionName, "ru");

            // Поля раздела
            $arFields = array(
                "IBLOCK_ID" => OCCUPANCIES_IBLOCK_ID,
                "ACTIVE" => "Y",
                "NAME" => $sectionName,
                "CODE" => $sectionCode,
                "UF_EXTERNAL_ID" => $arSection["UF_EXTERNAL_ID"],
            );

            $sectionIdOccupancies = $iS->Add($arFields);

            if ($sectionIdOccupancies) {
                // echo "Добавлен раздел размещения (" . $sectionIdOccupancies . ") \"" . $sectionName . "\"<br>\r\n";
            }
        } else {
            $sectionIdOccupancies = $arSectionOccupancies['ID'];
        }

        foreach ($arRooms as $arRoom) {
            if ($arRoom["accommodation_type"] == 4) {
                continue;
            }
            $elementName = $arRoom["name"];
            $elementCode = \CUtil::translit($elementName, "ru");

            // Поля элемента
            $arCategory = CIBlockElement::GetList(false, array(
                "IBLOCK_ID" => CATEGORIES_IBLOCK_ID,
                "PROPERTY_EXTERNAL_ID" => $arRoom['id'],
            ))->Fetch();

            if (!$arCategory) {
                $arFields = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => CATEGORIES_IBLOCK_ID,
                    "IBLOCK_SECTION_ID" => $sectionId,
                    "NAME" => $elementName,
                    "CODE" => $elementCode,
                    "PROPERTY_VALUES" => array(
                        "EXTERNAL_ID" => $arRoom['id'],
                    )
                );
                $elementIdCat = $iE->Add($arFields);

                if ($elementIdCat) {
                    // echo "Добавлен номер (" . $elementIdCat . ") \"" . $elementName . "\" в отель (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
                }
            } else {
                $elementIdCat = $arCategory['ID'];
            }

            //Элемент размещения
            $elementName = $arRoom['adults'] . ' Взросл.';
            $elementCode = 'a.' . $arRoom['adults'];
            if (!empty($arRoom['children']) && $arRoom['children'] > 0) {
                $elementName .= '+ ' . $arRoom['children'] . ' детей с местом';
                $noCildren = false;
            }

            $arAgesValues = []; //Возрастные интервалы
            if (isset($arRoom['extra_array']['children_ages']) && !empty($arRoom['extra_array']['children_ages']) && !isset($arRoom['extra_array']['people'])) {
                $elementAppend = '';
                foreach ($arRoom['extra_array']['children_ages'] as $key => $arAge) {
                    $realChildrenAgesIds[$key] = $key;
                    if (is_array($arAge)) {
                        $arAgesValues[] = ["VALUE" => $childrenAgesId[$key] ? $childrenAgesId[$key] : 0, "DESCRIPTION" => $arAge[array_key_first($arAge)]['people_count']];
                        if ($childrenAgesId[$key]) {
                            $elementCode .= '_c.' . $arAge[array_key_first($arAge)]['people_count'] . '.' . $childrenAgesId[$key];
                            $noCildren = false;
                        } else {
                            $elementAppend = '_e.' . $arAge[array_key_first($arAge)]['people_count'];
                        }
                    } else {
                        $arAgesValues[] = ["VALUE" => $childrenAgesId[$key], "DESCRIPTION" => $arAge];
                        $elementCode .= '_c.' . $arAge . '.' . $childrenAgesId[$key];
                        $noCildren = false;
                    }
                }
                if ($elementAppend != '') {
                    $elementCode .= $elementAppend;
                    $elementAppend = '';
                }
            } elseif (isset($arRoom['extra_array']['children_ages']) && !empty($arRoom['extra_array']['children_ages']) && isset($arRoom['extra_array']['people'])) {
                $childrenCount = 0;
                $childrenMinAge = 100;
                $childrenMaxAge = -10;
                foreach ($arRoom['extra_array']['children_ages'] as $key => $arAge) {
                    if ($key == 0) {
                        continue;
                    }
                    $realChildrenAgesIds[$key] = $key;
                    if (is_array($arAge)) {
                        $arAgesValues[] = ["VALUE" => $childrenAgesId[$key], "DESCRIPTION" => $arAge[array_key_first($arAge)]['people_count']];
                        $childrenCount = $arRoom['extra_array']['people']['max_adults'] - $arRoom['extra_array']['people']['main_beds'];
                        if ($arSection['children_ages'][$key]['min_age'] < $childrenMinAge) {
                            $childrenMinAge = $arSection['children_ages'][$key]['min_age'];
                        }
                        if ($arSection['children_ages'][$key]['max_age'] > $childrenMaxAge) {
                            $childrenMaxAge = $arSection['children_ages'][$key]['max_age'];
                        }
                        $noCildren = false;
                    }
                }
            }

            // Поля элемента
            $arOccupancies = CIBlockElement::GetList(false, array(
                "IBLOCK_ID" => OCCUPANCIES_IBLOCK_ID,
                "PROPERTY_CATEGORY_ID" => $elementIdCat,
                "!PROPERTY_IS_MARKUP" => 13,
            ))->Fetch();

            if (!$arOccupancies) {
                $arFields = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => OCCUPANCIES_IBLOCK_ID,
                    "IBLOCK_SECTION_ID" => $sectionIdOccupancies,
                    "NAME" => $elementName,
                    "CODE" => $elementCode,
                    "PROPERTY_VALUES" => array(
                        "CATEGORY_ID" => $elementIdCat,
                        "GUESTS_COUNT" => $arRoom['adults'],
                        "CHILDREN_COUNT" => $arRoom['children'],
                        "CHILDREN_AGES" => $arAgesValues,
                        "CHILDREN_MIN_AGE" => $arRoom['children'] > 0 ? 0 : '',
                        "CHILDREN_MAX_AGE" => $arRoom['children'] > 0 ? 17 : '',
                    )
                );
                if (isset($arRoom['extra_array']['people'])) {
                    $arFields["PROPERTY_VALUES"]['MAIN_BEDS'] = $arRoom['extra_array']['people']['main_beds'];
                    $arFields["PROPERTY_VALUES"]['GUESTS_COUNT'] = $arRoom['extra_array']['people']['max_adults'];
                    $arFields["PROPERTY_VALUES"]['CHILDREN_COUNT'] = $childrenCount;
                    $arFields["PROPERTY_VALUES"]['CHILDREN_MIN_AGE'] = $childrenMinAge != 100 ? $childrenMinAge : '';
                    $arFields["PROPERTY_VALUES"]['CHILDREN_MAX_AGE'] = $childrenMaxAge != -10 ? $childrenMaxAge : '';
                }
                $elementIdOccupancies = $iE->Add($arFields);

                if ($elementIdOccupancies) {
                    // echo "Добавлено размещение (" . $elementIdOccupancies . ") \"" . $elementName . "\" в отель (" . $sectionId . ") \"" . $sectionName . "\"<br>\r\n";
                }
            } else {
                $elementId = $arOccupancies["ID"];
                $arFields = array(
                    "CODE" => $elementCode,
                );

                $res = $iE->Update($elementId, $arFields);
                CIBlockElement::SetPropertyValuesEx($elementId, OCCUPANCIES_IBLOCK_ID, array(
                    "CHILDREN_AGES" => $arAgesValues,
                    "CHILDREN_MIN_AGE" => $arRoom['children'] > 0 ? 0 : '',
                    "CHILDREN_MAX_AGE" => $arRoom['children'] > 0 ? 17 : '',
                ));

                $elementIdOccupancies = $arOccupancies['ID'];
            }

            if ((isset($arRoom['extra_array']['people']) && count($arRoom['extra_array']['people'])) && (isset($arRoom['extra_array']['children_ages']) && count($arRoom['extra_array']['children_ages']))) {
                $this->markupHandler($childrenAgesId, $elementIdCat, $sectionIdOccupancies, $arRoom, $arSection['children_ages']);
                $isMarkup = true;
            }

            //Товары объекта - номера
            $elementName = empty($arRoom["name_ru"]) ? $arRoom["name"] : $arRoom["name_ru"];
            $elementCode = \CUtil::translit($elementName, "ru");

            // Amenities
            // $arAmenities = array();
            // $roomsFeaturesEntityClass   = self::getEntityClass(self::$roomsFeaturesHLId);
            // foreach ($arRoom["amenities"] as $key => $arItem) {
            //     if ($key == "1") {
            //         continue;
            //     }
            //     $rsData = $roomsFeaturesEntityClass::getList([
            //         "select" => ["*"],
            //         "filter" => [
            //             "UF_XML_ID" => "bn_" . $key
            //         ],
            //         "order" => ["UF_SORT" => "ASC"],
            //     ]);
            //     $arEntity = $rsData->Fetch();
            //     if ($arEntity) {
            //         $arAmenities[] = $arEntity["UF_XML_ID"];
            //     }
            // }

            // Поля элемента
            $arExistElement = CIBlockElement::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "PROPERTY_EXTERNAL_ID" => $arRoom['id'], "PROPERTY_EXTERNAL_SERVICE" => 6))->Fetch();
            if ($arExistElement) {
                if (!$onlyRooms) {
                    $elementId = $arExistElement["ID"];
                    $arFields = array(
                        //"NAME" => $elementName,
                        "CODE" => $elementCode,
                        //"DETAIL_TEXT" => empty($arRoom["description"]) ? nl2br($arRoom["description_ru"]) : nl2br($arRoom["description"]),
                        //"DETAIL_TEXT_TYPE" => 'html'
                    );

                    $res = $iE->Update($elementId, $arFields);
                    CIBlockElement::SetPropertyValuesEx($elementId, CATALOG_IBLOCK_ID, array(
                        // "PHOTOS" => $arElementImages,
                        "CATEGORY" => $elementIdCat,
                        // "TARIFF" => $tariffsIds,
                        // "FEATURES" => $arAmenities,
                        "PARENT_ID" => $arRoom["parent_id"],
                        "SQUARE" => $arRoom["amenities"]["1"]["value"],
                    ));

                    if ($res) {
                        // echo "Обновлен номер (".$elementId.") \"".$elementName."\" в отеле (".$sectionId.") \"".$sectionName."\"<br>\r\n";
                    }
                }
            } else {
                $arElementImages = array();
                $arElementImages = self::getImages($arRoom["photos"]);

                $arFields = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                    "IBLOCK_SECTION_ID" => $arSection['ID'],
                    "NAME" => $elementName,
                    "CODE" => $elementCode,
                    "DETAIL_TEXT" => empty($arRoom["description"]) ? nl2br($arRoom["description_ru"]) : nl2br($arRoom["description"]),
                    "DETAIL_TEXT_TYPE" => 'html',
                    "PROPERTY_VALUES" => array(
                        "PHOTOS" => $arElementImages,
                        "EXTERNAL_ID" => $arRoom["id"],
                        "CATEGORY" => $elementIdCat,
                        "TARIFF" => $tariffsIds,
                        "EXTERNAL_SERVICE" => 6,
                        // "FEATURES" => $arAmenities,
                        "PARENT_ID" => $arRoom["parent_id"],
                        "SQUARE" => $arRoom["amenities"]["1"]["value"],
                    )
                );
                $elementId = $iE->Add($arFields);

                if ($elementId) {
                    Products::setQuantity($elementId);
                    Products::setPrice($elementId);
                }

                if ($elementId && $onlyRooms) {
                    $this->sendMessage([
                        'MESSAGE' => "Добавлен номер (" . $elementId . ") \"" . $elementName . "\" в отель (" . $sectionId . ") \"" . $sectionName . "\"",
                    ]);
                }
            }
        }

        if ($noCildren) {
            $iS = new CIBlockSection();
            $iS->Update($arSection['ID'], array(
                "UF_NO_CHILDREN_PLACE" => 1,
            ));
        }

        if (!$noCildren && !$isMarkup &&  is_array($realChildrenAgesIds) && !empty($realChildrenAgesIds)) {
            $realChildrenAgesIds = array_unique($realChildrenAgesIds);
            foreach ($childrenAgesId as $ageKey => $ageValue) {
                if (isset($realChildrenAgesIds[$ageKey])) {
                    continue;
                }
                $minChildInterval = $ageValue;
            }
            if (isset($minChildInterval)) {
                $entity =  HighloadBlockTable::compileEntity('ChildrenAges')->getDataClass();

                $query =  $entity::query()
                    ->addSelect('UF_MAX_AGE')
                    ->where('ID', $minChildInterval)
                    ?->fetch();

                if (isset($query['UF_MAX_AGE'])) {
                    $iS = new CIBlockSection();
                    $iS->Update($arSection['ID'], array(
                        "UF_MIN_AGE" => $query['UF_MAX_AGE'],
                    ));
                }
            }
        }
    }

    /**
     * Добавление/обновление размещения в виде наценки
     *
     * @return void
     * 
     */
    private function markupHandler($childrenAgesId, $elementIdCat, $sectionIdOccupancies, $arRoom, $childrenAges): void
    {
        $acc = new CIBlockElement();
        $seats = [
            0 => 'на основном месте',
            1 => 'на доп. месте',
            2 => 'без места',
        ];
        $people = [
            0 => 'c',
            1 => 'x',
            2 => 'x',
            3 => 'e'
        ];
        foreach ($arRoom['extra_array']['children_ages'] as $ageId => $arAge) {
            foreach ($arAge as $bedType => $data) {
                $elementName = '1' . ($ageId == 0 ? ' взрослых ' : ' детей (' . $childrenAges[$ageId]['min_age'] . '-' . $childrenAges[$ageId]['max_age'] . ' лет) ') . $seats[$bedType];
                if ($ageId == 0) {
                    $elementCode = 'e.1';
                } else {
                    if ($bedType == 2) {
                        $append = '.0';
                    } else if ($bedType == 1) {
                        $append = '.1';
                    } else {
                        $append = '';
                    }

                    $elementCode = $people[$bedType] . '.1.' . $childrenAgesId[$ageId] . $append;
                }
                $arFields = array(
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => OCCUPANCIES_IBLOCK_ID,
                    "IBLOCK_SECTION_ID" => $sectionIdOccupancies,
                    "NAME" => $elementName,
                    "CODE" => $elementCode,
                    "PROPERTY_VALUES" => array(
                        "CATEGORY_ID" => $elementIdCat,
                        "GUESTS_COUNT" => $data['people_count'],
                        "CHILDREN_COUNT" => $ageId != 0 ? $data['people_count'] : '',
                        "CHILDREN_AGES" => $ageId != 0 ? ["VALUE" => $childrenAgesId[$ageId], "DESCRIPTION" => $data['people_count']] : '',
                        "CHILDREN_MIN_AGE" => $ageId != 0 ? $childrenAges[$ageId]['min_age'] : '',
                        "CHILDREN_MAX_AGE" => $ageId != 0 ? $childrenAges[$ageId]['max_age'] : '',
                        "IS_MARKUP" => 17,
                        "MARKUP_PRICE" => $data['price'],
                        'MAIN_BEDS' => $arRoom['extra_array']['people']['main_beds'],
                    )
                );
                $curAcc = \Bitrix\Iblock\Elements\ElementOccupanciesTable::getList([
                    'select' => ['ID'],
                    'filter' => ['=ACTIVE' => 'Y', '=CODE' => $elementCode, '=CATEGORY_ID.VALUE' => $elementIdCat],
                ])->fetch();
                if (!$curAcc) {
                    $newId = $acc->Add($arFields);
                } else {
                    $oldId = $acc->Update($curAcc['ID'], $arFields);
                }
            }
        }
    }

    public static function getImages($arImagesUrl)
    {
        $arImages = array();
        foreach ($arImagesUrl as $key => $arImage) {
            $arFile = \CFile::MakeFileArray($arImage["url"]);

            if ($arFile) {
                $arImages[] = $arFile;
            }
        }

        return $arImages;
    }

    /* Обновление цен и броней */
    public function updateReservationData($hotelId, $arTariffs, $arCategories, $arDates)
    {
        $url = $this->bnovoApiURL . '/plans_data';
        $headers = array(
            "Content-Type: application/json"
        );

        sort($arDates);
        $dateFrom = $arDates[0];
        $dateTo = $arDates[count($arDates) - 1];

        $data = array(
            "token" => $this->token,
            "account_id" => $hotelId,
            "dfrom" => $dateFrom,
            "dto" => $dateTo,
            "plans" => (array)$arTariffs,
            "roomtypes" => (array)$arCategories
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($ch);
        $arData = json_decode($response, true);

        //xprint($arData);

        if (empty($arData) || (isset($arData['code']) && $arData['code'] != 200)) {
            if ($arData['code'] == 403) {
                return 'Объект отключен от вашего канала продаж';
            } else {
                return 'Непредвиденная ошибка. Код ошибки - ' . $arData['code'] . '. Сообщение - ' . $arData['message'];
            }
        }

        if (!empty($arData) && empty($arData["plans_data"])) {
            return 'Пустой массив. По объекту нет доступных тарифов';
        }

        curl_close($ch);

        $entityClass = new HighLoadBlockHelper(self::$pricesHlCode);
        $arResultRooms = [];

        $arDatesFilter = [];
        $period = new DatePeriod(
            new DateTime($dateFrom),
            new DateInterval('P1D'),
            new DateTime(date('d.m.Y', strtotime($dateTo . '+1 day')))
        );
        foreach ($period as $key => $value) {
            $arDatesFilter[] = $value->format('d.m.Y');
        }

        $entityClass->prepareParamsQuery(
            [
                "ID",
                "UF_PRICE",
                "UF_CLOSED",
                "UF_MIN_STAY",
                "UF_MAX_STAY",
                "UF_MIN_STAY_ARRIVAL",
                "UF_CLOSED_ARRIVAL",
                "UF_CLOSED_DEPARTURE",
                "UF_TARIFF_ID",
                "UF_CATEGORY_ID",
                "UF_DATE"
            ],
            [
                "ID" => "ASC"
            ],
            [
                "UF_HOTEL_ID" => $hotelId,
                "UF_DATE" => $arDatesFilter
            ],
        );

        $arResData = $entityClass->getDataAll();

        foreach ($arResData as $key => $arEntity) {
            $arResultRooms[$arEntity['UF_TARIFF_ID']][$arEntity['UF_CATEGORY_ID']][$arEntity['UF_DATE']->format("Y-m-d")] = $arEntity;
        }

        foreach ($arData["plans_data"] as $tariffId => $arCategories) {
            foreach ($arCategories as $categoryId => $arCategoryDates) {
                foreach ($arCategoryDates as $date => $arDate) {
                    $price = $arDate['price'];
                    $isReserved = ($arDate['closed']) ? 1 : 0;
                    $minStay = $arDate['min_stay'];
                    $maxStay = $arDate['max_stay'];
                    $closedArrival = $arDate['closed_arrival'];
                    $closedDeparture = $arDate['closed_departure'];
                    $minStayArrival = $arDate['min_stay_arrival'] != 0 ? $arDate['min_stay_arrival'] : 0;

                    $tmpRoom = [];
                    $arFields = [];
                    if (isset($arResultRooms[$tariffId][$categoryId][$date])) {
                        $tmpRoom = $arResultRooms[$tariffId][$categoryId][$date];
                        $entityId = $tmpRoom['ID'];
                        unset($tmpRoom['ID'], $tmpRoom['UF_TARIFF_ID'], $tmpRoom['UF_CATEGORY_ID'], $tmpRoom['UF_DATE']);

                        $arFields = [
                            "UF_PRICE" => $price,
                            "UF_CLOSED" => $isReserved,
                            "UF_MIN_STAY" => $minStay,
                            "UF_MAX_STAY" => $maxStay,
                            "UF_CLOSED_ARRIVAL" => $closedArrival,
                            "UF_CLOSED_DEPARTURE" => $closedDeparture,
                            "UF_MIN_STAY_ARRIVAL" => $minStayArrival,
                        ];

                        //if (array_diff($tmpRoom, $arFields)) {
                        $entityClass->update($entityId, $arFields);
                        //}
                    } else {
                        $arFields = [
                            "UF_HOTEL_ID" => $hotelId,
                            "UF_TARIFF_ID" => $tariffId,
                            "UF_CATEGORY_ID" => $categoryId,
                            "UF_DATE" => date('d.m.Y', strtotime($date)),
                            "UF_PRICE" => $price,
                            "UF_CLOSED" => $isReserved,
                            "UF_MIN_STAY" => $minStay,
                            "UF_MAX_STAY" => $maxStay,
                            "UF_CLOSED_ARRIVAL" => $closedArrival,
                            "UF_CLOSED_DEPARTURE" => $closedDeparture,
                            "UF_MIN_STAY_ARRIVAL" => $minStayArrival,
                        ];
                        $entityClass->add($arFields);
                    }
                }
            }
        }
    }

    /* Обновление наличия */
    public function updateAvailabilityData($hotelId, $arCategories, $arDates, $fromOrder = false)
    {
        $url = $this->bnovoApiURL . '/availability';
        $headers = array(
            "Content-Type: application/json"
        );

        sort($arDates);
        $dateFrom = $arDates[0];
        $dateTo = $arDates[count($arDates) - 1];
        if (is_array($arCategories)) {
            $roomTypes = $arCategories;
        } else {
            $roomTypes = [$arCategories];
        }


        $data = array(
            "token" => $this->token,
            "account_id" => $hotelId,
            "dfrom" => $dateFrom,
            "dto" => $dateTo,
            "roomtypes" => $roomTypes
        );

        //Debug::writeToFile($data, 'BNOVO_REQUEST_' . $hotelId . date('Y-m-d H:i:s'), '__BNOVO_REQUEST.log');

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($ch);
        $arData = json_decode($response, true);

        //Debug::writeToFile($arData, 'BNOVO_RESPONSE_' . $hotelId . date('Y-m-d H:i:s'), '__BNOVO_RESPONSE.log');

        if (empty($arData) || (isset($arData['code']) && $arData['code'] != 200)) {
            if ($arData['code'] == 403) {
                return 'Объект отключен от вашего канала продаж';
            } else {
                return 'Непредвиденная ошибка. Код ошибки - ' . $arData['code'] . '. Сообщение - ' . $arData['message'];
            }
        }

        if (!empty($arData) && empty($arData["availability"])) {
            return 'Пустой массив. По объекту нет доступных тарифов';
        }

        if ($fromOrder) {
            return $arData['availability'];
        }

        curl_close($ch);

        $entityClass = new HighLoadBlockHelper(self::$pricesHlCode);

        $arDatesFilter = [];
        $period = new DatePeriod(
            new DateTime($dateFrom),
            new DateInterval('P1D'),
            new DateTime(date('d.m.Y', strtotime($dateTo . '+1 day')))
        );
        foreach ($period as $key => $value) {
            $arDatesFilter[] = $value->format('d.m.Y');
        }

        $arReservedOne = [];
        $arReservedNull = [];

        foreach ($arData["availability"] as $categoryId => $arCategoryDates) {
            $arResultRoomsId = [];
            $arResultRooms = [];

            $entityClass->prepareParamsQuery(
                [
                    "ID",
                    "UF_RESERVED",
                    "UF_DATE",
                ],
                [
                    "ID" => "ASC"
                ],
                [
                    "=UF_HOTEL_ID" => $hotelId,
                    "=UF_CATEGORY_ID" => $categoryId,
                    "UF_DATE" => $arDatesFilter,
                ],
            );

            $arResData = $entityClass->getDataAll();

            if (empty($arResData)) {
                return 'Нет данных по объекту bnovoId: ' . $hotelId . ' bnovoCategoryId ' . $categoryId . ' на переданные в запросе даты';
            }
            foreach ($arResData as $key => $arEntity) {
                $arResultRoomsId[$arEntity['UF_DATE']->format("Y-m-d")][] = $arEntity['ID'];
                $arResultRooms[$arEntity['ID']] = $arEntity;
            }

            foreach ($arCategoryDates as $date => $isAvailable) {
                $arFields = [];
                if (isset($arResultRoomsId[$date])) {
                    $arFields = array(
                        'UF_RESERVED' => !empty($isAvailable) ? '0' : '1'
                    );

                    foreach ($arResultRoomsId[$date] as $id) {
                        if ($arFields["UF_RESERVED"] != $arResultRooms[$id]["UF_RESERVED"]) {
                            if ($arFields["UF_RESERVED"] == '1') {
                                $arReservedOne[] = $id;
                            } else {
                                $arReservedNull[] = $id;
                            }
                        }
                    }
                }
            }
        }

        $connection = \Bitrix\Main\Application::getConnection();

        if (!empty($arReservedOne)) {
            $query = 'UPDATE b_hlbd_room_offers SET UF_RESERVED=1 WHERE id IN (' . implode(',', $arReservedOne) . ')';

            //Debug::writeToFile($query, 'BNOVO_UNRESERVED_' . $hotelId . date('Y-m-d H:i:s'), '__BNOVO_UNRESERVED_log.log');

            $result = $connection->queryExecute($query);
        }

        if (!empty($arReservedNull)) {
            $query = 'UPDATE b_hlbd_room_offers SET UF_RESERVED=0 WHERE id IN (' . implode(',', $arReservedNull) . ')';

            //Debug::writeToFile($query, 'BNOVO_RESERVED_' . $hotelId . date('Y-m-d H:i:s'), '__BNOVO_RESERVED__log.log');

            $result = $connection->queryExecute($query);
        }
    }

    /* Бронирование объекта из заказа */
    public function makeReservation($orderId, $arOrder, $arUser, $reservationPropId)
    {
        $externalSectionId = $arOrder['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_ID'];
        $sectionId = $arOrder['ITEMS'][0]['ITEM']['SECTION']['ID'];
        $sectionName = $arOrder['ITEMS'][0]['ITEM']['SECTION']['NAME'];
        $dateFrom = $arOrder['PROPS']['DATE_FROM'];
        $dateTo = $arOrder['PROPS']['DATE_TO'];
        $guests = $arOrder["ITEMS"][0]["ITEM_BAKET_PROPS"]["GUESTS_COUNT"]['VALUE'];
        $children = !empty($arOrder['PROPS']['CHILDREN']) ? $arOrder['PROPS']['CHILDREN'] : 0;
        $price = $arOrder['FIELDS']['BASE_PRICE'];

        $url = $this->bnovoApiURL . '/channel_manager_bookings';
        $headers = array(
            "Content-Type: application/json"
        );
        $data = array(
            "token" => $this->token,
            "account_id" => $externalSectionId,
            "booking_data" => [
                //"ota_id" => $sectionId,
                "ota_id" => "naturalist",
                "ota_booking_id" => time(),
                "status_id" => 1,
                "name" => $arOrder['PROPS']["NAME"],
                "surname" => $arOrder['PROPS']["LAST_NAME"],
                "email" => $arOrder['PROPS']["EMAIL"],
                "phone" => $arOrder['PROPS']["PHONE"],
                "comment" => $arOrder['FIELDS']["USER_DESCRIPTION"],
                "room_types" => [
                    0 => [
                        "arrival" => date('Y-m-d', strtotime($dateFrom)),
                        "departure" => date('Y-m-d', strtotime($dateTo)),
                        "room_type_id" => $arOrder['PROPS']['CATEGORY_ID'],
                        "plan_id" => $arOrder['PROPS']['TARIFF_ID'],
                        "count" => 1,
                        "adults" => $guests,
                        "children" => $children,
                        "amount" => $arOrder['FIELDS']['BASE_PRICE'],
                        "prices" => unserialize($arOrder['PROPS']["PRICES"]),
                        "extra" => [
                            "Guests" => [
                                "List" => $arOrder['PROPS']['GUEST_LIST'],
                                "Number" => $guests
                            ],
                            "Ota info" => [
                                "info" => "Hotel info",
                                "Hotel id" => $sectionId,
                                "Hotel name" => $sectionName,
                                "Paid sum" => (int)$price,
                                "prepay" => 1,
                            ]
                        ]
                    ]
                ]
            ]
        );
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log_bnovo.txt', json_encode($data) . PHP_EOL, FILE_APPEND);
        file_put_contents(
            $_SERVER["DOCUMENT_ROOT"] . '/log_bnovo_order.txt',
            json_encode($arOrder) . PHP_EOL,
            FILE_APPEND
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($data)
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);

        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log_bnovo_responce.txt', 'Номер заказа: ' . $orderId . PHP_EOL, FILE_APPEND);
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log_bnovo_responce.txt', $response . PHP_EOL, FILE_APPEND);

        if ($arResponse['created_bookings'][0]['ota_booking_id']) {
            // Сохраняем ID бронирования в заказе
            $reservationId = $arResponse['created_bookings'][0]['ota_booking_id'];

            $order = Order::load($orderId);
            $propertyCollection = $order->getPropertyCollection();
            $propertyValue = $propertyCollection->getItemByOrderPropertyId($reservationPropId);
            $propertyValue->setValue($reservationId);
            $res = $order->save();

            if ($res->isSuccess()) {
                return $reservationId;
            } else {
                return [
                    "ERROR" => "Ошибка сохранения ID бронирования."
                ];
            }
        } else {
            return [
                "ERROR" => "Ошибка запроса бронирования."
            ];
        }
    }

    /* Отмена бронирования объекта из заказа  */
    public function cancelReservation($arOrder)
    {
        $reservationId = $arOrder['PROPS']['RESERVATION_ID'];
        $externalSectionId = $arOrder['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_ID'];
        $sectionId = $arOrder['ITEMS'][0]['ITEM']['SECTION']['ID'];
        $sectionName = $arOrder['ITEMS'][0]['ITEM']['SECTION']['NAME'];
        $dateFrom = $arOrder['PROPS']['DATE_FROM'];
        $dateTo = $arOrder['PROPS']['DATE_TO'];
        $guests = $arOrder['PROPS']['GUESTS_COUNT'];
        $children = !empty($arOrder['PROPS']['CHILDREN_AGE']) ? count($arOrder['PROPS']['CHILDREN_AGE']) : 0;

        $url = $this->bnovoApiURL . '/channel_manager_bookings';
        $headers = array(
            "Content-Type: application/json"
        );
        $data = array(
            "token" => $this->token,
            "account_id" => $externalSectionId,
            "booking_data" => [
                "ota_id" => "naturalist",
                "ota_booking_id" => $reservationId,
                "status_id" => 2,
                "name" => $arOrder['PROPS']["NAME"],
                "surname" => $arOrder['PROPS']["LAST_NAME"],
                "email" => $arOrder['PROPS']["EMAIL"],
                "phone" => $arOrder['PROPS']["PHONE"],
                "comment" => $arOrder['FIELDS']["USER_DESCRIPTION"],
                "room_types" => [
                    0 => [
                        "arrival" => date('Y-m-d', strtotime($dateFrom)),
                        "departure" => date('Y-m-d', strtotime($dateTo)),
                        "room_type_id" => $arOrder['PROPS']["CATEGORY_ID"],
                        "plan_id" => $arOrder['PROPS']["TARIFF_ID"],
                        "count" => 1,
                        "adults" => $guests,
                        "children" => $children,
                        "amount" => $arOrder['FIELDS']['PRICE'],
                        "prices" => unserialize($arOrder['PROPS']["PRICES"]),
                        "extra" => [
                            "Guests" => [
                                "List" => $arOrder['PROPS']['GUEST_LIST'],
                                "Number" => $guests
                            ],
                            "Ota info" => [
                                "info" => "Hotel info",
                                "Hotel id" => $sectionId,
                                "Hotel name" => $sectionName
                            ]
                        ]
                    ]
                ]
            ]
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($data)
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/log_bnovo.txt', $response . PHP_EOL, FILE_APPEND);

        if ($arResponse['canceled_bookings'][0]['ota_booking_id'] == $reservationId) {
            return true;
        } else {
            return false;
        }
    }

    public static function deleteOldPrices()
    {
        $hlEntity = new HighLoadBlockHelper(self::$pricesHlCode);
        $today = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime('-1 days'))->format("d.m.Y");

        $hlEntity->prepareParamsQuery(
            ["ID"],
            ["ID" => "ASC"],
            ["<UF_DATE" => $today],
        );

        $rows = $hlEntity->getDataAll();

        if (is_array($rows) && count($rows)) {
            foreach ($rows as $key => $row) {
                $hlEntity->delete($row['ID']);
            }
        }
    }

    private static function getEntityClass($hlId = 11)
    {
        Loader::IncludeModule('highloadblock');
        $hlblock = HighloadBlockTable::getById($hlId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }

    private static function getEntityCollection($hlId = 11)
    {
        Loader::IncludeModule('highloadblock');
        $hlblock = HighloadBlockTable::getById($hlId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity;
    }

    private function sendMessage($arSend)
    {
        \CEvent::Send($this->sendEventName, SITE_ID, $arSend);
    }

    private function writeToFile($data, $function, $hotelId)
    {
        if (is_array($data)) {
            $importFilePath = $_SERVER["DOCUMENT_ROOT"] . '/import/bnovo/answers/' . $function . '_hotelid_' . $hotelId . '_date_' . date("j-m-Y-H-i-s") . '.json';

            $fp = fopen($importFilePath, 'w+');
            fwrite($fp, json_encode($data));
            fclose($fp);
        }
    }

    /**
     * Возвращает все разделы (отели) Bnovo
     * 
     * @param string $limit Ограничение элементов.
     * @param string $offset С какого элемента выбирать.
     * @param string $countTotal Подсчёт общего количества.
     * @param boolean $raw Вернуть не обработанный объект.
     * 
     * @return array
     * 
     */
    private function getSections($limit = '', $offset = '', $countTotal = '', $raw = false)
    {
        $entity = \Bitrix\Iblock\Model\Section::compileEntityByIblock(CATALOG_IBLOCK_ID);
        $rsSectionObjects = $entity::getList(
            [
                'filter' => ['IBLOCK_ID' => CATALOG_IBLOCK_ID, 'ACTIVE' => 'Y', 'UF_EXTERNAL_SERVICE' => $this->bnovoSectionPropEnumId],
                'select' => ['ID', 'NAME', 'UF_EXTERNAL_UID', 'UF_EXTERNAL_ID'],
                'limit' => $limit,
                'offset' => $offset,
                'count_total' => $countTotal
            ]
        );

        if ($raw) {
            return $rsSectionObjects;
        }

        return $rsSectionObjects->fetchAll();
    }

    /**
     * Обновляет тарифы по всем объектам
     *
     * @return array
     * 
     */
    public function updateTariffs()
    {
        $sections = $this->getSections();

        if (!empty($sections)) {
            foreach ($sections as $section) {
                $this->updatePublicObject($section['UF_EXTERNAL_UID'], false, true);
            }
            echo 'Тарифы обновлены';
        }
    }

    /**
     * Загружает цены за 2 ближайших месяца
     *
     * @return void
     * 
     */
    public function getNearPrices(): void
    {
        return;
        $now = new DateTime();
        $startDate = FormatDate("Y-m-d", $now->getTimeStamp());

        $future = new DateTime(date('Y-m-d', strtotime($startDate . '+90 day')));
        $endDate = FormatDate("Y-m-d", $future->getTimeStamp());

        $lastDownLoad = Option::get("main", "bnovo_last_download");
        if ($lastDownLoad == 'Y') {
            return;
        }

        $bnovoElementOffset = Option::get("main", "bnovo_element_offset");
        $bnovoElementLimit = Option::get("main", "bnovo_element_limit");
        $bnovoElementCount = $this->getSections(1, 0, 1, true)->getCount();

        Option::set("main", "bnovo_element_count", $bnovoElementCount);

        if ($bnovoElementOffset == '') {
            $bnovoElementOffset = 0;
            Option::set("main", "bnovo_element_offset", $bnovoElementOffset);
        }

        if ($bnovoElementLimit == '') {
            $bnovoElementLimit = 10;
            Option::set("main", "bnovo_element_limit", $bnovoElementLimit);
        }

        $sections = $this->getSections($bnovoElementLimit, $bnovoElementOffset, 1);

        $nextOffset = $bnovoElementOffset + $bnovoElementLimit;

        if ($nextOffset >= $bnovoElementCount) {
            $nextOffset = 0;
            Option::set("main", "bnovo_last_download", 'Y');
        }

        Option::set("main", "bnovo_element_offset", $nextOffset);

        if (!empty($sections)) {
            foreach ($sections as $section) {
                $this->updateReservationData($section['UF_EXTERNAL_ID'], [], [], [$startDate, $endDate]);
                $this->updateAvailabilityData($section['UF_EXTERNAL_ID'], [], [$startDate, $endDate]);
            }
        }
    }

    /**
     * Проверяет все объекты Бново на подключение к каналу продаж
     *
     * @return void
     * 
     */
    public function checkDisabledBnovoObjects(): void
    {
        $sections = $this->getSections();
        $message = '';

        $now = new DateTime();
        $startDate = FormatDate("Y-m-d", $now->getTimeStamp());

        $future = new DateTime(date('Y-m-d', strtotime($startDate . '+1 day')));
        $endDate = FormatDate("Y-m-d", $future->getTimeStamp());

        if (!empty($sections)) {
            foreach ($sections as $section) {
                $result = $this->updateAvailabilityData($section['UF_EXTERNAL_ID'], [], [$startDate, $endDate], true);
                if (!is_array($result)) {
                    $message .= "Ошибка при проверке данных по объекту " . $section['NAME'] . "\r\n";
                    $message .= "Текст ошибки: " . $result . "\r\n\r\n";
                }
            }
        }

        if ($message != '') {
            \CEvent::Send('BNOVO_DATA_ERROR', 's1', [
                'MESSAGE' => $message,
            ]);
        }
    }

    /**
     * Проверяет наличие наценок для номеров объекта
     *
     * @return void
     * 
     */
    public function checkMarkups(): void
    {
        $sections = $this->getSections();
        $message = '';

        if (!empty($sections)) {
            foreach ($sections as $section) {
                $rooms = $this->getRoomsFromApi($section['UF_EXTERNAL_ID']);
                if (!empty($rooms)) {
                    foreach ($rooms as $room) {
                        if (isset($room['extra_array']['people']) && count($room['extra_array']['people']) && isset($room['extra_array']['children_ages']) && count($room['extra_array']['children_ages'])) {
                            $arRoomsRequest = \Bitrix\Iblock\Elements\ElementCategoriesTable::getList([
                                'select' => ['ID', 'NAME', 'EXTERNAL_ID_' => 'EXTERNAL_ID'],
                                'filter' => ['=EXTERNAL_ID.VALUE' => $room['id']],
                            ])->fetch();

                            if ($arRoomsRequest['ID']) {
                                $arAccRequest = \Bitrix\Iblock\Elements\ElementOccupanciesTable::getList([
                                    'select' => ['ID', 'NAME', 'CATEGORY_ID'],
                                    'filter' => ['CATEGORY_ID.VALUE' => $arRoomsRequest['ID'], 'IS_MARKUP.VALUE' => 17],
                                ])->fetch();
                                if (!$arAccRequest['ID']) {
                                    $message .= "У объекта " . $section['NAME'] . " доступны наценки \r\n";
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($message != '') {
            \CEvent::Send('BNOVO_MARKUP', 's1', [
                'MESSAGE' => $message,
            ]);
        }

        Option::set("main", "bnovo_last_download", 'N');
    }

    /**
     * Возвращает массив номеров объекта
     *
     * @param int $hotelId
     * 
     * @return array
     * 
     */
    private function getRoomsFromApi(int $hotelId): array
    {
        $result = [];
        $url = $this->bnovoApiPublicURL . '/roomtypes';
        $headers = array(
            "Content-Type: application/json"
        );

        $data = array(
            "account_id" => $hotelId,
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url . '?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers
        ));
        $response = curl_exec($ch);
        $arData = json_decode($response, true);
        $result = $arData['rooms'];
        curl_close($ch);

        return $result;
    }
}
