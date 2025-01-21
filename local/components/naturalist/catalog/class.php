<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Naturalist\Products;
use Naturalist\Filters\Components;
use Naturalist\Users;
use Naturalist\Regions;

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

    private $arUriParams = [];
    private $filterParams = [];
    private $arFilter = [];
    private $arFilterValues = [];
    private $filterCount = 0;
    private $chpy;

    private function fillSectionVariables()
    {
        $this->arUriParams = [
            'dateFrom' => $this->request->get('dateFrom'),
            'dateTo' => $this->request->get('dateTo'),
            'guests' => $this->request->get('guests'),
            'children' => $this->request->get('children'),
            'childrenAge' => $this->request->get('childrenAge'),
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
        ];
        $this->chpy = Components::getChpyLinkByUrl($_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL'] : $_SERVER['SCRIPT_NAME']);
        $this->setSectionFilters();
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
            $arFilterServices = explode(',', $_GET['services']);
            $this->arFilter["UF_SERVICES"] = $arFilterServices;
            $this->filterCount += count($arFilterServices);
        }

        // Питание
        if (!empty($this->filterParams['food'])) {
            $arFilterFood = explode(',', $_GET['food']);
            $this->arFilter["UF_FOOD"] = $arFilterFood;
            $this->filterCount += count($arFilterFood);
        }

        // Особенности
        if (!empty($this->filterParams['features'])) {
            $arFilterFeatures = explode(',', $_GET['features']);
            $this->arFilter["UF_FEATURES"] = $arFilterFeatures;
            $this->filterCount += count($arFilterFeatures);
        }

        // Варианты отдыха
        if (!empty($this->filterParams['restvariants'])) {
            $arFilterRestVariants = explode(',', $_GET['restvariants']);
            $this->arFilter["UF_REST_VARIANTS"] = $arFilterRestVariants;
            $this->filterCount += count($arFilterRestVariants);
        }

        // Удобства
        if (!empty($this->filterParams['objectcomforts'])) {
            $arFilterObjectComforts = explode(',', $_GET['objectcomforts']);
            $this->arFilter["UF_OBJECT_COMFORTS"] = $arFilterObjectComforts;
            $this->filterCount += count($arFilterObjectComforts);
        }

        // Тип дома
        if (!empty($this->filterParams['housetypes'])) {
            $arFilterHousetypes = explode(',', $_GET['housetypes']);
            $this->arFilter["UF_SUIT_TYPE"] = $arFilterHousetypes;
            $this->filterCount += count($arFilterHousetypes);
        }

        // Водоём
        if (!empty($this->filterParams['water'])) {
            $arFilterWater = explode(',', $_GET['water']);
            $this->arFilter["UF_WATER"] = $arFilterWater;
            $this->filterCount += count($arFilterWater);
        }

        // Общий водоём
        if (!empty($this->filterParams['commonwater'])) {
            $arFilterCommonWater = explode(',', $_GET['commonwater']);
            $this->arFilter["UF_COMMON_WATER"] = $arFilterCommonWater;
            $this->filterCount += count($arFilterCommonWater);
        }

        // Sitemap
        if (!empty($this->filterParams['sitemap'])) {
            $arFilterSitemap = explode(',', $_GET['sitemap']);
            $this->arFilter["UF_SITEMAP"] = $arFilterSitemap;
            $this->filterCount += count($arFilterSitemap);
        }

        // Подборки
        if (!empty($this->filterParams['selection'])) {
            $arFilterImpressions = explode(',', $_GET['selection']);
            $this->arFilter["UF_IMPRESSIONS"] = $arFilterImpressions;
            $this->filterCount += count($arFilterImpressions);
        }

        // Разные фильтры
        if (!empty($this->filterParams['diffilter'])) {
            $arDifFilters = explode(',', $_GET['diffilter']);
            $this->arFilter["UF_DIFF_FILTERS"] = $arDifFilters;
            $this->filterCount += count($arDifFilters);
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
        $this->arResult['URI_PARAMS'] = $this->arUriParams;
        $this->arResult['CHPY'] = $this->chpy;
        $this->arResult['CHPY_SEO_TEXT'] = $this->chpy['UF_SEO_TEXT'];
        $this->arResult['SEO_FILE'] = CSite::InDir('/map') ? 'map' : 'catalog';
        $this->arResult['FAVORITES'] = Users::getFavourites();
        $this->arResult['SECTION_FILTER'] = $this->arFilter;
        $this->arResult['SECTION_FILTER_VALUES'] = $this->arFilterValues;
        $this->arResult['FILTER_COUNT'] = $this->filterCount;
    }

    protected function prepareDetail() {}

    public function executeComponent()
    {
        $this->fillBaseInfo();
        $this->prepareResultArray();
        $this->includeComponentTemplate($this->componentPage);
    }
}
