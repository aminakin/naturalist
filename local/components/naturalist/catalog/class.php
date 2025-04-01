<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Grid\Declension;
use Bitrix\Main\Web\Uri;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\Elements\ElementObjectfaqTable;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Iblock\Elements\ElementFeaturesdetailTable;
use Bitrix\Main\Data\Cache;
use Naturalist\Products;
use Naturalist\Filters\Components;
use Naturalist\Users;
use Naturalist\Regions;
use Naturalist\Utils;
use Naturalist\Filters\UrlHandler;
use Naturalist\Reviews;

Loc::loadMessages(__FILE__);

/**
 * Компонент каталога
 */
class NaturalistCatalog extends \CBitrixComponent
{
    private string $componentPage = '';
    const SEF_URL_TEMPLATES = [
        "news" => "",
        "search" => "search/",
        "rss" => "rss/",
        "rss_section" => "#SECTION_ID#/rss/",
        "detail" => "#ELEMENT_ID#/",
        "section" => "",
    ];
    const COMPONENT_VARIABLES = [
        "SECTION_ID",
        "SECTION_CODE",
        "ELEMENT_ID",
        "ELEMENT_CODE",
    ];

    private $daysCount = 0;
    private $filterCount = 0;
    private $page = 0;
    private $minPrice = 0;
    private $maxPrice = 0;
    private $reviewsCount = 0;
    private $avgRating = 0;
    private $reviewsPage = 0;
    private $reviewsPageCount = 0;
    private $allCount = 0;
    private $pageCount = 0;
    private $isUserReview = 'N';
    private $searchError = '';
    private $sortBy = '';
    private $sortOrder = '';
    private $orderReverse = '';
    private $titleSEO = '';
    private $descriptionSEO = '';
    private $h1SEO = '';
    private $search = '';
    private $searchName = '';
    private $reviewsSortType = '';
    private $arUriParams = [];
    private $filterParams = [];
    private $arFilter = [];
    private $arFilterValues = [];
    private $arRegionIds = [];
    private $arSeoImpressions = [];
    private $arSort = [];
    private $season = [];
    private $arExternalInfo = [];
    private $arExternalAvail = [];
    private $arSections = [];
    private $arHLTypes = [];
    private $houseTypeData = [];
    private $water = [];
    private $commonWater = [];
    private $difFilter = [];
    private $restVariants = [];
    private $objectComforts = [];
    private $arHLFeatures = [];
    private $arServices = [];
    private $arHLFood = [];
    private $pageSeoData = [];
    private $arReviews = [];
    private $arReviewsAvg = [];
    private $arAvgCriterias = [];
    private $arReviewsUsers = [];
    private $arReviewsLikesData = [];
    private $arElements = [];
    private $arElementsParent = [];
    private $arHLRoomFeatures = [];
    private $arHouseTypes = [];
    private $arObjectComforts = [];
    private $arHLFeaturesIds = [];
    private $arObjectComfortsIds = [];
    private $chpy;
    private $searchedRegionData;
    private $arDates;
    private $currMonthName;
    private $currYear;
    private $nextYear;
    private $daysRange;


