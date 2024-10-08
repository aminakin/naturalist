<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

use Bitrix\Main\Application;
use Bitrix\Main\Grid\Declension;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Web\Uri;
use Naturalist\Morpher;
use Naturalist\Regions;
use Naturalist\Users;
use Naturalist\Products;
use Naturalist\Reviews;
use Bitrix\Iblock\Elements\ElementGlampingsTable;
use Naturalist\Utils;
use Naturalist\Filters\Components;
use Naturalist\Filters\UrlHandler;
use Bitrix\Main\Localization\Loc;

global $arUser, $userId, $isAuthorized;

$request = Application::getInstance()->getContext()->getRequest();
$isAjax = $request->isAjaxRequest();

$arUriParams = array(
    'dateFrom' => $_GET['dateFrom'],
    'dateTo' => $_GET['dateTo'],
    'guests' => $_GET['guests'],
    'children' => $_GET['children'],
    'childrenAge' => $_GET['childrenAge'],
);

if (CSite::InDir('/map')) {
    $seoFile = 'map';
} else {
    $seoFile = 'catalog';
}

$requestUrl = $_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL'] : $_SERVER['SCRIPT_NAME'];
$chpy = Components::getChpyLinkByUrl($requestUrl);
$chySeoText = $chpy['UF_SEO_TEXT'];

if ($_GET['page'] && count($_GET) > 1) {
    $urlWithPage = '/catalog/?';
    foreach ($_GET as $getName => $getValue) {
        if ($getName !== 'page') {
            $urlWithPage .= $getName . '=' . $getValue . '&';
        }
    }
    $urlWithPage = substr($urlWithPage, 0, -1);
    $pageSeoData = UrlHandler::getByRealUrl($urlWithPage, SITE_ID);
}

/* Избранное (список ID) */
$arFavourites = Users::getFavourites();

/* Склонения */
$countDeclension = new Declension('вариант', 'варианта', 'вариантов');
$reviewsDeclension = new Declension('отзыв', 'отзыва', 'отзывов');
$guestsDeclension = new Declension('гость', 'гостя', 'гостей');

/* Отзывы */
/*$rsReviews = CIBlockElement::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => REVIEWS_IBLOCK_ID, "ACTIVE" => "Y"), false, false, array("ID", "PROPERTY_CAMPING_ID", "PROPERTY_RATING"));
$arReviews = array();
while ($arReview = $rsReviews->Fetch()) {
    $arReviews[$arReview["PROPERTY_CAMPING_ID_VALUE"]][$arReview["ID"]] = $arReview["PROPERTY_RATING_VALUE"];
}
$arReviewsAvg = array_map(function ($a) {
    return round(array_sum($a) / count($a), 1);
}, $arReviews);*/

$isSeoText = false;

/* Фильтрация */
$arFilter = array(
    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
    "ACTIVE" => "Y"
);
// Название и место
if (!empty($_GET['name']) && isset($_GET['name'])) {
    $search = ($_GET['name']) ? $_GET['name'] : null;

    $decodeSearch = json_decode($search, true);
    if ($decodeSearch['type']) {
        switch ($decodeSearch['type']) {
            case 'area':
                //                $arFilter["%UF_REGION_NAME"] = $decodeSearch['item'];
                $searchName = $decodeSearch['item'];
                $arRegionIds = Regions::RegionFilterSearcher($searchName);

                $arFilter["UF_REGION"] = $arRegionIds;

                break;
            case 'id':
                $arFilter["ID"] = $decodeSearch['item'];
                break;
            case 'street':
                $arFilter["%UF_ADDRESS"] = $decodeSearch['item'];
                break;

            case 'object':
                $arNameResult = CIBlockSection::GetList([], array_merge($arFilter, ["%NAME" => trim($decodeSearch['item'])]), false, array("ID"), false)->Fetch();
                if ($arNameResult) {
                    $arSectionIDs[] = $arNameResult["ID"];
                }
                $arFilter["ID"] = $arSectionIDs;
                break;
        }

        $arFilterValues["SEARCH"] = json_encode($decodeSearch, JSON_UNESCAPED_UNICODE);
        $arFilterValues["SEARCH_TEXT"] = strip_tags($decodeSearch['title']);
    } else {

        //        CModule::IncludeModule('search');
        //        $obSearch = new CSearch;
        //        $obSearch->SetOptions(array(
        //            'ERROR_ON_EMPTY_STEM' => false,
        //        ));
        //        $obSearch->Search(array(
        //            'QUERY' => trim($search),
        //            'MODULE_ID' => 'iblock',
        //            'PARAM1' => 'catalog',
        //            'PARAM2' => CATALOG_IBLOCK_ID
        //        ));
        //        if (!$obSearch->selectedRowsCount()) {//и делаем резапрос, если не найдено с морфологией...
        //            $obSearch->Search(array(
        //                'QUERY' => trim($search),
        //                'MODULE_ID' => 'iblock',
        //                'PARAM1' => 'catalog',
        //                'PARAM2' => CATALOG_IBLOCK_ID
        //            ), array(), array('STEMMING' => false));//... уже с отключенной морфологией
        //        }
        //        while ($row = $obSearch->fetch()) {
        //            if($row['ITEM_ID'][0] == 'S'){
        //                $arSectionIDs[] = substr($row['ITEM_ID'],1);
        //            }
        //        }

        $arRegionIds = Regions::RegionFilterSearcher($search);
        $arFilter["UF_REGION"] = $arRegionIds;


        if (empty($arRegionIds)) {

            $arNameResult = CIBlockSection::GetList([], ['NAME' => '%' . $search . '%'], false, ['ID'], false)->Fetch();
            if ($arNameResult) {
                $arSectionIDs[] = $arNameResult["ID"];
            }



            $arFilter["ID"] = $arSectionIDs;
            unset($arFilter["UF_REGION"]);
        }

        //        $arFilter["ID"] = $arSectionIDs;
        $arFilterValues["SEARCH_TEXT"] = strip_tags($search);
    }
}


