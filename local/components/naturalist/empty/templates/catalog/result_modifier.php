<?php

use Bitrix\Main\Web\Uri;
use Naturalist\Products;
use Naturalist\Regions;

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
    "arSearchedRegions" => $arParams['arSearchedRegions'] ?? false,
);


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

                $minimalDisctanceRegionId = $regionHLData['ID'];
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
                if($arExternalIDs) {
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
            $arFilter = array(
                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                "ACTIVE" => "Y"
            );
            $arFilter["UF_REGION"] = $minimalDisctanceRegionId;

            //список кемпингов по региону
            $rsSections = CIBlockSection::GetList($arSort, $arFilter, false, array("IBLOCK_ID", "ID", "NAME", "CODE", "SECTION_PAGE_URL", "UF_*"), false);
            $arSections = array();
            while ($arSection = $rsSections->GetNext()) {
                $arDataFullGallery = [];
                if ($arSection["UF_PHOTOS"]) {
                    foreach ($arSection["UF_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "&quot;" . $imageOriginal["SRC"] . "&quot;";
                        $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 600, 'height' => 360), BX_RESIZE_IMAGE_EXACT, true);
                    }

                } else {
                    $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/no_photo.png";
                }

                $arSection["FULL_GALLERY"] = implode(",", $arDataFullGallery);

                $arSection["RATING"] = (isset($arReviewsAvg[$arSection["ID"]])) ? $arReviewsAvg[$arSection["ID"]] : 0;
                $arSection["REVIEWS_COUNT"] = (isset($arReviews[$arSection["ID"]])) ? count($arReviews[$arSection["ID"]]) : 0;

                if ($arSection["UF_COORDS"]) {
                    $arSection["COORDS"] = explode(',', $arSection["UF_COORDS"]);
                }

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

                $arUriParams = array_merge($arUriParams, $arUriParamsSort);

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
    }
}


