<?php

use Bitrix\Main\Web\Uri;
use Naturalist\Morpher;
use Naturalist\Products;
use Naturalist\Regions;
use Naturalist\Utils;

$arResult = array(
    "sortBy" => $arParams['sortBy'],
    "orderReverse" => $arParams['orderReverse'],
    "page" => $arParams['page'],
    "pageCount" => $arParams['pageCount'],
    "allCount" => $arParams['allCount'],
    "countDeclension" => $arParams['countDeclension'],
    "reviewsDeclension" => $arParams['reviewsDeclension'],
    "arPageSections" => $arParams['arPageSections'],
    "arReviewsAvg" => $arParams['arReviewsAvg'],
    "arFavourites" => $arParams['arFavourites'],
    "arHLTypes" => $arParams['arHLTypes'],
    "arHLFeatures" => $arParams['arHLFeatures'],
    "arServices" => $arParams['arServices'],
    "arSearchedRegions" => $arParams['arSearchedRegions'] ?? false,
    "searchedRegionData" => $arParams['searchedRegionData'] ?? false,
    "searchName" => $arParams['searchName'] ?? '',
    "arFilterValues" => $arParams['arFilterValues'],
    "dateFrom" => $arParams['dateFrom'],
    "dateTo" => $arParams['dateTo'],
    "arDates" => $arParams['arDates'],
    "currMonthName" => $arParams['currMonthName'],
    "currYear" => $arParams['currYear'],
    "nextYear" => $arParams['nextYear'],
    "guests" => $arParams['guests'],
    "children" => $arParams['children'],
    "guestsDeclension" => $arParams['guestsDeclension'],
    "arChildrenAge" => $arParams['arChildrenAge'],
    "itemsCount" => $arParams["itemsCount"],
    "filterData" => $arParams["filterData"],
    "arHouseTypes" => $houseTypeData,
    "arHLTypes" => $arParams["arHLTypes"],
    "arFilterTypes" => $arParams["arFilterTypes"],
);

if (is_array($arParams["arFilterTypes"]) && count($arParams["arFilterTypes"]) == 1) {
    $arResult['filteredHouseType'] = $arParams["arHLTypes"][$arParams["arFilterTypes"][0]]['UF_NAME'];
}