// Заезд, выезд, кол-во гостей
$dateFrom = $_GET['dateFrom'];
$dateTo = $_GET['dateTo'];
$guests = $_GET['guests'] ?? 2;
$children = $_GET['children'] ?? 0;
$arChildrenAge = (isset($_GET['childrenAge'])) ? explode(',', $_GET['childrenAge']) : [];
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

// Тип
if (!empty($_GET['types']) && isset($_GET['types'])) {
    $arFilterTypes = explode(',', $_GET['types']);
    //$arFilter["UF_TYPE"] = $arFilterTypes;
    $arFilter[] = array(
        "LOGIC" => "OR",
        array("UF_TYPE" => explode(',', $_GET['types'])),
        array("UF_TYPE_EXTRA" => explode(',', $_GET['types']))
    );
}

// Услуги
if (!empty($_GET['services']) && isset($_GET['services'])) {
    $arFilterServices = explode(',', $_GET['services']);
    $arFilter["UF_SERVICES"] = $arFilterServices;
}

// Питание
if (!empty($_GET['food']) && isset($_GET['food'])) {
    $arFilterFood = explode(',', $_GET['food']);
    $arFilter["UF_FOOD"] = $arFilterFood;
}

// Особенности
if (!empty($_GET['features']) && isset($_GET['features'])) {
    $arFilterFeatures = explode(',', $_GET['features']);
    $arFilter["UF_FEATURES"] = $arFilterFeatures;
}

// Варианты отдыха
if (!empty($_GET['restvariants']) && isset($_GET['restvariants'])) {
    $arFilterRestVariants = explode(',', $_GET['restvariants']);
    $arFilter["UF_REST_VARIANTS"] = $arFilterRestVariants;
}

// Удобства
if (!empty($_GET['objectcomforts']) && isset($_GET['objectcomforts'])) {
    $arFilterObjectComforts = explode(',', $_GET['objectcomforts']);
    $arFilter["UF_OBJECT_COMFORTS"] = $arFilterObjectComforts;
}

// Тип дома
if (!empty($_GET['housetypes']) && isset($_GET['housetypes'])) {
    $arFilterHousetypes = explode(',', $_GET['housetypes']);
    $arFilter["UF_SUIT_TYPE"] = $arFilterHousetypes;
}

// Водоём
if (!empty($_GET['water']) && isset($_GET['water'])) {
    $arFilterWater = explode(',', $_GET['water']);
    $arFilter["UF_WATER"] = $arFilterWater;
}

// Общий водоём
if (!empty($_GET['commonwater']) && isset($_GET['commonwater'])) {
    $arFilterCommonWater = explode(',', $_GET['commonwater']);
    $arFilter["UF_COMMON_WATER"] = $arFilterCommonWater;
}

// Sitemap
if (!empty($_GET['sitemap']) && isset($_GET['sitemap'])) {
    $arFilterSitemap = explode(',', $_GET['sitemap']);
    $arFilter["UF_SITEMAP"] = $arFilterSitemap;
}

// Sitemap
if (!empty($_GET['selection']) && isset($_GET['selection'])) {
    $arFilterImpressions = explode(',', $_GET['selection']);
    $arFilter["UF_IMPRESSIONS"] = $arFilterImpressions;
}