    private function fillSectionVariables()
    {
        $this->fillUriParams();

        $this->filterParams = [
            'types' => $this->request->get('types'),
            'services' => $this->request->get('services'),
            'food' => $this->request->get('food'),
            'features' => $this->request->get('features'),
            'restvariants' => $this->request->get('restvariants'),
            'objectcomforts' => $this->request->get('objectcomforts'),
            'housetypes' => $this->request->get('housetypes'),
            'water' => $this->request->get('water'),
            'commonwater' => $this->request->get('commonwater'),
            'sitemap' => $this->request->get('sitemap'),
            'selection' => $this->request->get('selection'),
            'diffilter' => $this->request->get('diffilter'),
            'impressions' => $this->request->get('impressions'),
        ];
        $this->page = $this->request->get('page') ?? 1;
        $this->chpy = Components::getChpyLinkByUrl($_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL'] : $_SERVER['SCRIPT_NAME']);
        $this->setSectionFilters();
        $this->getHlBlocks();
        $this->setSort();
        $this->getSeason();
        $this->getExternalData();
        $this->fillSections();
        $this->fillRating();
        $this->applySort();
        $this->setPageSeoData();
        $this->makeCalendar();
    }

    private function fillUriParams()
    {
        $this->arUriParams = [
            'dateFrom' => $this->request->get('dateFrom'),
            'dateTo' => $this->request->get('dateTo'),
            'guests' => $this->request->get('guests') ? $this->request->get('guests') : 2,
            'children' => $this->request->get('children') ? $this->request->get('children') : 0,
        ];


        $this->arUriParams['childrenAge'] = [];
        if ($this->request->get('childrenAge')) {
            if (is_array($this->request->get('childrenAge'))) {

                $this->arUriParams['childrenAge'] = $this->request->get('childrenAge');
            }

            if (is_string($this->request->get('childrenAge'))) {
                $this->arUriParams['childrenAge'] = explode(',', $this->request->get('childrenAge'));
            }
        }
    }

    private function makeCalendar()
    {
        /* Генерация массива месяцев для фильтра */
        $this->arDates = Products::getDates();
        $this->currMonthName = FormatDate("f");
        $this->currYear = date('Y');
        $this->nextYear = $this->currYear + 1;
    }

    private function setPageSeoData()
    {
        if ($this->request->get('page') && count($_GET) > 1) {
            $urlWithPage = '/catalog/?';
            foreach ($_GET as $getName => $getValue) {
                if ($getName !== 'page') {
                    $urlWithPage .= $getName . '=' . $getValue . '&';
                }
            }
            $urlWithPage = substr($urlWithPage, 0, -1);
            $this->pageSeoData = UrlHandler::getByRealUrl($urlWithPage, SITE_ID);
        }

        /* Генерация SEO */
        global $APPLICATION;
        $metaTags = getMetaTags();
        $currentURLDir = $APPLICATION->GetCurDir();

        if ($this->page > 1 && !empty($this->request->get("impressions")) && !empty($metaTags["/catalog/?page=2&impressions"])) { //переход с раздела "Впечатления" с пагинацией
            if (!empty($this->arSeoImpressions)) {
                $impressionReplace = $this->arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] ? $this->arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] : $this->arSeoImpressions[0]["NAME"];
            } else {
                $impressionReplace = "";
            }
            $this->titleSEO = $this->arSeoImpressions[0]["META"]['ELEMENT_META_TITLE'] . ' Страница - ' . $this->page;
            $this->descriptionSEO = $this->arSeoImpressions[0]["META"]['ELEMENT_META_DESCRIPTION'] . ' Страница - ' . $this->page;;
            $this->h1SEO = $impressionReplace;
        } elseif (!empty($this->request->get("impressions")) && !empty($metaTags["/catalog/?page=2&impressions"])) { //переход с раздела "Впечатления"
            if (!empty($this->arSeoImpressions)) {
                $impressionReplace = $this->arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] ? $this->arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] : $this->arSeoImpressions[0]["NAME"];
            } else {
                $impressionReplace = "";
            }
            $this->titleSEO = $this->arSeoImpressions[0]["META"]['ELEMENT_META_TITLE'];
            $this->descriptionSEO = $this->arSeoImpressions[0]["META"]['ELEMENT_META_DESCRIPTION'];
            $this->h1SEO = $impressionReplace;
        } elseif ($this->page > 1 && !empty($metaTags["/catalog/?page=2"])) { //страницы пагинации
            $this->titleSEO = str_replace("#PAGE#", $this->page, $metaTags["/catalog/?page=2"]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
            $this->descriptionSEO = str_replace("#PAGE#", $this->page, $metaTags["/catalog/?page=2"]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"]);
            $this->h1SEO = str_replace("#PAGE#", $this->page, $metaTags["/catalog/?page=2"]["~PROPERTY_H1_VALUE"]["TEXT"]);
        } elseif (!empty($metaTags[$currentURLDir])) {
            $this->titleSEO = $metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"];
            $this->descriptionSEO = $metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"];
            $this->h1SEO = $metaTags[$currentURLDir]["~PROPERTY_H1_VALUE"]["TEXT"];
        } else {
            $this->titleSEO = "Каталог - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист";
            $this->descriptionSEO = "Каталог | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования.";
            $this->h1SEO = "Карта глэмпингов в России";
        }
    }

    private function getHlBlocks()
    {
        // Тип объекта
        $objectTypesClass = HighloadBlockTable::compileEntity(TYPES_HL_ENTITY)->getDataClass();
        $objectTypesData = $objectTypesClass::getList([
            "select" => ["*"],
            "order" => ["UF_SORT" => "ASC"],
            "filter" => ["UF_SHOW_FILTER" => "1"],
            'cache' => ['ttl' => 36000000],
        ]);
        while ($arEntity = $objectTypesData->Fetch()) {
            $this->arHLTypes[$arEntity["ID"]] = $arEntity;
        }

        // Питание
        $foodClass = HighloadBlockTable::compileEntity(FOOD_HL_ENTITY)->getDataClass();
        $foodData = $foodClass::getList([
            "select" => ["*"],
            "order" => ["UF_SORT" => "ASC"],
            "filter" => ["UF_SHOW_FILTER" => "1"],
            'cache' => ['ttl' => 36000000],
        ]);
        while ($arEntity = $foodData->Fetch()) {
            $this->arHLFood[$arEntity["ID"]] = $arEntity;
        }

        // Типы домов
        $houseTypesDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();
        $this->houseTypeData = $houseTypesDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ->setCacheTtl(36000000)
            ?->fetchAll();

        if (!empty($this->houseTypeData)) {
            foreach ($this->houseTypeData as $key => &$houseType) {
                $houseType['URL'] = Components::getChpyLink(SUIT_TYPES_HL_ENTITY . '_' . $houseType['ID'])['UF_NEW_URL'];
            }
            unset($houseType);
        }

        // Водоёмы
        $waterDataClass = HighloadBlockTable::compileEntity(WATER_HL_ENTITY)->getDataClass();
        $this->water = $waterDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ->setCacheTtl(36000000)
            ?->fetchAll();

        // Общие водоёмы
        $commonWaterDataClass = HighloadBlockTable::compileEntity(COMMON_WATER_HL_ENTITY)->getDataClass();
        $this->commonWater = $commonWaterDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ->setCacheTtl(36000000)
            ?->fetchAll();

        // Разные фильтры
        $difFilterDataClass = HighloadBlockTable::compileEntity(DIFFERENT_FILTERS_HL_ENTITY)->getDataClass();
        $this->difFilter = $difFilterDataClass::query()
            ->addSelect('*')
            ->setCacheTtl(36000000)
            ?->fetchAll();

        // Варианты отдыха
        $restVariantsDataClass = HighloadBlockTable::compileEntity(REST_VARS_HL_ENTITY)->getDataClass();
        $this->restVariants = $restVariantsDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ->setCacheTtl(36000000)
            ?->fetchAll();

        // Удобства
        $objectComfortsDataClass = HighloadBlockTable::compileEntity(OBJECT_COMFORT_HL_ENTITY)->getDataClass();
        $this->objectComforts = $objectComfortsDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ->setCacheTtl(36000000)
            ?->fetchAll();

        // Особенности объекта        
        $entityClass = HighloadBlockTable::compileEntity(FEATURES_HL_ENTITY)->getDataClass();
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "order" => ["UF_SORT" => "ASC"],
            'cache' => ['ttl' => 36000000],

        ]);
        while ($arEntity = $rsData->Fetch()) {
            $this->arHLFeatures[$arEntity["ID"]] = $arEntity;
        }

        // Услуги
        $rsServices = CIBlockElement::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_SHOW_FILTER_VALUE" => "Y"), false, false, array("ID", "IBLOCK_ID", "NAME", "CODE"));
        while ($arService = $rsServices->Fetch()) {
            $this->arServices[$arService["ID"]] = $arService;
        }
    }

    private function getExternalData()
    {
        if (!empty($this->arUriParams['dateFrom']) && !empty($this->arUriParams['dateTo']) && !empty($this->arUriParams['guests'])) {
            $this->daysCount = abs(strtotime($this->arUriParams['dateTo']) - strtotime($this->arUriParams['dateFrom'])) / 86400;

            $cache = Cache::createInstance();
            $cacheKey = $this->arUriParams['dateFrom'] . $this->arUriParams['dateTo'] . $this->arUriParams['guests'];

            if ($cache->initCache(3600, $cacheKey)) {
                $this->arExternalInfo = $cache->getVars();
            } elseif ($cache->startDataCache()) {
                // Запрос в апи на получение списка кемпингов со свободными местами в выбранный промежуток
                $this->arExternalInfo = Products::search($this->arUriParams['guests'], $this->arUriParams['childrenAge'], $this->arUriParams['dateFrom'], $this->arUriParams['dateTo'], false);
                $cache->endDataCache($this->arExternalInfo);
            }

            $arExternalIDs = array_keys($this->arExternalInfo);
            if ($arExternalIDs) {
                $this->arFilter["UF_EXTERNAL_ID"] = $arExternalIDs;
            } else {
                $this->arFilter["UF_EXTERNAL_ID"] = false;
            }
        }
    }

    private function setSectionPictures(&$arSection, &$arDataFullGallery = [])
    {
        foreach ($this->season['UF_SEASON'] as $season) {
            if ($season == 'Лето') {
                if ($arSection["UF_PHOTOS"]) {
                    foreach ($arSection["UF_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                        $preResult = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                        $preResult['big'] = $imageOriginal['SRC'];
                        $arSection["PICTURES"][] = $preResult;
                    }
                } else {
                    $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                }
                if ($arSection['UF_STORIES_SUMMER'] && $arSection['UF_STORIES_PREVIEW_SUMMER']) {
                    foreach ($arSection['UF_STORIES_SUMMER'] as $key => $story) {
                        $arSection['STORIES'][] =
                            [
                                'name' => $arSection['NAME'],
                                'video' => CFile::GetPath($story),
                                'preview' => CFile::GetPath($arSection['UF_STORIES_PREVIEW_SUMMER'][$key]),
                            ];
                    }
                }
            } elseif ($season == 'Зима') {
                if ($arSection["UF_WINTER_PHOTOS"]) {
                    foreach ($arSection["UF_WINTER_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                        $preResult = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                        $preResult['big'] = $imageOriginal['SRC'];
                        $arSection["PICTURES"][] = $preResult;
                    }
                } else {
                    if ($arSection["UF_PHOTOS"]) {
                        foreach ($arSection["UF_PHOTOS"] as $photoId) {
                            $imageOriginal = CFile::GetFileArray($photoId);
                            $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                            $preResult = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                            $preResult['big'] = $imageOriginal['SRC'];
                            $arSection["PICTURES"][] = $preResult;
                        }
                    } else {
                        $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                    }
                }
                if ($arSection['UF_STORIES_WINTER'] && $arSection['UF_STORIES_PREVIEW_WINTER']) {
                    foreach ($arSection['UF_STORIES_WINTER'] as $key => $story) {
                        $arSection['STORIES'][] =
                            [
                                'name' => $arSection['NAME'],
                                'video' => CFile::GetPath($story),
                                'preview' => CFile::GetPath($arSection['UF_STORIES_PREVIEW_WINTER'][$key]),
                            ];
                    }
                }
            } elseif ($season == 'Осень+Весна') {
                if ($arSection["UF_MIDSEASON_PHOTOS"]) {
                    foreach ($arSection["UF_MIDSEASON_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                        $preResult = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                        $preResult['big'] = $imageOriginal['SRC'];
                        $arSection["PICTURES"][] = $preResult;
                    }
                } else {
                    if ($arSection["UF_PHOTOS"]) {
                        foreach ($arSection["UF_PHOTOS"] as $photoId) {
                            $imageOriginal = CFile::GetFileArray($photoId);
                            $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                            $preResult = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                            $preResult['big'] = $imageOriginal['SRC'];
                            $arSection["PICTURES"][] = $preResult;
                        }
                    } else {
                        $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                    }
                }
                if ($arSection['UF_STORIES_MIDSEASON'] && $arSection['UF_STORIES_PREVIEW_MIDSEASON']) {
                    foreach ($arSection['UF_STORIES_MIDSEASON'] as $key => $story) {
                        $arSection['STORIES'][] =
                            [
                                'name' => $arSection['NAME'],
                                'video' => CFile::GetPath($story),
                                'preview' => CFile::GetPath($arSection['UF_STORIES_PREVIEW_MIDSEASON'][$key]),
                            ];
                    }
                }
            }
        }
        unset($arSection["UF_PHOTOS"]);
    }

    private function sectionsQuery()
    {
        $rsSections = CIBlockSection::GetList($this->arSort, $this->arFilter, false, ["IBLOCK_ID", "ID", "NAME", "CODE", "SECTION_PAGE_URL", "UF_*"], false);
        while ($arSection = $rsSections->GetNext()) {
            $this->arSections[$arSection['ID']] = $arSection;
        }
    }

    private function fillSections()
    {
        //кеш каталог а временно выключен на переработку
        //        if (!isset($this->arFilter['UF_EXTERNAL_ID'])) {

        //            $cache = Cache::createInstance();
        //            $arFilterCacheKey = Utils::recursiveImplode($this->arFilter, '_');
        //            $cacheKey = 'without_date_search_' . $arFilterCacheKey;

        //            if ($cache->initCache(86400, $cacheKey)) {
        //                $this->arSections = $cache->getVars();
        //            } elseif ($cache->startDataCache()) {
        //                $this->sectionsQuery();
        //                $cache->endDataCache($this->arSections);
        //            }
        //        } else {
        $this->sectionsQuery();
        //        }

        $this->searchedRegionData = Regions::getRegionById($this->arRegionIds[0] ?? false);
        foreach ($this->arSections as &$arSection) {

            $arDataFullGallery = [];

            $this->setSectionPictures($arSection, $arDataFullGallery);

            $arSection["FULL_GALLERY"] = implode(",", $arDataFullGallery);

            if ($arSection["UF_COORDS"]) {
                $arSection["COORDS"] = explode(',', $arSection["UF_COORDS"]);
            }

            // Минимальная и максимальная цена в выборке
            if ($arSection['UF_MIN_PRICE'] !== NULL) {
                $arSectionsPrice[] = $arSection['UF_MIN_PRICE'];
            }

            /* Растояние до поискового запроса */
            if ($this->searchedRegionData) {

                $this->searchedRegionData['COORDS'] = explode(',', $this->searchedRegionData['UF_COORDS']);

                $arSection['DISCTANCE'] = Utils::calculateTheDistance(
                    (float)$this->searchedRegionData['COORDS'][0],
                    (float)$this->searchedRegionData['COORDS'][1],
                    (float)$arSection['COORDS'][0],
                    (float)$arSection['COORDS'][1]
                );
                $arSection['DISCTANCE_TO_REGION'] = $this->searchedRegionData['UF_CENTER_NAME_RU'] ?? $this->searchedRegionData['CENTER_UF_NAME'];

                $arSection['DISCTANCE_TO_REGION'] = ucfirst($arSection['DISCTANCE_TO_REGION']);
            } else {
                $arSection['REGION'] = Regions::getRegionById($arSection['UF_REGION'] ?? false);
            }

            /* -- */
            if ($this->arExternalInfo) {
                $sectionPrice = $this->arExternalInfo[$arSection["UF_EXTERNAL_ID"]];
                // Если это Traveline, то делим цену на кол-во дней
                if ($arSection["UF_EXTERNAL_SERVICE"] == 1) {
                    $sectionPrice = round($sectionPrice / $this->daysCount);
                }
            } else {
                $sectionPrice = $arSection["UF_MIN_PRICE"];
            }
            $arSection["PRICE"] = $sectionPrice;

            $arUriParamsSort = array(
                'sort' => $this->sortBy,
                'order' => $this->sortOrder,
            );

            if ($this->arUriParams['dateFrom'] != '') {
                $this->arUriParams = array_merge($this->arUriParams, $arUriParamsSort);
            }

            $uri = new Uri($arSection["SECTION_PAGE_URL"]);
            $uri->addParams($this->arUriParams);
            $sectionUrl = $uri->getUri();
            $arSection["URL"] = $sectionUrl;

            $arButtons = CIBlock::GetPanelButtons($arSection["IBLOCK_ID"], $arSection["ID"], 0, array("SECTION_BUTTONS" => false, "SESSID" => false));
            $arSection["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
            $arSection["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

            if ($this->request->get('maxPrice') || $this->request->get('minPrice')) {
                if (($arSection["PRICE"] <= $this->request->get('maxPrice') &&
                        $arSection["PRICE"] >= $this->request->get('minPrice')) &&
                    $arSection["PRICE"] !== NULL) {
                    $this->arSections[$arSection["ID"]] = $arSection;
                } else{
                   unset($this->arSections[$arSection["ID"]]);
                }
            } else {
                $this->arSections[$arSection["ID"]] = $arSection;
            }
        }

        if (!empty($arSectionsPrice)) {
            $this->minPrice = round(min($arSectionsPrice));
            $this->maxPrice = round(max($arSectionsPrice));
        }
    }

    private function fillRating()
    {
        /* Отзывы */
        $arCampingIDs = array_map(function ($a) {
            return $a["ID"];
        }, $this->arSections);

        if (isset($arCampingIDs) && !empty($arCampingIDs)) {
            $this->arReviewsAvg = Reviews::getCampingRating($arCampingIDs);
            foreach ($this->arReviewsAvg as $id => $review) {
                $this->arSections[$id]["RATING"] = $review["avg"];
            }
        }
    }

    private function getSeason()
    {
        if (Cmodule::IncludeModule('asd.iblock')) {
            $this->season = CASDiblockTools::GetIBUF(CATALOG_IBLOCK_ID);
        }
    }

    private function setSort()
    {
        $this->sortBy = (!empty($this->request->get('sort'))) ? strtolower($this->request->get('sort')) : "sort";
        $this->sortOrder = (!empty($this->request->get('order'))) ? strtolower($this->request->get('order')) : "asc";
        $this->orderReverse = (!empty($this->request->get('order')) && $this->request->get('order') == 'asc') ? "desc" : "asc";
        switch ($this->sortBy) {
            case 'popular':
                $sort = 'UF_RESERVE_COUNT';
                break;

            default:
                $sort = 'SORT';
                break;
        }

        $this->arSort = array($sort => $this->sortOrder);
    }

    private function applySort()
    {
        $sortOrder = $this->sortOrder;

        // Сортировка по расстоянию
        if ($this->searchedRegionData) {
            usort($this->arSections, function ($a, $b) {
                return ($a['DISCTANCE'] - $b['DISCTANCE']);
            });
        }

        /* Кастомная сортировка по рейтингу */
        if ($this->sortBy == 'rating') {
            uasort($this->arSections, function ($a, $b) use ($sortOrder) {
                if ($a['RATING'] == $b['RATING'])
                    return false;

                if ($sortOrder == 'asc') {
                    return ($a['RATING'] > $b['RATING']) ? 1 : -1;
                } elseif ($sortOrder == 'desc') {
                    return ($a['RATING'] < $b['RATING']) ? 1 : -1;
                }
            });
        }

        /* Кастомная сортировка по цене */
        if ($this->sortBy == 'price') {
            uasort($this->arSections, function ($a, $b) use ($sortOrder) {
                if ($a['PRICE'] == $b['PRICE'])
                    return false;

                if ($sortOrder == 'asc') {
                    return ($a['PRICE'] > $b['PRICE']) ? 1 : -1;
                } elseif ($sortOrder == 'desc') {
                    return ($a['PRICE'] < $b['PRICE']) ? 1 : -1;
                }
            });
        }
    }

    private function setSectionFilters()
    {
        $this->arFilter = [
            "IBLOCK_ID" => CATALOG_IBLOCK_ID,
            "ACTIVE" => "Y",
        ];
        $this->setSectionNameFilter();
        $this->setOtherFilters();
    }

    private function setSectionNameFilter()
    {
        if ($this->request->get('name') !== null) {
            $this->search = ($this->request->get('name')) ? $this->request->get('name') : null;

            $decodeSearch = json_decode($this->search, true);
            if ($decodeSearch['type']) {
                switch ($decodeSearch['type']) {
                    case 'area':
                        $this->searchName = $decodeSearch['item'];
                        $arRegionIds = Regions::getCityByName($this->searchName);
                        if (!empty($arRegionIds)) {
                            $this->arFilter["UF_AREA_NAME"] = $arRegionIds[0]['ID'];
                            $arRegionIds = array_map(function ($arRegion) {
                                return $arRegion['REGION_ID'];
                            }, $arRegionIds);
                        } else {
                            $arRegionIds = Regions::RegionFilterSearcher($this->searchName);
                            $this->arFilter["UF_REGION"] = $arRegionIds;
                        }

                        break;
                    case 'id':
                        $this->arFilter["ID"] = $decodeSearch['item'];
                        break;
                    case 'street':
                        $this->arFilter["%UF_ADDRESS"] = $decodeSearch['item'];
                        break;

                    case 'object':
                        $arNameResult = CIBlockSection::GetList([], array_merge($this->arFilter, ["%NAME" => trim($decodeSearch['item'])]), false, array("ID"), false)->Fetch();
                        if ($arNameResult) {
                            $arSectionIDs[] = $arNameResult["ID"];
                        }
                        $this->arFilter["ID"] = $arSectionIDs;
                        break;
                }

                $this->arFilterValues["SEARCH"] = json_encode($decodeSearch, JSON_UNESCAPED_UNICODE);
                $this->arFilterValues["SEARCH_TEXT"] = strip_tags($decodeSearch['title']);
            } else {
                $arRegionIds = Regions::RegionFilterSearcher($this->search);
                $this->arFilter["UF_REGION"] = $arRegionIds;


                if (empty($arRegionIds)) {

                    $arNameResult = CIBlockSection::GetList([], ['NAME' => '%' . $this->search . '%'], false, ['ID'], false)->Fetch();
                    if ($arNameResult) {
                        $arSectionIDs[] = $arNameResult["ID"];
                    }

                    $this->arFilter["ID"] = $arSectionIDs;
                    unset($this->arFilter["UF_REGION"]);
                }
                $this->arFilterValues["SEARCH_TEXT"] = strip_tags($this->search);
            }

            if ($arRegionIds) {
                $this->arRegionIds = $arRegionIds;
            }
        }
    }

    private function setOtherFilters()
    {
        // Тип
        if (!empty($this->filterParams['types'])) {
            $this->arResult['arFilterTypes'] = explode(',', $this->filterParams['types']);
            $this->arFilter[] = [
                "LOGIC" => "OR",
                ["UF_TYPE" => explode(',', $this->filterParams['types'])],
                ["UF_TYPE_EXTRA" => explode(',', $this->filterParams['types'])]
            ];
            $this->filterCount += count($this->arResult['arFilterTypes']);
        }

        // Услуги
        if (!empty($this->filterParams['services'])) {
            $this->arResult['arFilterServices'] = explode(',', $this->filterParams['services']);
            $this->arFilter["UF_SERVICES"] = $this->arResult['arFilterServices'];
            $this->filterCount += count($this->arResult['arFilterServices']);
        }

        // Питание
        if (!empty($this->filterParams['food'])) {
            $this->arResult['arFilterFood'] = explode(',', $this->filterParams['food']);
            $this->arFilter["UF_FOOD"] = $this->arResult['arFilterFood'];
            $this->filterCount += count($this->arResult['arFilterFood']);
        }

        // Особенности
        if (!empty($this->filterParams['features'])) {
            $this->arResult['arFilterFeatures'] = explode(',', $this->filterParams['features']);
            $this->arFilter["UF_FEATURES"] = $this->arResult['arFilterFeatures'];
            $this->filterCount += count($this->arResult['arFilterFeatures']);
        }

        // Варианты отдыха
        if (!empty($this->filterParams['restvariants'])) {
            $this->arResult['arFilterRestVariants'] = explode(',', $this->filterParams['restvariants']);
            $this->arFilter["UF_REST_VARIANTS"] = $this->arResult['arFilterRestVariants'];
            $this->filterCount += count($this->arResult['arFilterRestVariants']);
        }

        // Удобства
        if (!empty($this->filterParams['objectcomforts'])) {
            $this->arResult['arFilterObjectComforts'] = explode(',', $this->filterParams['objectcomforts']);
            $this->arFilter["UF_OBJECT_COMFORTS"] = $this->arResult['arFilterObjectComforts'];
            $this->filterCount += count($this->arResult['arFilterObjectComforts']);
        }

        // Тип дома
        if (!empty($this->filterParams['housetypes'])) {
            $this->arResult['arFilterHousetypes'] = explode(',', $this->filterParams['housetypes']);
            $this->arFilter["UF_SUIT_TYPE"] = $this->arResult['arFilterHousetypes'];
            $this->filterCount += count($this->arResult['arFilterHousetypes']);
        }

        // Водоём
        if (!empty($this->filterParams['water'])) {
            $this->arResult['arFilterWater'] = explode(',', $this->filterParams['water']);
            $this->arFilter["UF_WATER"] = $this->arResult['arFilterWater'];
            $this->filterCount += count($this->arResult['arFilterWater']);
        }

        // Общий водоём
        if (!empty($this->filterParams['commonwater'])) {
            $this->arResult['arFilterCommonWater'] = explode(',', $this->filterParams['commonwater']);
            $this->arFilter["UF_COMMON_WATER"] = $this->arResult['arFilterCommonWater'];
            $this->filterCount += count($this->arResult['arFilterCommonWater']);
        }

        // Sitemap
        if (!empty($this->filterParams['sitemap'])) {
            $this->arResult['arFilterSitemap'] = explode(',', $this->filterParams['sitemap']);
            $this->arFilter["UF_SITEMAP"] = $this->arResult['arFilterSitemap'];
            $this->filterCount += count($this->arResult['arFilterSitemap']);
        }

        // Подборки
        if (!empty($this->filterParams['selection'])) {
            $this->arResult['arFilterSelection'] = explode(',', $this->filterParams['selection']);
            $this->arFilter["UF_IMPRESSIONS"] = $this->arResult['arFilterSelection'];
            $this->filterCount += count($this->arResult['arFilterSelection']);
        }

        // Разные фильтры
        if (!empty($this->filterParams['diffilter'])) {
            $this->arResult['arDifFilters'] = explode(',', $this->filterParams['diffilter']);
            $this->arFilter["UF_DIFF_FILTERS"] = $this->arResult['arDifFilters'];
            $this->filterCount += count($this->arResult['arDifFilters']);
        }

        // Впечатления
        if (!empty($this->filterParams['impressions'])) {
            $arRequestImpressions = explode(',', $this->filterParams['impressions']);

            $rsImpressions = CIBlockElement::GetList(false, array("IBLOCK_ID" => IMPRESSIONS_IBLOCK_ID, "ACTIVE" => "Y", "CODE" => $arRequestImpressions));
            $arFilterImpressions = array();
            while ($arImpression = $rsImpressions->Fetch()) {
                $arFilterImpressions[] = $arImpression["ID"];
                $meta = new \Bitrix\Iblock\InheritedProperty\ElementValues(IMPRESSIONS_IBLOCK_ID, $arImpression['ID']);
                $arImpression['META'] = $meta->getValues();
                $this->arSeoImpressions[] = $arImpression;
            }

            $this->arFilter["UF_IMPRESSIONS"] = $arFilterImpressions;

            if (empty($arFilterImpressions)) {
                LocalRedirect("/404/");
            }

            $this->filterCount += count($arRequestImpressions);
        }
    }

    private function fillBaseInfo()
    {
        global $APPLICATION;

        $arDefaultVariableAliases404 = [];

        $arVariables = array();

        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates(self::SEF_URL_TEMPLATES, $this->arParams["SEF_URL_TEMPLATES"]);
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);

        $engine = new CComponentEngine($this);
        if (CModule::IncludeModule('iblock')) {
            $engine->addGreedyPart("#SECTION_CODE_PATH#");
            $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
        }
        $this->componentPage = $engine->guessComponentPath(
            $this->arParams["SEF_FOLDER"],
            $arUrlTemplates,
            $arVariables
        );

        $b404 = false;
        if (!$this->componentPage) {
            $this->componentPage = "news";
            $b404 = true;
        }

        if ($this->componentPage == "section") {
            if (isset($arVariables["SECTION_ID"]))
                $b404 |= (intval($arVariables["SECTION_ID"]) . "" !== $arVariables["SECTION_ID"]);
            else
                $b404 |= !isset($arVariables["SECTION_CODE"]);
        }

        if ($b404 && CModule::IncludeModule('iblock')) {
            $folder404 = str_replace("\\", "/", $this->arParams["SEF_FOLDER"]);
            if ($folder404 != "/")
                $folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
            if (mb_substr($folder404, -1) == "/")
                $folder404 .= "index.php";

            if ($folder404 != $APPLICATION->GetCurPage(true)) {
                \Bitrix\Iblock\Component\Tools::process404(
                    "",
                    ($this->arParams["SET_STATUS_404"] === "Y"),
                    ($this->arParams["SET_STATUS_404"] === "Y"),
                    ($this->arParams["SHOW_404"] === "Y"),
                    $this->arParams["FILE_404"]
                );
            }
        }

        CComponentEngine::initComponentVariables($this->componentPage, self::COMPONENT_VARIABLES, $arVariableAliases, $arVariables);

        $this->arResult = [
            "FOLDER" => $this->arParams["SEF_FOLDER"],
            "URL_TEMPLATES" => $arUrlTemplates,
            "VARIABLES" => $arVariables,
            "ALIASES" => $arVariableAliases,
        ];

        /* Особенность структуры сайта */
        if ($this->componentPage == "news" || $_REQUEST["impressions"]) {
            $this->componentPage = "section";
        } elseif ($this->componentPage == "section") {
            $this->componentPage = "detail";
        }
    }

    protected function prepareResultArray()
    {
        if ($this->componentPage == "section") {
            $this->prepareSection();
        } else if ($this->componentPage == "detail") {
            $this->prepareDetail();
        }
    }

    protected function prepareSection()
    {
        $this->fillSectionVariables();
        $this->arResult['arUriParams'] = $this->arUriParams;
        $this->arResult['CHPY'] = $this->chpy;
        $this->arResult['CHPY_SEO_TEXT'] = $this->chpy['UF_SEO_TEXT'];
        $this->arResult['SEO_FILE'] = CSite::InDir('/map') ? 'map' : 'catalog';
        $this->arResult['FAVORITES'] = Users::getFavourites();
        $this->arResult['SECTION_FILTER'] = $this->arFilter;
        $this->arResult['SECTION_FILTER_VALUES'] = $this->arFilterValues;
        $this->arResult['FILTER_COUNT'] = $this->filterCount;
        $this->arResult['arRegionIds'] = $this->arRegionIds;
        $this->arResult['countDeclension'] = new Declension('вариант', 'варианта', 'вариантов');
        $this->arResult['reviewsDeclension'] = new Declension('отзыв', 'отзыва', 'отзывов');
        $this->arResult['guestsDeclension'] = new Declension('гость', 'гостя', 'гостей');
        $this->arResult['arSeoImpressions'] = $this->arSeoImpressions;
        $this->arResult['arSort'] = $this->arSort;
        $this->arResult['sortBy'] = $this->sortBy;
        $this->arResult['sortOrder'] = $this->sortOrder;
        $this->arResult['orderReverse'] = $this->orderReverse;
        $this->arResult['SECTIONS'] = $this->arSections;
        $this->arResult['arHLTypes'] = $this->arHLTypes;
        $this->arResult['arHLFood'] = $this->arHLFood;
        $this->arResult['houseTypeData'] = $this->houseTypeData;
        $this->arResult['water'] = $this->water;
        $this->arResult['commonWater'] = $this->commonWater;
        $this->arResult['difFilter'] = $this->difFilter;
        $this->arResult['restVariants'] = $this->restVariants;
        $this->arResult['objectComforts'] = $this->objectComforts;
        $this->arResult['arHLFeatures'] = $this->arHLFeatures;
        $this->arResult['arServices'] = $this->arServices;
        $this->arResult['pageSeoData'] = $this->pageSeoData;
        $this->arResult['titleSEO'] = $this->titleSEO;
        $this->arResult['descriptionSEO'] = $this->descriptionSEO;
        $this->arResult['h1SEO'] = $this->h1SEO;
        $this->arResult['minPrice'] = $this->minPrice;
        $this->arResult['maxPrice'] = $this->maxPrice;
        $this->arResult['arReviewsAvg'] = $this->arReviewsAvg;
        $this->arResult['page'] = $this->page;
        $this->arResult['arDates'] = $this->arDates;
        $this->arResult['currMonthName'] = $this->currMonthName;
        $this->arResult['currYear'] = $this->currYear;
        $this->arResult['nextYear'] = $this->nextYear;
        $this->arResult['searchedRegionData'] = $this->searchedRegionData;
        $this->arResult['searchName'] = $this->searchName;
        $this->arResult['search'] = $this->search;
    }

    protected function prepareDetail()
    {
        $this->fillDetailVariables();
        $this->arResult['arUriParams'] = $this->arUriParams;
        $this->arResult['FAVORITES'] = Users::getFavourites();
        $this->arResult['SECTION'] = $this->arSections;
        $this->arResult['guestsDeclension'] = new Declension('гость', 'гостя', 'гостей');
        $this->arResult['childrenDeclension'] = new Declension('ребенок', 'ребенка', 'детей');
        $this->arResult['reviewsDeclension'] = new Declension('отзыв', 'отзыва', 'отзывов');
        $this->arResult['daysDeclension'] = new Declension('ночь', 'ночи', 'ночей');
        $this->arResult['roomsDeclension'] = new Declension('комната', 'комнаты', 'комнат');
        $this->arResult['bedsDeclension'] = new Declension('спальное место', 'спальных места', 'спальных мест');
        $this->arResult['humenDeclension'] = new Declension('взрослый', 'взрослых', 'взрослых');
        $this->arResult['titleSEO'] = $this->titleSEO;
        $this->arResult['descriptionSEO'] = $this->descriptionSEO;
        $this->arResult['h1SEO'] = $this->h1SEO;
        $this->arResult['currMonthName'] = $this->currMonthName;
        $this->arResult['currYear'] = $this->currYear;
        $this->arResult['nextYear'] = $this->nextYear;
        $this->arResult['arDates'] = $this->arDates;
        $this->arResult['daysCount'] = $this->daysCount;
        $this->arResult['daysRange'] = $this->daysRange;
        $this->arResult['searchError'] = $this->searchError;
        $this->arResult['arExternalInfo'] = $this->arExternalInfo;
        $this->arResult['arFilter'] = $this->arFilter;
        $this->arResult['isUserReview'] = $this->isUserReview;
        $this->arResult['arReviews'] = $this->arReviews;
        $this->arResult['reviewsSortType'] = $this->reviewsSortType;
        $this->arResult['arReviewsUsers'] = $this->arReviewsUsers;
        $this->arResult['reviewsPage'] = $this->reviewsPage;
        $this->arResult['reviewsPageCount'] = $this->reviewsPageCount;
        $this->arResult['arReviewsLikesData'] = $this->arReviewsLikesData;
        $this->arResult['reviewsCount'] = $this->reviewsCount;
        $this->arResult['avgRating'] = $this->avgRating;
        $this->arResult['arAvgCriterias'] = $this->arAvgCriterias;
        $this->arResult['arElements'] = $this->arElements;
        $this->arResult['arElementsParent'] = $this->arElementsParent;
        $this->arResult['allCount'] = $this->allCount;
        $this->arResult['page'] = $this->page;
        $this->arResult['pageCount'] = $this->pageCount;
        $this->arResult['minPrice'] = $this->minPrice;
        $this->arResult['arHLTypes'] = $this->arHLTypes;
        $this->arResult['arHLFeatures'] = $this->arHLFeatures;
        $this->arResult['arHLRoomFeatures'] = $this->arHLRoomFeatures;
        $this->arResult['arHouseTypes'] = $this->arHouseTypes;
        $this->arResult['arObjectComforts'] = $this->arObjectComforts;
    }

    protected function fillDetailVariables()
    {
        $this->fillDetailSection();
        $this->getSeason();
        $this->setSectionPictures($this->arSections);
        $this->fillUriParams();
        $this->fillDetailSeoParams();
        $this->makeCalendar();
        $this->getFaq();
        $this->setDetailFilter();
        $this->getDetailExternalInfo();
        $this->getDetailReviews();
        $this->getDetailServices();
        $this->getRooms();
        $this->setPagination();
        $this->setMinPrice();
        $this->fillHLInfo();
        $this->getComfortsDetail();
    }

    private function getComfortsDetail()
    {
        // Поиск детального описания удобства или развлечения
        $featuresDetailList = ElementFeaturesdetailTable::getList([
            'select' => ['ID', 'NAME', 'FUN_VALUE' => 'FUN.VALUE', 'COMFORT_VALUE' => 'COMFORT.VALUE'],
            'filter' => [
                'OBJECT.VALUE' => $this->arSections['ID'],
                [
                    "LOGIC" => "OR",
                    ["COMFORT.VALUE" => $this->arObjectComfortsIds],
                    ["FUN.VALUE" => $this->arHLFeaturesIds]
                ],
            ],
            'cache' => ['ttl' => 36000000],
        ])->fetchAll();

        foreach ($featuresDetailList as $featuresDetail) {
            if (isset($this->arObjectComforts[$featuresDetail['COMFORT_VALUE']])) {
                $this->arObjectComforts[$featuresDetail['COMFORT_VALUE']]['ELEMENT'] = $featuresDetail['ID'];
            }
            if (isset($this->arHLFeatures[$featuresDetail['FUN_VALUE']])) {
                $this->arHLFeatures[$featuresDetail['FUN_VALUE']]['ELEMENT'] = $featuresDetail['ID'];
            }
        }
    }

    private function fillHLInfo()
    {
        // Тип объекта        
        $entityClass = HighloadBlockTable::compileEntity(TYPES_HL_ENTITY)->getDataClass();
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "order" => ["UF_SORT" => "ASC"],
            'cache' => ['ttl' => 36000000],
        ]);
        while ($arEntity = $rsData->Fetch()) {
            $this->arHLTypes[$arEntity["ID"]] = $arEntity;
        }

        // Особенности объекта           
        $entityClass = HighloadBlockTable::compileEntity(FEATURES_HL_ENTITY)->getDataClass();
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "filter" => ['ID' => $this->arSections['UF_FEATURES']],
            "order" => ["UF_SORT" => "ASC"],
            'cache' => ['ttl' => 36000000],
        ]);
        while ($arEntity = $rsData->Fetch()) {
            $this->arHLFeatures[$arEntity["UF_XML_ID"]] = $arEntity;
            $this->arHLFeaturesIds[] = $arEntity["UF_XML_ID"];
        }

        // Особенности номера        
        $entityClass = HighloadBlockTable::compileEntity(HL_ROOM_FEATURES_ENTITY)->getDataClass();
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "order" => ["UF_SORT" => "ASC"],
            'cache' => ['ttl' => 36000000],
        ]);
        while ($arEntity = $rsData->Fetch()) {
            $this->arHLRoomFeatures[$arEntity["UF_XML_ID"]] = $arEntity;
        }

        // Типы домов
        $houseTypesDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();
        $houseTypeData = $houseTypesDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ->setCacheTtl(36000000)
            ?->fetchAll();
        foreach ($houseTypeData as $houseType) {
            $this->arHouseTypes[$houseType['ID']] = $houseType;
        }

        // Удобства объекта
        if (is_array($this->arSections['UF_OBJECT_COMFORTS']) && count($this->arSections['UF_OBJECT_COMFORTS'])) {
            $objectComfortsDataClass = HighloadBlockTable::compileEntity(OBJECT_COMFORT_HL_ENTITY)->getDataClass();
            $objectComfortsData = $objectComfortsDataClass::query()
                ->addSelect('*')
                ->where('ID', 'in', $this->arSections['UF_OBJECT_COMFORTS'])
                ->setOrder(['UF_SORT' => 'ASC'])
                ->setCacheTtl(36000000)
                ?->fetchAll();
            foreach ($objectComfortsData as $objectComfort) {
                $this->arObjectComforts[$objectComfort['UF_XML_ID']] = $objectComfort;
                $this->arObjectComfortsIds[] = $objectComfort['UF_XML_ID'];
            }
        }
    }

    private function setMinPrice()
    {
        if (!empty($this->arExternalInfo)) {
            $this->minPrice = $this->arElements[0]['PRICE'];
        } else {
            $this->minPrice = $this->arSections['UF_MIN_PRICE'];
        }

        if ($this->minPrice == 0) {
            $this->minPrice = $this->arSections['UF_MIN_PRICE'];
        }
    }

    private function setPagination()
    {
        // Пагинация номеров
        $this->allCount = count($this->arElements);
        if ($this->allCount > 0) {
            $this->page = $_REQUEST['page'] ?? 1;
            $this->pageCount = ceil($this->allCount / $this->arParams["DETAIL_ITEMS_COUNT"]);
            if ($this->pageCount > 1) {
                $this->arElements = array_slice(
                    $this->arElements,
                    ($this->page - 1) * $this->arParams["DETAIL_ITEMS_COUNT"],
                    $this->arParams["DETAIL_ITEMS_COUNT"]
                );
            }
        }
    }

    private function getRooms()
    {
        // Список номеров
        $rsElements = CIBlockElement::GetList(
            array("SORT" => "ASC"),
            $this->arFilter,
            false,
            false,
            array(
                "IBLOCK_ID",
                "ID",
                "IBLOCK_SECTION_ID",
                "NAME",
                "DETAIL_TEXT",
                "PROPERTY_PHOTOS",
                "PROPERTY_FEATURES",
                "PROPERTY_EXTERNAL_ID",
                "PROPERTY_EXTERNAL_CATEGORY_ID",
                "PROPERTY_SQUARE",
                "PROPERTY_PARENT_ID",
                'PROPERTY_ROOMS',
                'PROPERTY_BEDS',
                'PROPERTY_ROOMTOUR',
                'PROPERTY_WITH_PETS',
                'PROPERTY_QUANTITY_HUMEN',
                'PROPERTY_QUANTITY_CHILD'
            )
        );

        $this->arElements = array();
        while ($arElement = $rsElements->Fetch()) {
            
            if ($arElement["PROPERTY_PHOTOS_VALUE"]) {
                foreach ($arElement["PROPERTY_PHOTOS_VALUE"] as $photoId) {
                    $arElement["PICTURES"][$photoId] = [
                        'src' => CFile::ResizeImageGet($photoId, array('width' => 464, 'height' => 328), BX_RESIZE_IMAGE_EXACT, true)['src'],
                        'big' => CFile::GetFileArray($photoId)["SRC"],
                    ];
                }
            } else {
                $arElement["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/no_photo.png";
            }

            //if (!empty($this->arExternalInfo)) {
            //    $roomElement = current($this->arExternalInfo[$arElement["ID"]]);
            //    $arElement["PRICE"] = $roomElement["price"];
            //} else {
            //    $arElement["PRICE"] = 0;
            //}

            if (!empty($this->arExternalInfo)) {
                if (isset($this->arExternalInfo[$arElement["ID"]])) {
                    $roomElement = $this->arExternalInfo[$arElement["ID"]];
                    $arElement["PRICE"] = $roomElement[0]["price"];
                }
            } else {
                $arElement["PRICE"] = 0;
            }

            $discountData = CCatalogProduct::GetOptimalPrice($arElement['ID'], 1, CurrentUser::get()->getUserGroups(), 'N');

            if (is_array($discountData['DISCOUNT']) && count($discountData['DISCOUNT'])) {
                $arElement['DISCOUNT_DATA']['VALUE'] = $discountData['DISCOUNT']['VALUE'];
                $arElement['DISCOUNT_DATA']['VALUE_TYPE'] = $discountData['DISCOUNT']['VALUE_TYPE'];
            }

            $arElement['AVAILABLE_ID'] = false;
            if (is_array($this->arExternalAvail['ID']) && in_array($arElement['ID'], $this->arExternalAvail['ID'])) {
                $arElement['AVAILABLE_ID'] = true;
            }

            $this->arElements[$arElement['ID']] = $arElement;
        }

        if ($this->arSections["UF_EXTERNAL_SERVICE"] == "bnovo") {
            foreach ($this->arElements as $key => $arElement) {
                if ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] > 0) {
                    if (!isset($parentExternalIds) || !in_array(
                        $arElement["PROPERTY_PARENT_ID_VALUE"],
                        $parentExternalIds
                    )) {
                        $parentExternalIds[] = $arElement["PROPERTY_PARENT_ID_VALUE"];
                    }
                }

                if ($arElement["PRICE"] == NULL) {
                    unset($key);
                }
            }

            if (isset($parentExternalIds) && !empty($parentExternalIds)) {
                unset($this->arFilter["ID"]);
                $this->arFilter["?PROPERTY_EXTERNAL_ID"] = implode('|', $parentExternalIds);
                $rsElements = CIBlockElement::GetList(false, $this->arFilter, false, false, array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_TEXT", "PROPERTY_PHOTOS", "PROPERTY_FEATURES", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID", "PROPERTY_SQUARE", "PROPERTY_PARENT_ID"));
                while ($arElement = $rsElements->Fetch()) {
                    if ($arElement["PROPERTY_PHOTOS_VALUE"]) {
                        foreach ($arElement["PROPERTY_PHOTOS_VALUE"] as $photoId) {
                            $arElement["PICTURES"][$photoId] = [
                                'src' => CFile::ResizeImageGet($photoId, array('width' => 464, 'height' => 328), BX_RESIZE_IMAGE_EXACT, true)['src'],
                                'big' => CFile::GetFileArray($photoId)["SRC"],
                            ];
                        }
                    } else {
                        $arElement["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/no_photo.png";
                    }

                    $this->arElementsParent[$arElement['PROPERTY_EXTERNAL_ID_VALUE']] = $arElement;
                }
            }
        }

        usort($this->arElements, function ($a, $b) {
            // Сначала сортируем по AVAILABLE_ID (true идет первым)
            if ($a['AVAILABLE_ID'] === $b['AVAILABLE_ID']) {
                // Если AVAILABLE_ID одинаковы, сортируем по PRICE по убыванию
                return $b['PRICE'] <=> $a['PRICE'];
            }

            return $b['AVAILABLE_ID'] <=> $a['AVAILABLE_ID'];
        });
    }

    private function getDetailServices()
    {
        if ($this->arSections["UF_SERVICES"]) {
            $rsServices = CIBlockElement::GetList(false, array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "!IBLOCK_SECTION_ID" => false, "ID" => $this->arSections["UF_SERVICES"]), false, false, array("IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME"));
            while ($arService = $rsServices->Fetch()) {
                $this->arSections['arServices'][] = $arService;
            }
        }
    }

    private function getDetailReviews()
    {
        /* Отзывы */
        $this->reviewsSortType = (!empty($_GET['sort']) && isset($_GET['sort'])) ? strtolower($_GET['sort']) : "date";
        switch ($this->reviewsSortType) {
            case 'date':
                $arReviewsSort = array("ACTIVE_FROM" => "DESC");
                break;

            case 'negative':
                $arReviewsSort = array("PROPERTY_RATING" => "ASC");
                break;

            case 'positive':
                $arReviewsSort = array("PROPERTY_RATING" => "DESC");
                break;
        }
        $rsReviews = CIBlockElement::GetList($arReviewsSort, array("IBLOCK_ID" => REVIEWS_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_CAMPING_ID" => $this->arSections["ID"]), false, false, array("ID", "NAME", "ACTIVE_FROM", "DATE_CREATE", "DETAIL_TEXT", "PROPERTY_CAMPING_ID", "PROPERTY_PHOTOS", "PROPERTY_USER_ID", "PROPERTY_CRITERION_1", "PROPERTY_CRITERION_2", "PROPERTY_CRITERION_3", "PROPERTY_CRITERION_4", "PROPERTY_CRITERION_5", "PROPERTY_CRITERION_6", "PROPERTY_CRITERION_7", "PROPERTY_CRITERION_8", "PROPERTY_RATING"));
        $arReviewsUserIDs = array();
        $reviewsCountNotNullRating = 0;
        while ($arReview = $rsReviews->GetNext()) {
            foreach ($arReview["PROPERTY_PHOTOS_VALUE"] as $photoId) {
                $arReview["PICTURES"][] = CFile::ResizeImageGet($photoId, array('width' => 1920, 'height' => 1080), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 80)["src"];
                $arReview["PICTURES_THUMB"][] = CFile::ResizeImageGet($photoId, array('width' => 125, 'height' => 87), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 80);
            }

            if ($arReview['PROPERTY_USER_ID_VALUE'] == CurrentUser::get()->getId()) {
                $this->isUserReview = 'Y';
            }

            $arButtons = CIBlock::GetPanelButtons($arReview["IBLOCK_ID"], $arReview["ID"], 0, array("SECTION_BUTTONS" => false, "SESSID" => false));
            $arReview["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
            $arReview["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

            $this->arReviews[$arReview["ID"]] = $arReview;

            for ($i = 1; $i <= 8; $i++) {
                if ($arReview["PROPERTY_CRITERION_" . $i . "_VALUE"] > 0) {
                    $this->arAvgCriterias[$i][0]['value'] += $arReview["PROPERTY_CRITERION_" . $i . "_VALUE"];
                    $this->arAvgCriterias[$i][0]['count'] += 1;
                }
            }

            $this->avgRating += $arReview["PROPERTY_RATING_VALUE"];
            $this->reviewsCount++;
            if ($arReview["PROPERTY_RATING_VALUE"] != 0) {
                $reviewsCountNotNullRating++;
            }

            if (!in_array($arReview["PROPERTY_USER_ID_VALUE"], $arReviewsUserIDs)) {
                $arReviewsUserIDs[] = $arReview["PROPERTY_USER_ID_VALUE"];
            }
        }
        if ($this->reviewsCount > 0) {
            // Средние значения
            for ($i = 1; $i <= 8; $i++) {
                if (!empty($this->arAvgCriterias[$i][0]['count'])) {
                    $this->arAvgCriterias[$i][0] = number_format(round($this->arAvgCriterias[$i][0]['value'] / $this->arAvgCriterias[$i][0]['count'], 1), 1, '.', '');
                    $this->arAvgCriterias[$i][1] = round($this->arAvgCriterias[$i][0] * 100 / 5);
                }
            }
            $this->avgRating = round($this->avgRating / ($reviewsCountNotNullRating ? $reviewsCountNotNullRating : 1), 1);

            // Список юзеров в отзывах
            $rsReviewsUsers = CUser::GetList(($by = "ID"), ($order = "ASC"), array("ID" => implode(' | ', $arReviewsUserIDs)), array("FIELDS" => array("ID", "NAME", "PERSONAL_PHOTO")));
            while ($arReviewUser = $rsReviewsUsers->Fetch()) {
                if ($arReviewUser["PERSONAL_PHOTO"]) {
                    $arReviewUser["PERSONAL_PHOTO"] = CFile::GetFileArray($arReviewUser["PERSONAL_PHOTO"])["SRC"];
                }

                $this->arReviewsUsers[$arReviewUser["ID"]] = $arReviewUser;
            }

            // Пагинация отзывов
            $this->reviewsPage = $_REQUEST['reviewsPage'] ?? 1;
            $this->reviewsPageCount = ceil($this->reviewsCount / $this->arParams["DETAIL_REVIEWS_COUNT"]);
            if ($this->reviewsPageCount > 1) {
                $this->arReviews = array_slice($this->arReviews, ($this->reviewsPage - 1) * $this->arParams["DETAIL_REVIEWS_COUNT"], $this->arParams["DETAIL_REVIEWS_COUNT"], true);
            }

            // Лайки отзывов
            $arReviewsIDs = array_keys($this->arReviews);
            $this->arReviewsLikesData = Reviews::getLikes($arReviewsIDs);
            foreach ($this->arReviewsLikesData["STATS"] as $reviewId => $arLikes) {
                $this->arReviews[$reviewId]["LIKES"] = $arLikes;
            }
        }
    }

    private function getDetailExternalInfo()
    {
        if (!empty($this->arSections) && !empty($this->arUriParams['dateFrom']) && !empty($this->arUriParams['dateTo']) && !empty($this->arUriParams['guests'])) {
            $this->daysRange = $this->arUriParams['dateFrom'] . " - " . $this->arUriParams['dateTo'];
            $this->daysCount = abs(strtotime($this->arUriParams['dateTo']) - strtotime($this->arUriParams['dateFrom'])) / 86400;

            // Запрос в апи на получение списка кемпингов со свободными местами в выбранный промежуток
            $arExternalResult = Products::searchRooms($this->arSections['ID'], $this->arSections['UF_EXTERNAL_ID'], $this->arSections['UF_EXTERNAL_SERVICE'], $this->arUriParams['guests'], $this->arUriParams['childrenAge'], $this->arUriParams['dateFrom'], $this->arUriParams['dateTo'], $this->arSections['UF_MIN_CHIELD_AGE']);
            $this->arExternalInfo = $arExternalResult['arRooms'];
            $this->searchError = $arExternalResult['error'];

            //if ($this->arExternalInfo) {
            //    $this->arFilter["ID"] = array_keys($this->arExternalInfo);
            //} else {
            //    $this->arFilter["ID"] = false;
            //}

            if ($this->arExternalInfo) {
                $this->arExternalAvail["ID"] = array_keys($this->arExternalInfo);
            } else {
                $this->arExternalAvail["ID"] = false;
            }
        }

        if (!isset($this->arExternalAvail["ID"]) || empty($this->arExternalAvail["ID"])) {
            $this->arFilter['PROPERTY_IS_FAVORITE_VALUE'] = 'Да';
            unset($this->arExternalAvail['ID']);
        }
    }

    private function setDetailFilter()
    {   
        $this->arFilter = array(
            "IBLOCK_ID" => CATALOG_IBLOCK_ID,
            "ACTIVE" => "Y",
            "SECTION_ID" => $this->arSections["ID"],
            [
                "LOGIC" => "OR",
                ["PROPERTY_PARENT_ID" => NULL],
                ["PROPERTY_PARENT_ID" => 0]
            ]
        );

        if ($this->arSections["UF_EXTERNAL_SERVICE"] == "bnovo") {
            $this->arFilter['PROPERTY_PARENT_ID'] = 0;
        }  
    }

    private function fillDetailSeoParams()
    {
        $fieldsSection = new SectionValues(CATALOG_IBLOCK_ID, $this->arSections['ID']);
        $fieldsSectionValues = $fieldsSection->getValues();

        if (!empty($fieldsSectionValues)) {
            if (!empty($this->arHLTypes[$this->arSections["UF_TYPE"]]["UF_NAME"])) {
                $typeObject = mb_strtolower($this->arSections[$this->arSections["UF_TYPE"]]["UF_NAME"], "UTF-8");
            } else {
                $typeObject = "";
            }

            $this->titleSEO = str_replace("#TYPE#", $this->arHLTypes[$this->arSections["UF_TYPE"]]["UF_NAME"], $fieldsSectionValues["SECTION_META_TITLE"]);
            $this->descriptionSEO = str_replace("#TYPE#", $typeObject, $fieldsSectionValues["SECTION_META_DESCRIPTION"]);
            $this->h1SEO = str_replace("#TYPE#", $this->arHLTypes[$this->arSections["UF_TYPE"]]["UF_NAME"], $fieldsSectionValues["SECTION_PAGE_TITLE"]);
        } else {
            $this->titleSEO = $this->arSections["NAME"] . " - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист";
            $this->descriptionSEO = $this->arSections["NAME"] . " | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования.";
            $this->h1SEO = $this->arSections["~NAME"];
        }
    }

    protected function fillDetailSection()
    {
        $this->arSections = CIBlockSection::GetList(false, array("ACTIVE" => "Y", "IBLOCK_ID" => CATALOG_IBLOCK_ID, "=CODE" => $this->arResult["VARIABLES"]["SECTION_CODE"]), false, array("IBLOCK_ID", "ID", "NAME", "CODE", "DESCRIPTION", "SECTION_PAGE_URL", "UF_*"), false)->GetNext();
        if ($this->arSections) {
            $arEnum = CUserFieldEnum::GetList(array(), array("CODE" => "UF_EXTERNAL_SERVICE", "ID" => $this->arSections["UF_EXTERNAL_SERVICE"]))->GetNext();
            $this->arSections["UF_EXTERNAL_SERVICE"] = $arEnum['XML_ID'];

            if ($this->arSections["UF_COORDS"]) {
                $this->arSections["COORDS"] = explode(',', $this->arSections["UF_COORDS"]);
            }
        }
    }

    protected function getFaq()
    {
        $this->arResult['FAQ'] = ElementObjectfaqTable::getList([
            'select' => ['NAME', 'PREVIEW_TEXT'],
            'order' => ['SORT' => 'ASC'],
            'cache' => ['ttl' => 36000000],
        ])->fetchAll();
    }

    public function executeComponent()
    {
        $this->fillBaseInfo();
        $this->prepareResultArray();
        $this->includeComponentTemplate($this->componentPage);
    }
}