// Выборка по наиболее близким координатам
if ($arResult['arSearchedRegions']) {
    //список регионов
    $arRegionList = Regions::getRegionList($arResult['arSearchedRegions']);

    $arResult['SECTIONS'] = [];
    foreach ($arResult['arSearchedRegions'] as $regionId) {
        $arRegionData = Regions::getRegionById($regionId);
        if (empty($arRegionData["UF_COORDS"])) {
            continue;
        }

        $currentRegionCoords = explode(",", $arRegionData["UF_COORDS"]);

        $disctanceToCurrent = 999999;
        $minimalDisctanceRegion = 0;
        foreach ($arRegionList as $regionHLData) {

            if (empty($regionHLData["UF_COORDS"])) {
                continue;
            }

            $regionHLCoords = explode(",", $regionHLData["UF_COORDS"]);
            //квадрат расстояния между отрезками
            $disctanceToCurrentSqrt = sqrt((($currentRegionCoords[0] - $regionHLCoords[0]) * ($currentRegionCoords[0] - $regionHLCoords[0])) + (($currentRegionCoords[1] - $regionHLCoords[1]) * ($currentRegionCoords[1] - $regionHLCoords[1])));

            if ($disctanceToCurrentSqrt < $disctanceToCurrent) {
                $disctanceToCurrent = $disctanceToCurrentSqrt;

                $minimalDisctanceRegionId[] = $regionHLData['ID'];
            }
        }

        //получения даных для блока
        if ($minimalDisctanceRegionId > 0) {

            // Заезд, выезд, кол-во гостей
            $dateFrom = $_GET['dateFrom'];
            $dateTo = $_GET['dateTo'];
            $guests = $_GET['guests'] ?? 2;
            $children = $_GET['children'] ?? 0;
            $arChildrenAge = (isset($_GET['childrenAge'])) ? explode(',', $_GET['childrenAge']) : [];
            $arUriParams = array(
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'guests' => $guests,
                'children' => $children,
                'childrenAge' => $arChildrenAge,
            );

            if (!empty($dateFrom) && !empty($dateTo) && !empty($_GET['guests'])) {
                $daysCount = abs(strtotime($dateTo) - strtotime($dateFrom)) / 86400;

                // Запрос в апи на получение списка кемпингов со свободными местами в выбранный промежуток
                $arExternalInfo = Products::search($guests, $arChildrenAge, $dateFrom, $dateTo, false);
                $arExternalIDs = array_keys($arExternalInfo);
                if ($arExternalIDs) {
                    $arFilter["UF_EXTERNAL_ID"] = $arExternalIDs;
                } else {
                    $arFilter["UF_EXTERNAL_ID"] = false;
                }
            }


            /* Сортировка */
            $sortBy = (!empty($_GET['sort']) && isset($_GET['sort'])) ? strtolower($_GET['sort']) : "sort";
            $sortOrder = (!empty($_GET['order']) && isset($_GET['order'])) ? strtolower($_GET['order']) : "asc";
            $orderReverse = (!empty($_GET['order']) && isset($_GET['order']) && $_GET['order'] == 'asc') ? "desc" : "asc";
            switch ($sortBy) {
                case 'popular':
                    $sort = 'UF_RESERVE_COUNT';
                    break;

                default:
                    $sort = 'SORT';
                    break;
            }
            $arSort = array($sort => $sortOrder);


            /* Отзывы */
            $rsReviews = CIBlockElement::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => REVIEWS_IBLOCK_ID, "ACTIVE" => "Y"), false, false, array("ID", "PROPERTY_CAMPING_ID", "PROPERTY_RATING"));
            $arReviews = array();
            while ($arReview = $rsReviews->Fetch()) {
                $arReviews[$arReview["PROPERTY_CAMPING_ID_VALUE"]][$arReview["ID"]] = $arReview["PROPERTY_RATING_VALUE"];
            }
            $arReviewsAvg = array_map(function ($a) {
                return round(array_sum($a) / count($a), 1);
            }, $arReviews);


            //фильр по региону                
            $arFilter["IBLOCK_ID"] = CATALOG_IBLOCK_ID;
            $arFilter["ACTIVE"] = "Y";
            $arFilter["UF_REGION"] = $minimalDisctanceRegionId;

            /** получение сезона из ИБ */
            if (Cmodule::IncludeModule('asd.iblock')) {
                $arFields = CASDiblockTools::GetIBUF(CATALOG_IBLOCK_ID);
            }

            //список кемпингов по региону
            $rsSections = CIBlockSection::GetList($arSort, $arFilter, false, array("IBLOCK_ID", "ID", "NAME", "CODE", "SECTION_PAGE_URL", "UF_*"), false);
            $arSections = array();
            while ($arSection = $rsSections->GetNext()) {
                $arDataFullGallery = [];

                foreach($arFields['UF_SEASON'] as $season){
                    if($season == 'Лето'){
                        if ($arSection["UF_PHOTOS"]) {
                            foreach ($arSection["UF_PHOTOS"] as $photoId) {
                                $imageOriginal = CFile::GetFileArray($photoId);
                                $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                            }
                        } else {
                            $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                        }
                    }elseif($season == 'Зима'){
                        if ($arSection["UF_WINTER_PHOTOS"]) {
                            foreach ($arSection["UF_WINTER_PHOTOS"] as $photoId) {
                                $imageOriginal = CFile::GetFileArray($photoId);
                                $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                            }
                        } else {
                            if ($arSection["UF_PHOTOS"]) {
                                foreach ($arSection["UF_PHOTOS"] as $photoId) {
                                    $imageOriginal = CFile::GetFileArray($photoId);
                                    $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                                    $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                                }
                            } else {
                                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                            }
                        }
                    }elseif($season == 'Осень+Весна'){
                        if ($arSection["UF_MIDSEASON_PHOTOS"]) {
                            foreach ($arSection["UF_MIDSEASON_PHOTOS"] as $photoId) {
                                $imageOriginal = CFile::GetFileArray($photoId);
                                $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                            }
                        } else {
                            if ($arSection["UF_PHOTOS"]) {
                                foreach ($arSection["UF_PHOTOS"] as $photoId) {
                                    $imageOriginal = CFile::GetFileArray($photoId);
                                    $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                                    $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                                }
                            } else {
                                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                            }
                        }
                    }
                }
                unset($arSection["UF_PHOTOS"]);
                $arSection["FULL_GALLERY"] = implode(",", $arDataFullGallery);

                $arSection["RATING"] = (isset($arReviewsAvg[$arSection["ID"]])) ? $arReviewsAvg[$arSection["ID"]] : 0;
                $arSection["REVIEWS_COUNT"] = (isset($arReviews[$arSection["ID"]])) ? count($arReviews[$arSection["ID"]]) : 0;

                if ($arSection["UF_COORDS"]) {
                    $arSection["COORDS"] = explode(',', $arSection["UF_COORDS"]);
                }


                /* Растояние до поискового запроса */
                if ($arResult['searchedRegionData']) {
                    $searchedRegionData['COORDS'] = explode(',', $arResult['searchedRegionData']['UF_COORDS']);

                    $arSection['DISCTANCE'] = Utils::calculateTheDistance($searchedRegionData['COORDS'][0], $searchedRegionData['COORDS'][1], $arSection['COORDS'][0], $arSection['COORDS'][1]);
                    $arSection['DISCTANCE_TO_REGION'] = Utils::morpher($arResult['searchedRegionData']['CENTER_UF_NAME'], Morpher::CASE_GENITIVE);
                    $arSection['DISCTANCE_TO_REGION'] = ucfirst($arSection['DISCTANCE_TO_REGION']);
                }
                /* до 500км */
                if ((int)$arSection['DISCTANCE'] > 500) {
                    continue;
                }

                /* -- */

                if ($arExternalInfo) {
                    $sectionPrice = $arExternalInfo[$arSection["UF_EXTERNAL_ID"]];
                    // Если это Traveline, то делим цену на кол-во дней
                    if ($arSection["UF_EXTERNAL_SERVICE"] == 1) {
                        $sectionPrice = round($sectionPrice / $daysCount);
                    }
                } else {
                    $sectionPrice = $arSection["UF_MIN_PRICE"];
                }
                $arSection["PRICE"] = $sectionPrice;

                $arUriParamsSort = array(
                    'sort' => $sortBy,
                    'order' => $sortOrder,
                );

                if ($dateFrom) {
                    $arUriParams = array_merge($arUriParams, $arUriParamsSort);
                }

                $uri = new Uri($arSection["SECTION_PAGE_URL"]);
                $uri->addParams($arUriParams);
                $sectionUrl = $uri->getUri();
                $arSection["URL"] = $sectionUrl;

                $arButtons = CIBlock::GetPanelButtons($arSection["IBLOCK_ID"], $arSection["ID"], 0, array("SECTION_BUTTONS" => false, "SESSID" => false));
                $arSection["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                $arSection["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

                $arResult['SECTIONS'][$arSection["ID"]] = $arSection;
            }
        }

        usort($arResult['SECTIONS'], function ($a, $b) {
            return ($a['DISCTANCE'] - $b['DISCTANCE']);
        });
    }
}