// Впечатления
if (!empty($_GET['impressions']) && isset($_GET['impressions'])) {
    $arRequestImpressions = explode(',', $_GET['impressions']);

    $rsImpressions = CIBlockElement::GetList(false, array("IBLOCK_ID" => IMPRESSIONS_IBLOCK_ID, "ACTIVE" => "Y", "CODE" => $arRequestImpressions));
    $arFilterImpressions = array();
    while ($arImpression = $rsImpressions->Fetch()) {
        $arFilterImpressions[] = $arImpression["ID"];
        $meta = new \Bitrix\Iblock\InheritedProperty\ElementValues(IMPRESSIONS_IBLOCK_ID, $arImpression['ID']);
        $arImpression['META'] = $meta->getValues();
        $arSeoImpressions[] = $arImpression;
    }

    $arFilter["UF_IMPRESSIONS"] = $arFilterImpressions;

    if (empty($arFilterImpressions)) {
        LocalRedirect("/404/");
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

/* Получение разделов */
$rsSections = CIBlockSection::GetList($arSort, $arFilter, false, array("IBLOCK_ID", "ID", "NAME", "CODE", "SECTION_PAGE_URL", "UF_*"), false);
$arSections = array();

$searchedRegionData = Regions::getRegionById($arRegionIds[0] ?? false);
while ($arSection = $rsSections->GetNext()) {
    $arDataFullGallery = [];
    if ($arSection["UF_PHOTOS"]) {
        foreach ($arSection["UF_PHOTOS"] as $photoId) {
            $imageOriginal = CFile::GetFileArray($photoId);
            $arDataFullGallery[] = "&quot;" . $imageOriginal["SRC"] . "&quot;";
            $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 600, 'height' => 400), BX_RESIZE_IMAGE_EXACT, true);
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

    /* Растояние до поискового запроса */
    if ($searchedRegionData) {

        $searchedRegionData['COORDS'] = explode(',', $searchedRegionData['UF_COORDS']);

        $arSection['DISCTANCE'] = Utils::calculateTheDistance($searchedRegionData['COORDS'][0], $searchedRegionData['COORDS'][1], $arSection['COORDS'][0], $arSection['COORDS'][1]);
        $arSection['DISCTANCE_TO_REGION'] = $searchedRegionData['UF_CENTER_NAME_RU'] ?? $searchedRegionData['CENTER_UF_NAME'];
        //        $arSection['DISCTANCE_TO_REGION'] = Utils::morpher($searchedRegionData['CENTER_UF_NAME'], Morpher::CASE_GENITIVE);

        $arSection['DISCTANCE_TO_REGION'] = ucfirst($arSection['DISCTANCE_TO_REGION']);
    } else {
        $arSection['REGION'] = Regions::getRegionById($arSection['UF_REGION'] ?? false);
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

    if ($arUriParams['dateFrom'] != '') {
        $arUriParams = array_merge($arUriParams, $arUriParamsSort);
    }

    $uri = new Uri($arSection["SECTION_PAGE_URL"]);
    $uri->addParams($arUriParams);
    $sectionUrl = $uri->getUri();
    $arSection["URL"] = $sectionUrl;

    $arButtons = CIBlock::GetPanelButtons($arSection["IBLOCK_ID"], $arSection["ID"], 0, array("SECTION_BUTTONS" => false, "SESSID" => false));
    $arSection["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
    $arSection["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

    $arSections[$arSection["ID"]] = $arSection;
}

/* Отзывы */
$arCampingIDs = array_map(function ($a) {
    return $a["ID"];
}, $arSections);
if (isset($arCampingIDs) && !empty($arCampingIDs)) {
    $arReviewsAvg = Reviews::getCampingRating($arCampingIDs);
    foreach ($arReviewsAvg as $id => $review) {
        $arSections[$id]["RATING"] = $review["avg"];
    }
}

if ($searchedRegionData) {
    usort($arSections, function ($a, $b) {
        return ($a['DISCTANCE'] - $b['DISCTANCE']);
    });
}
$allCount = count($arSections);

/* Кастомная сортировка по рейтингу */
if ($sortBy == 'rating') {
    uasort($arSections, function ($a, $b) use ($sortOrder) {
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
if ($sortBy == 'price') {
    uasort($arSections, function ($a, $b) use ($sortOrder) {
        if ($a['PRICE'] == $b['PRICE'])
            return false;

        if ($sortOrder == 'asc') {
            return ($a['PRICE'] > $b['PRICE']) ? 1 : -1;
        } elseif ($sortOrder == 'desc') {
            return ($a['PRICE'] < $b['PRICE']) ? 1 : -1;
        }
    });
}

/* Пагинация */
$page = $_REQUEST['page'] ?? 1;

$pageCount = ceil($allCount / $arParams["ITEMS_COUNT"]);
if ($pageCount > 1) {
    $arPageSections = array_slice($arSections, ($page - 1) * $arParams["ITEMS_COUNT"], $arParams["ITEMS_COUNT"]);
} else {
    $arPageSections = $arSections;
}

// Добавляем свойство Скидка, если есть хотя бы 1 элемент со скидкой
foreach ($arPageSections as $section) {
    $arSectionIds[] = $section['ID'];
}
unset($section);

$elements = ElementGlampingsTable::getList([
    'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID'],
    'filter' => ['IBLOCK_SECTION_ID' => $arSectionIds],
])->fetchAll();

foreach ($elements as $element) {
    $arElementsBySection[$element['IBLOCK_SECTION_ID']][] = $element;
}
unset($element);

foreach ($arPageSections as &$section) {
    foreach ($arElementsBySection[$section['ID']] as $element) {
        $arPrice = CCatalogProduct::GetOptimalPrice($element['ID'], 1, $USER->GetUserGroupArray(), 'N');
        if (is_array($arPrice['DISCOUNT']) && count($arPrice['DISCOUNT'])) {
            $section['IS_DISCOUNT'] = 'Y';
            $section['DISCOUNT_PERCENT'] = $arPrice['RESULT_PRICE']['PERCENT'];
            break;
        }
    }
}
unset($section);

/* HL Blocks */
// Тип объекта
$hlId = 2;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
    "filter" => ["UF_SHOW_FILTER" => "1"],
]);
$arHLTypes = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLTypes[$arEntity["ID"]] = $arEntity;
}
// Питание
$hlId = 12;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
    "filter" => ["UF_SHOW_FILTER" => "1"],
]);
$arHLFood = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLFood[$arEntity["ID"]] = $arEntity;
}

// Типы домов
$houseTypesDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();
$houseTypes = $houseTypesDataClass::query()
    ->addSelect('*')
    ->setOrder(['UF_SORT' => 'ASC'])
    ?->fetchAll();

// Варианты отдыха
$restVariantsDataClass = HighloadBlockTable::compileEntity(REST_VARS_HL_ENTITY)->getDataClass();
$restVariants = $restVariantsDataClass::query()
    ->addSelect('*')
    ->setOrder(['UF_SORT' => 'ASC'])
    ?->fetchAll();

// Удобства
$objectComfortsDataClass = HighloadBlockTable::compileEntity(OBJECT_COMFORT_HL_ENTITY)->getDataClass();
$objectComforts = $objectComfortsDataClass::query()
    ->addSelect('*')
    ->setOrder(['UF_SORT' => 'ASC'])
    ?->fetchAll();

// Особенности объекта
$hlId = 5;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
]);
$arHLFeatures = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLFeatures[$arEntity["ID"]] = $arEntity;
}

