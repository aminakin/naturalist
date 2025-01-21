<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Grid\Declension;
use Bitrix\Main\Web\Uri;
use Bitrix\Highloadblock\HighloadBlockTable;
use Naturalist\Products;
use Naturalist\Filters\Components;
use Naturalist\Users;
use Naturalist\Regions;
use Naturalist\Utils;
use Naturalist\Filters\UrlHandler;

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
    private $sortBy = '';
    private $sortOrder = '';
    private $orderReverse = '';
    private $arUriParams = [];
    private $filterParams = [];
    private $arFilter = [];
    private $arFilterValues = [];
    private $arRegionIds = [];
    private $arSeoImpressions = [];
    private $arSort = [];
    private $season = [];
    private $arExternalInfo = [];
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
    private $chpy;

    private function fillSectionVariables()
    {
        $this->arUriParams = [
            'dateFrom' => $this->request->get('dateFrom'),
            'dateTo' => $this->request->get('dateTo'),
            'guests' => $this->request->get('guests') ? $this->request->get('guests') : 2,
            'children' => $this->request->get('children') ? $this->request->get('children') : 0,
            'childrenAge' => $this->request->get('childrenAge') ? explode(',', $this->request->get('childrenAge')) : [],
        ];

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
        $this->setPageSeoData();
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

        if ($this->page > 1 && isset($_GET["impressions"]) && !empty($_GET['impressions']) && !empty($metaTags["/catalog/?page=2&impressions"])) { //переход с раздела "Впечатления" с пагинацией
            if (!empty($arSeoImpressions)) {
                $impressionReplace = $arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] ? $arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] : $arSeoImpressions[0]["NAME"];
            } else {
                $impressionReplace = "";
            }
            $titleSEO = $arSeoImpressions[0]["META"]['ELEMENT_META_TITLE'] . ' Страница - ' . $this->page;
            $descriptionSEO = $arSeoImpressions[0]["META"]['ELEMENT_META_DESCRIPTION'] . ' Страница - ' . $this->page;;
            $h1SEO = $impressionReplace;
        } elseif (isset($_GET["impressions"]) && !empty($_GET['impressions']) && !empty($metaTags["/catalog/?page=2&impressions"])) { //переход с раздела "Впечатления"
            if (!empty($arSeoImpressions)) {
                $impressionReplace = $arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] ? $arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] : $arSeoImpressions[0]["NAME"];
            } else {
                $impressionReplace = "";
            }
            $titleSEO = $arSeoImpressions[0]["META"]['ELEMENT_META_TITLE'];
            $descriptionSEO = $arSeoImpressions[0]["META"]['ELEMENT_META_DESCRIPTION'];
            $h1SEO = $impressionReplace;
        } elseif ($this->page > 1 && !empty($metaTags["/catalog/?page=2"])) { //страницы пагинации
            $titleSEO = str_replace("#PAGE#", $this->page, $metaTags["/catalog/?page=2"]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
            $descriptionSEO = str_replace("#PAGE#", $this->page, $metaTags["/catalog/?page=2"]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"]);
            $h1SEO = str_replace("#PAGE#", $this->page, $metaTags["/catalog/?page=2"]["~PROPERTY_H1_VALUE"]["TEXT"]);
        } elseif (!empty($metaTags[$currentURLDir])) {
            $titleSEO = $metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"];
            $descriptionSEO = $metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"];
            $h1SEO = $metaTags[$currentURLDir]["~PROPERTY_H1_VALUE"]["TEXT"];
        } else {
            $titleSEO = "Каталог - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист";
            $descriptionSEO = "Каталог | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования.";
            $h1SEO = "Карта глэмпингов в России";
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
        ]);
        while ($arEntity = $foodData->Fetch()) {
            $this->arHLFood[$arEntity["ID"]] = $arEntity;
        }

        // Типы домов
        $houseTypesDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();
        $this->houseTypeData = $houseTypesDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
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
            ?->fetchAll();

        // Общие водоёмы
        $commonWaterDataClass = HighloadBlockTable::compileEntity(COMMON_WATER_HL_ENTITY)->getDataClass();
        $this->commonWater = $commonWaterDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ?->fetchAll();

        // Разные фильтры
        $difFilterDataClass = HighloadBlockTable::compileEntity(DIFFERENT_FILTERS_HL_ENTITY)->getDataClass();
        $this->difFilter = $difFilterDataClass::query()
            ->addSelect('*')
            ?->fetchAll();

        // Варианты отдыха
        $restVariantsDataClass = HighloadBlockTable::compileEntity(REST_VARS_HL_ENTITY)->getDataClass();
        $this->restVariants = $restVariantsDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ?->fetchAll();

        // Удобства
        $objectComfortsDataClass = HighloadBlockTable::compileEntity(OBJECT_COMFORT_HL_ENTITY)->getDataClass();
        $this->objectComforts = $objectComfortsDataClass::query()
            ->addSelect('*')
            ->setOrder(['UF_SORT' => 'ASC'])
            ?->fetchAll();

        // Особенности объекта        
        $entityClass = HighloadBlockTable::compileEntity(FEATURES_HL_ENTITY)->getDataClass();
        $rsData = $entityClass::getList([
            "select" => ["*"],
            "order" => ["UF_SORT" => "ASC"],
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

            // Запрос в апи на получение списка кемпингов со свободными местами в выбранный промежуток
            $this->arExternalInfo = Products::search($this->arUriParams['guests'], $this->arUriParams['childrenAge'], $this->arUriParams['dateFrom'], $this->arUriParams['dateTo'], false);
            $arExternalIDs = array_keys($this->arExternalInfo);
            if ($arExternalIDs) {
                $this->arFilter["UF_EXTERNAL_ID"] = $arExternalIDs;
            } else {
                $this->arFilter["UF_EXTERNAL_ID"] = false;
            }
        }
    }

    private function fillSections()
    {
        $rsSections = CIBlockSection::GetList($this->arSort, $this->arFilter, false, ["IBLOCK_ID", "ID", "NAME", "CODE", "SECTION_PAGE_URL", "UF_*"], false);
        $searchedRegionData = Regions::getRegionById($this->arRegionIds[0] ?? false);
        while ($arSection = $rsSections->GetNext()) {

            $arDataFullGallery = [];

            foreach ($this->season['UF_SEASON'] as $season) {
                if ($season == 'Лето') {
                    if ($arSection["UF_PHOTOS"]) {
                        foreach ($arSection["UF_PHOTOS"] as $photoId) {
                            $imageOriginal = CFile::GetFileArray($photoId);
                            $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                            $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                        }
                    } else {
                        $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                    }
                } elseif ($season == 'Зима') {
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
                } elseif ($season == 'Осень+Весна') {
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

            if ($arSection["UF_COORDS"]) {
                $arSection["COORDS"] = explode(',', $arSection["UF_COORDS"]);
            }

            /* Растояние до поискового запроса */
            if ($searchedRegionData) {

                $searchedRegionData['COORDS'] = explode(',', $searchedRegionData['UF_COORDS']);

                $arSection['DISCTANCE'] = Utils::calculateTheDistance($searchedRegionData['COORDS'][0], $searchedRegionData['COORDS'][1], $arSection['COORDS'][0], $arSection['COORDS'][1]);
                $arSection['DISCTANCE_TO_REGION'] = $searchedRegionData['UF_CENTER_NAME_RU'] ?? $searchedRegionData['CENTER_UF_NAME'];

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
                if (($arSection["PRICE"] <= $this->request->get('maxPrice') && $arSection["PRICE"] >= $this->request->get('minPrice')) && $arSection["PRICE"] !== NULL) {
                    $this->arSections[$arSection["ID"]] = $arSection;
                }
            } else {
                $this->arSections[$arSection["ID"]] = $arSection;
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
            $search = ($this->request->get('name')) ? $this->request->get('name') : null;

            $decodeSearch = json_decode($search, true);
            if ($decodeSearch['type']) {
                switch ($decodeSearch['type']) {
                    case 'area':
                        $searchName = $decodeSearch['item'];
                        $arRegionIds = Regions::getCityByName($searchName);
                        if (!empty($arRegionIds)) {
                            $this->arFilter["UF_AREA_NAME"] = $arRegionIds[0]['ID'];
                            $arRegionIds = array_map(function ($arRegion) {
                                return $arRegion['REGION_ID'];
                            }, $arRegionIds);
                        } else {
                            $arRegionIds = Regions::RegionFilterSearcher($searchName);
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
                $arRegionIds = Regions::RegionFilterSearcher($search);
                $this->arFilter["UF_REGION"] = $arRegionIds;


                if (empty($arRegionIds)) {

                    $arNameResult = CIBlockSection::GetList([], ['NAME' => '%' . $search . '%'], false, ['ID'], false)->Fetch();
                    if ($arNameResult) {
                        $arSectionIDs[] = $arNameResult["ID"];
                    }

                    $this->arFilter["ID"] = $arSectionIDs;
                    unset($this->arFilter["UF_REGION"]);
                }
                $this->arFilterValues["SEARCH_TEXT"] = strip_tags($search);
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
    }

    protected function prepareDetail() {}

    public function executeComponent()
    {
        $this->fillBaseInfo();
        $this->prepareResultArray();
        $this->includeComponentTemplate($this->componentPage);
    }
}