// Услуги
$rsServices = CIBlockElement::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_SHOW_FILTER_VALUE" => "Y"), false, false, array("IBLOCK_ID", "ID", "CODE", "NAME"));
$arServices = array();
while ($arService = $rsServices->Fetch()) {
    $arServices[$arService["ID"]] = $arService;
}

/* Генерация массива месяцев для фильтра */
$arDates = array();
$currMonth = date('m');
$currMonthName = FormatDate("f");
$currYear = date('Y');
$nextYear = $currYear + 1;
for ($i = $currMonth; $i <= 12; $i++) {
    $arDates[0][] = FormatDate("f", strtotime('1970-' . $i . '-01'));
}
for ($j = 1; $j <= 12; $j++) {
    $arDates[1][] = FormatDate("f", strtotime('1970-' . $j . '-01'));
}

/* Генерация SEO */
$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if ($page > 1 && isset($_GET["impressions"]) && !empty($_GET['impressions']) && !empty($metaTags["/catalog/?page=2&impressions"])) { //переход с раздела "Впечатления" с пагинацией
    if (!empty($arSeoImpressions)) {
        $impressionReplace = $arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] ? $arSeoImpressions[0]["META"]['ELEMENT_PAGE_TITLE'] : $arSeoImpressions[0]["NAME"];
    } else {
        $impressionReplace = "";
    }
    $titleSEO = $arSeoImpressions[0]["META"]['ELEMENT_META_TITLE'] . ' Страница - ' . $page;
    $descriptionSEO = $arSeoImpressions[0]["META"]['ELEMENT_META_DESCRIPTION'] . ' Страница - ' . $page;;
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
} elseif ($page > 1 && !empty($metaTags["/catalog/?page=2"])) { //страницы пагинации
    $titleSEO = str_replace("#PAGE#", $page, $metaTags["/catalog/?page=2"]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $descriptionSEO = str_replace("#PAGE#", $page, $metaTags["/catalog/?page=2"]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"]);
    $h1SEO = str_replace("#PAGE#", $page, $metaTags["/catalog/?page=2"]["~PROPERTY_H1_VALUE"]["TEXT"]);
} elseif (!empty($metaTags[$currentURLDir])) {
    $titleSEO = $metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"];
    $descriptionSEO = $metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"];
    $h1SEO = $metaTags[$currentURLDir]["~PROPERTY_H1_VALUE"]["TEXT"];
} else {
    $titleSEO = "Каталог - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист";
    $descriptionSEO = "Каталог | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования.";
    $h1SEO = "Карта глэмпингов в России";
}

if ($pageSeoData) {
    if ($pageSeoData['UF_H1']) {
        $APPLICATION->SetPageProperty("custom_title", $pageSeoData['UF_H1']);
    }
    if ($pageSeoData['UF_TITLE']) {
        $APPLICATION->SetTitle($pageSeoData['UF_TITLE']);
    }
    if ($pageSeoData['UF_DESCRIPTION']) {
        $APPLICATION->SetPageProperty("description", $pageSeoData['UF_DESCRIPTION']);
    }
} else {
    $APPLICATION->SetTitle($titleSEO);
    $APPLICATION->SetPageProperty("custom_title", $h1SEO);
    $APPLICATION->SetPageProperty("description", $descriptionSEO);
}

if (!count($arPageSections)) {
    $APPLICATION->AddHeadString('<meta name="robots" content="noindex">', true);
}

if (empty($chpy)) {
    $APPLICATION->AddHeadString('<link rel="canonical" href="' . HTTP_HOST . $APPLICATION->GetCurPage() . '">', true);
}

if ($chpy['UF_CANONICAL']) {
    $APPLICATION->AddHeadString('<link rel="canonical" href="' . HTTP_HOST . $chpy['UF_CANONICAL'] . '">', true);
}
/**/
?>

<main class="main  <?php if (CSite::InDir('/map')): ?>main__on_map<?php endif; ?>">
    <section class="section section_crumbs section_crumbs_catalog_new">
        <div class="container">
            <?
            $APPLICATION->IncludeComponent(
                "naturalist:empty",
                "catalog_breadcrumbs",
                array(
                    "map" => $arParams["MAP"]
                )
            );
            ?>
            <div class="wrapper_title_catalog_page">
                <h1 class="page_title"><? $APPLICATION->ShowProperty("custom_title") ?></h1>
                <div class="crumbs__controls">
                    <!--<a class="crumbs__controls-mobile" href="#" data-map-full="data-map-full">Смотреть на карте</a>-->
                    <!--                        <a class="button button_transparent" target="_blank"-->
                    <!--                           href="https://yandex.ru/maps/?mode=routes&rtext=" data-route="data-route">Маршрут</a>-->

                </div>
            </div>
            <?php if (CSite::InDir('/map')): ?>
                <div class="catalog_filter catalog_map">
                    <form class="form filters" id="form-catalog-filter-front">
                        <div class="form_group_wrapper">
                            <div class="form__item item_name">
                                <div class="field field_autocomplete" data-autocomplete="/ajax/autocomplete.php">
                                    <input type="hidden" data-autocomplete-result value='<?= ($arFilterValues["SEARCH"]) ? $arFilterValues["SEARCH"] : null ?>'>
                                    <label for="field-place"><?= Loc::getMessage('FILTER_PLACE') ?></label>
                                    <input class="field__input" type="text" name="name" placeholder="" data-autocomplete-field value='<?= ($arFilterValues["SEARCH_TEXT"]) ? $arFilterValues["SEARCH_TEXT"] : null ?>'>
                                    <div class="autocomplete-dropdown-wrap">
                                        <div class="autocomplete-dropdown-search">
                                            <input class="field__input" id="field-place" type="text" name="name" placeholder="Регионы или локации" data-autocomplete-field-mobile>
                                        </div>
                                        <div class="autocomplete-dropdown" data-autocomplete-dropdown>
                                        </div>
                                        <div class="autocomplete-dropdown-close-wrap">
                                            <div class="autocomplete-dropdown-close">
                                                Закрыть
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form_group_wrapper-filter_items">
                                <div class="form__row calendar" data-calendar="data-calendar" data-calendar-min="today" data-calendar-max="365">
                                    <div class="form__item">
                                        <div class="field field_icon field_calendar">
                                            <label><?= Loc::getMessage('FILTER_FROM') ?></label>
                                            <div class="field__input" data-calendar-label="data-calendar-label" data-date-from><?php if ($dateFrom): ?><?= $dateFrom ?><?php else: ?><span></span><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form__item">
                                        <div class="field field_icon field_calendar">
                                            <label><?= Loc::getMessage('FILTER_TO') ?></label>
                                            <div class="field__input" data-calendar-label="data-calendar-label" data-date-to><?php if ($dateTo): ?><?= $dateTo ?><?php else: ?><span></span><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="calendar__dropdown" data-calendar-dropdown="data-calendar-dropdown">
                                        <div class="calendar__navigation">
                                            <div class="calendar__navigation-item calendar__navigation-item_months">
                                                <div class="calendar__navigation-label" data-calendar-navigation="data-calendar-navigation"><span><?= $currMonthName ?></span></div>
                                                <ul class="list">
                                                    <?
                                                    $k = 0;
                                                    ?>
                                                    <? foreach ($arDates[0] as $monthName) : ?>
                                                        <li class="list__item<? if ($k == 0) : ?> list__item_active<? endif; ?>">
                                                            <button data-calendar-year="<?= $currYear ?>" data-calendar-month-select="<?= $k ?>" type="button"><?= $monthName ?></button>
                                                        </li>
                                                        <? $k++; ?>
                                                    <? endforeach ?>

                                                    <? foreach ($arDates[1] as $key => $monthName) : ?>

                                                        <li class="list__item">
                                                            <button data-calendar-year="<?= $nextYear ?>" data-calendar-month-select="<?= $k ?>" type="button"><?= $monthName ?></button>
                                                            <? if ($key === 0): ?>
                                                                <div class="list__item-year"><?= $nextYear ?></div>
                                                            <? endif; ?>
                                                        </li>
                                                        <? $k++; ?>
                                                    <? endforeach ?>
                                                </ul>
                                            </div>

                                            <div class="calendar__navigation-item calendar__navigation-item_years">
                                                <div class="calendar__navigation-label" data-calendar-navigation="data-calendar-navigation"><span><?= $currYear ?></span></div>
                                                <ul class="list">
                                                    <li class="list__item list__item_active">
                                                        <button data-calendar-year-select="<?= $currYear ?>" type="button"><?= $currYear ?></button>
                                                    </li>
                                                    <li class="list__item">
                                                        <button data-calendar-year-select="<?= $nextYear ?>" type="button"><?= $nextYear ?></button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="calendar__month">
                                            <input type="hidden" data-calendar-value="data-calendar-value">
                                        </div>

                                        <div class="calendar__dropdown-close">
                                            Закрыть
                                        </div>
                                    </div>
                                </div>
                                <div class="form__item guest">
                                    <div class="field field_icon guests" data-guests="data-guests">
                                        <div class="field__input"
                                            data-guests-control="data-guests-control"><?= $guests + $children ?> <?= $guestsDeclension->get($guests + $children) ?></div>
                                        <div class="guests__dropdown">
                                            <div class="guests__guests">
                                                <div class="guests__item">
                                                    <div class="guests__label">
                                                        <div><?= GetMessage('FILTER_ADULTS') ?></div>
                                                        <span><?= GetMessage('FILTER_ADULTS_AGE') ?></span>
                                                    </div>
                                                    <div class="counter">
                                                        <button class="counter__minus" type="button"></button>
                                                        <input type="text" disabled="disabled"
                                                            data-guests-adults-count="data-guests-adults-count"
                                                            name="guests-adults-count" value="<?= $guests ?>"
                                                            data-min="1">
                                                        <button class="counter__plus" type="button"></button>
                                                    </div>
                                                </div>
                                                <div class="guests__item">
                                                    <div class="guests__label">
                                                        <div><?= GetMessage('FILTER_CHILDREN') ?></div>
                                                        <span><?= GetMessage('FILTER_CHILDREN_AGE') ?></span>
                                                    </div>
                                                    <div class="counter">
                                                        <button class="counter__minus" type="button"></button>
                                                        <input type="text" disabled="disabled"
                                                            data-guests-children-count="data-guests-children-count"
                                                            name="guests-children-count" value="<?= $children ?>"
                                                            data-min="0">
                                                        <button class="counter__plus" type="button"></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="guests__children"
                                                data-guests-children="data-guests-children">
                                                <?php if ($arChildrenAge): ?>
                                                    <?php foreach ($arChildrenAge as $keyAge => $valueAge): ?>
                                                        <div class="guests__item">
                                                            <div class="guests__label">
                                                                <div><?= GetMessage('FILTER_CHILD_AGE') ?></div>
                                                                <span><?= getChildrenOrderTitle($keyAge + 1) ?> <?= GetMessage('FILTER_CHILD') ?></span>
                                                            </div>
                                                            <div class="counter">
                                                                <button class="counter__minus" type="button">
                                                                    <svg class="icon icon_arrow-small"
                                                                        viewBox="0 0 16 16"
                                                                        style="width: 1.6rem; height: 1.6rem;">
                                                                        <use xlink:href="#arrow-small"></use>
                                                                    </svg>
                                                                </button>
                                                                <input type="text" disabled=""
                                                                    data-guests-children=""
                                                                    name="guests-children-<?= $keyAge ?>"
                                                                    value="<?= $valueAge ?>"
                                                                    data-min="0" data-max="17">
                                                                <button class="counter__plus" type="button">
                                                                    <svg class="icon icon_arrow-small"
                                                                        viewBox="0 0 16 16"
                                                                        style="width: 1.6rem; height: 1.6rem;">
                                                                        <use xlink:href="#arrow-small"></use>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="guests__dropdown-close">
                                                Закрыть
                                            </div>
                                        </div>
                                    </div>
                                    <button class="button button_primary" data-filter-set data-filter-catalog-front-btn="true">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9697 16.9697C17.2626 16.6768 17.7374 16.6768 18.0303 16.9697L22.5303 21.4697C22.8232 21.7626 22.8232 22.2374 22.5303 22.5303C22.2374 22.8232 21.7626 22.8232 21.4697 22.5303L16.9697 18.0303C16.6768 17.7374 16.6768 17.2626 16.9697 16.9697Z" fill="white" />
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.25 11C1.25 5.61522 5.61522 1.25 11 1.25C16.3848 1.25 20.75 5.61522 20.75 11C20.75 16.3848 16.3848 20.75 11 20.75C5.61522 20.75 1.25 16.3848 1.25 11ZM11 2.75C6.44365 2.75 2.75 6.44365 2.75 11C2.75 15.5563 6.44365 19.25 11 19.25C15.5563 19.25 19.25 15.5563 19.25 11C19.25 6.44365 15.5563 2.75 11 2.75Z" fill="white" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="filters__controls">
                            <button class="button button_primary" data-filter-set data-filter-catalog-front-btn="true">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9697 16.9697C17.2626 16.6768 17.7374 16.6768 18.0303 16.9697L22.5303 21.4697C22.8232 21.7626 22.8232 22.2374 22.5303 22.5303C22.2374 22.8232 21.7626 22.8232 21.4697 22.5303L16.9697 18.0303C16.6768 17.7374 16.6768 17.2626 16.9697 16.9697Z" fill="white" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.25 11C1.25 5.61522 5.61522 1.25 11 1.25C16.3848 1.25 20.75 5.61522 20.75 11C20.75 16.3848 16.3848 20.75 11 20.75C5.61522 20.75 1.25 16.3848 1.25 11ZM11 2.75C6.44365 2.75 2.75 6.44365 2.75 11C2.75 15.5563 6.44365 19.25 11 19.25C15.5563 19.25 19.25 15.5563 19.25 11C19.25 6.44365 15.5563 2.75 11 2.75Z" fill="white" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="catalog_filter">
                    <form class="form filters" id="form-catalog-filter-front">
                        <div class="form__group">

                            <div class="form_group_wrapper">
                                <div class="form__item item_name">
                                    <div class="field field_autocomplete" data-autocomplete="/ajax/autocomplete.php">
                                        <input type="hidden" data-autocomplete-result
                                            value='<?= ($arFilterValues["SEARCH"]) ? $arFilterValues["SEARCH"] : null ?>'>
                                        <input class="field__input" type="text" name="name"
                                            placeholder="Укажите место или глэмпинг" data-autocomplete-field
                                            value='<?= ($arFilterValues["SEARCH_TEXT"]) ? $arFilterValues["SEARCH_TEXT"] : null ?>'>
                                        <div class="autocomplete-dropdown" data-autocomplete-dropdown></div>
                                    </div>
                                </div>

                                <div class="form_group_wrapper-filter_items">

                                    <div class="form__row calendar" data-calendar="data-calendar"
                                        data-calendar-min="today" data-calendar-max="365">
                                        <div class="form__item">
                                            <div class="field field_icon field_calendar">
                                                <div class="field__input" data-calendar-label="data-calendar-label"
                                                    data-date-from><?php if ($dateFrom): ?><?= $dateFrom ?><?php else: ?>
                                                    <span>Заезд</span><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form__item">
                                            <div class="field field_icon field_calendar">
                                                <div class="field__input" data-calendar-label="data-calendar-label"
                                                    data-date-to><?php if ($dateTo): ?><?= $dateTo ?><?php else: ?>
                                                    <span>Выезд</span><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="calendar__dropdown" data-calendar-dropdown="data-calendar-dropdown">
                                            <div class="calendar__navigation">
                                                <div class="calendar__navigation-item calendar__navigation-item_months">
                                                    <div class="calendar__navigation-label"
                                                        data-calendar-navigation="data-calendar-navigation">
                                                        <span><?= $currMonthName ?></span>
                                                    </div>
                                                    <ul class="list">
                                                        <?php
                                                        $k = 0;
                                                        ?>
                                                        <?php foreach ($arDates[0] as $monthName) : ?>
                                                            <li class="list__item<?php if ($k == 0) : ?> list__item_active<?php endif; ?>">
                                                                <button data-calendar-year="<?= $currYear ?>"
                                                                    class="list__item-month"
                                                                    data-calendar-month-select="<?= $k ?>"
                                                                    type="button"><?= $monthName ?></button>
                                                            </li>
                                                            <?php $k++; ?>
                                                        <?php endforeach ?>
                                                        <li class="list__item"
                                                            data-calendar-delimiter="data-calendar-delimiter">
                                                            <div class="list__item-year"><?= $nextYear ?></div>
                                                        </li>
                                                        <?php foreach ($arDates[1] as $monthName) : ?>
                                                            <li class="list__item">
                                                                <button data-calendar-year="<?= $nextYear ?>"
                                                                    class="list__item-month"
                                                                    data-calendar-month-select="<?= $k ?>"
                                                                    type="button"><?= $monthName ?></button>
                                                            </li>
                                                            <?php $k++; ?>
                                                        <?php endforeach ?>
                                                    </ul>
                                                </div>

                                                <div class="calendar__navigation-item calendar__navigation-item_years">
                                                    <div class="calendar__navigation-label"
                                                        data-calendar-navigation="data-calendar-navigation">
                                                        <span><?= $currYear ?></span>
                                                    </div>
                                                    <ul class="list">
                                                        <li class="list__item list__item_active">
                                                            <button data-calendar-year-select="<?= $currYear ?>"
                                                                type="button"><?= $currYear ?></button>
                                                        </li>
                                                        <li class="list__item">
                                                            <button data-calendar-year-select="<?= $nextYear ?>"
                                                                type="button"><?= $nextYear ?></button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="calendar__month">
                                                <input type="hidden" data-calendar-value="data-calendar-value">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form__item guest">
                                        <div class="field field_icon guests" data-guests="data-guests">
                                            <div class="field__input"
                                                data-guests-control="data-guests-control"><?= $guests + $children ?> <?= $guestsDeclension->get($guests + $children) ?></div>

                                            <div class="guests__dropdown">
                                                <div class="guests__guests">
                                                    <div class="guests__item">
                                                        <div class="guests__label">
                                                            <div><?= GetMessage('FILTER_ADULTS') ?></div>
                                                            <span><?= GetMessage('FILTER_ADULTS_AGE') ?></span>
                                                        </div>
                                                        <div class="counter">
                                                            <button class="counter__minus" type="button"></button>
                                                            <input type="text" disabled="disabled"
                                                                data-guests-adults-count="data-guests-adults-count"
                                                                name="guests-adults-count" value="<?= $guests ?>"
                                                                data-min="1">
                                                            <button class="counter__plus" type="button"></button>
                                                        </div>
                                                    </div>

                                                    <div class="guests__item">
                                                        <div class="guests__label">
                                                            <div><?= GetMessage('FILTER_CHILDREN') ?></div>
                                                            <span><?= GetMessage('FILTER_CHILDREN_AGE') ?></span>
                                                        </div>
                                                        <div class="counter">
                                                            <button class="counter__minus" type="button"></button>
                                                            <input type="text" disabled="disabled"
                                                                data-guests-children-count="data-guests-children-count"
                                                                name="guests-children-count" value="<?= $children ?>"
                                                                data-min="0">
                                                            <button class="counter__plus" type="button"></button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="guests__children"
                                                    data-guests-children="data-guests-children">
                                                    <?php if ($arChildrenAge): ?>
                                                        <?php foreach ($arChildrenAge as $keyAge => $valueAge): ?>
                                                            <div class="guests__item">
                                                                <div class="guests__label">
                                                                    <div><?= GetMessage('FILTER_CHILD_AGE') ?></div>
                                                                    <span><?= getChildrenOrderTitle($keyAge + 1) ?> <?= GetMessage('FILTER_CHILD') ?></span>
                                                                </div>
                                                                <div class="counter">
                                                                    <button class="counter__minus" type="button">
                                                                        <svg class="icon icon_arrow-small"
                                                                            viewBox="0 0 16 16"
                                                                            style="width: 1.6rem; height: 1.6rem;">
                                                                            <use xlink:href="#arrow-small"></use>
                                                                        </svg>
                                                                    </button>
                                                                    <input type="text" disabled=""
                                                                        data-guests-children=""
                                                                        name="guests-children-<?= $keyAge ?>"
                                                                        value="<?= $valueAge ?>"
                                                                        data-min="0" data-max="17">
                                                                    <button class="counter__plus" type="button">
                                                                        <svg class="icon icon_arrow-small"
                                                                            viewBox="0 0 16 16"
                                                                            style="width: 1.6rem; height: 1.6rem;">
                                                                            <use xlink:href="#arrow-small"></use>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="guests__dropdown-close">
                                                    Закрыть
                                                </div>
                                            </div>
                                        </div>


                                        <button class="button button_primary" data-filter-set
                                            data-filter-catalog-front-btn="true">Найти
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="filters__controls">
                                <button class="button button_primary" data-filter-set
                                    data-filter-catalog-front-btn="true">Найти
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            <?php endif; ?>
            <div class="catalog_sorter">
                <div class="filter_btn">
                    <a class="button filter" href="#filters-modal" data-modal="data-modal">
                        <span>Фильтры</span>
                    </a>
                    <?php if (CSite::InDir('/map')): ?>
                        <a href="/catalog/<?= ($_SERVER['QUERY_STRING'] !== '') ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" class="button button_primary catalog__map-halfscreen link__to_catalog">
                            <svg class="icon icon_arrow-text" viewbox="0 0 12 8" style="width: 1.2rem; height: 0.8rem;">
                                <use xlink:href="#arrow-text" />
                            </svg>
                            <span>Перейти к списку</span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="sort <?= (CSite::InDir('/map')) ? 'hidden' : '' ?>">
                    <span>Сортировать по:</span>
                    <ul class="list">
                        <li class="list__item">
                            <?php if ($sortBy == "sort"): ?>
                                <span class="list__link" data-sort="sort"
                                    data-type="<?= $orderReverse ?>"><span>По</span> <span>Наитию</span></span>
                            <?php else: ?>
                                <a class="list__link" href="#" data-sort="sort" data-type="asc"><span>По</span>
                                    <span>Наитию</span></a>
                            <?php endif; ?>
                        </li>
                        <!--<li class="list__item">
                            <?php /*if($sortBy == "popular"):*/ ?>
                                <span class="list__link" data-sort="popular" data-type="<?php /*=$orderReverse*/ ?>"><span>По</span> <span>Популярности</span></span>
                            <?php /*else:*/ ?>
                                <a class="list__link" href="#" data-sort="popular" data-type="<?php /*=$orderReverse*/ ?>"><span>По</span> <span>Популярности</span></a>
                            <?php /*endif;*/ ?>
                        </li>-->
                        <li class="list__item">
                            <?php if ($sortBy == "price"): ?>
                                <span class="list__link" data-sort="price"
                                    data-type="<?= $orderReverse ?>"><span>По</span> <span>Цене</span></span>
                            <?php else: ?>
                                <a class="list__link" href="#" data-sort="price" data-type="asc"><span>По</span>
                                    <span>Цене</span></a>
                            <?php endif; ?>
                        </li>
                        <li class="list__item">
                            <?php if ($sortBy == "rating"): ?>
                                <span class="list__link" data-sort="rating"
                                    data-type="<?= $orderReverse ?>"><span>По</span> <span>Рейтингу</span></span>
                            <?php else: ?>
                                <a class="list__link" href="#" data-sort="rating" data-type="desc"><span>По</span>
                                    <span>Рейтингу</span></a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </section>
    <!-- section-->

    <section class="section section_catalog">
        <div class="container">
            <?
            $APPLICATION->IncludeComponent(
                "naturalist:empty",
                "catalog",
                array(
                    "sortBy" => $sortBy,
                    "orderReverse" => $orderReverse,
                    "page" => $arParams["REAL_PAGE"] ? $arParams["REAL_PAGE"] : $page,
                    "pageCount" => $pageCount,
                    "allCount" => $allCount,
                    "countDeclension" => $countDeclension,
                    "reviewsDeclension" => $reviewsDeclension,
                    "arPageSections" => $arPageSections,
                    "arReviewsAvg" => $arReviewsAvg,
                    "arFavourites" => $arFavourites,
                    "arHLTypes" => $arHLTypes,
                    "arHLFeatures" => $arHLFeatures,
                    "arServices" => $arServices,
                    "arSearchedRegions" => is_array($arRegionIds) ? array_unique($arRegionIds) : '',
                    "searchedRegionData" => $searchedRegionData,
                    "searchName" => $searchName ?? $search,
                    "arFilterValues" => $arFilterValues,
                    "dateFrom" => $dateFrom,
                    "dateTo" => $dateTo,
                    "arDates" => $arDates,
                    "currMonthName" => $currMonthName,
                    "currYear" => $currYear,
                    "nextYear" => $nextYear,
                    "guests" => $guests,
                    "children" => $children,
                    "guestsDeclension" => $guestsDeclension,
                    "arChildrenAge" => $arChildrenAge,
                    "itemsCount" => $arParams["ITEMS_COUNT"],
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->
    <?php if (CSite::InDir('/map') == false): ?>
        <section class="cert-index__seo-text">
            <div class="container">
                <? if (!empty($arSeoImpressions) && reset($arSeoImpressions)['DETAIL_TEXT'] != '') {
                    echo reset($arSeoImpressions)['DETAIL_TEXT'];
                    $isSeoText = true;
                } else if (empty($_GET)) {
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => '/include/' . $seoFile . '-seo-text.php',
                            "EDIT_TEMPLATE" => ""
                        )
                    );
                    $isSeoText = false;
                } else if ($chySeoText) {
                    echo $chySeoText;
                    $isSeoText = true;
                } else if (isset($pageSeoData) && isset($pageSeoData['UF_SEO_TEXT'])) {
                    echo $pageSeoData['UF_SEO_TEXT'];
                    $isSeoText = true;
                } ?>
            </div>
        </section>
        <? if ($isSeoText) { ?>
            <div class="container">
                <a href="#" class="show-more-seo">Показать ещё</a>
            </div>
        <? } ?>
    <? endif; ?>
</main>

<div class="modal modal_filters" id="filters-modal">
    <div class="modal__container">
        <?
        $APPLICATION->IncludeComponent(
            "naturalist:empty",
            "catalog_filters",
            array(
                "arFilterValues" => $arFilterValues,
                "dateFrom" => $dateFrom,
                "dateTo" => $dateTo,
                "arDates" => $arDates,
                "currMonthName" => $currMonthName,
                "currYear" => $currYear,
                "nextYear" => $nextYear,
                "guests" => $guests,
                "children" => $children,
                "guestsDeclension" => $guestsDeclension,
                "arChildrenAge" => $arChildrenAge,
                "arHLTypes" => $arHLTypes,
                "arFilterTypes" => $arFilterTypes,
                "arServices" => $arServices,
                "arHLFood" => $arHLFood,
                "arFilterFood" => $arFilterFood,
                "arHLFeatures" => $arHLFeatures,
                "arFilterFeatures" => $arFilterFeatures,
                "arFilterServices" => $arFilterServices,
                "houseTypes" => $houseTypes,
                "arFilterHouseTypes" => $arFilterHousetypes,
                "restVariants" => $restVariants,
                "arFilterRestVariants" => $arFilterRestVariants,
                "objectComforts" => $objectComforts,
                "arFilterObjectComforts" => $arFilterObjectComforts,
            )
        );
        ?>
    </div>
</div>

<?
$APPLICATION->IncludeComponent(
    "naturalist:empty",
    "catalog_scripts",
    array(
        "arSections" => $arSections,
        "arFavourites" => $arFavourites,
        "arReviewsAvg" => $arReviewsAvg,
        "map" => $arParams["MAP"]
    )
);
