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

// Впечатления
if (!empty($_GET['impressions']) && isset($_GET['impressions'])) {
    $arRequestImpressions = explode(',', $_GET['impressions']);

    $rsImpressions = CIBlockElement::GetList(false, array("IBLOCK_ID" => IMPRESSIONS_IBLOCK_ID, "CODE" => $arRequestImpressions));
    $arFilterImpressions = array();
    while ($arImpression = $rsImpressions->Fetch()) {
        $arFilterImpressions[] = $arImpression["ID"];
        $arSeoImpressions[] = $arImpression;
    }    

    $arFilter["UF_IMPRESSIONS"] = $arFilterImpressions;
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

    $arUriParams = array_merge($arUriParams, $arUriParamsSort);

    $uri = new Uri($arSection["SECTION_PAGE_URL"]);
    $uri->addParams($arUriParams);
    $sectionUrl = $uri->getUri();
    $arSection["URL"] = $sectionUrl;

    $arButtons = CIBlock::GetPanelButtons($arSection["IBLOCK_ID"], $arSection["ID"], 0, array("SECTION_BUTTONS" => false, "SESSID" => false));
    $arSection["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
    $arSection["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

    $arSections[$arSection["ID"]] = $arSection;
}

if ($searchedRegionData) {

    usort($arSections, function ($a, $b) {
        return ($a['DISCTANCE'] - $b['DISCTANCE']);
    });
}
$allCount = count($arSections);

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
// Данные из сессии по предыдущему просмотру каталога
$session = Application::getInstance()->getSession();
$previousShowenItems = $session['catalog_showen_items'];
$nextPage = $session['current_catalog_page'];

if ($nextPage) {
    $page = intval($nextPage) - 1;
} else {
    $page = $_REQUEST['page'] ?? 1;
}

// Добавляем свойство Скидка, если есть хотя бы 1 элемент со скидкой
foreach ($arSections as $section) {
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

foreach ($arSections as &$section) {
    foreach ($arElementsBySection[$section['ID']] as $element) {
        $arPrice = CCatalogProduct::GetOptimalPrice($element['ID'], 1, $USER->GetUserGroupArray(), 'N');
        if (is_array($arPrice['DISCOUNT']) && count($arPrice['DISCOUNT'])) {
            $section['IS_DISCOUNT'] = 'Y';
            break;
        }
    }
}
unset($section);

$pageCount = ceil($allCount / $arParams["ITEMS_COUNT"]);
if ($pageCount > 1) {
    $arPageSections = array_slice($arSections, $previousShowenItems ? 0 : ($page - 1) * $arParams["ITEMS_COUNT"], $previousShowenItems ? $previousShowenItems : $arParams["ITEMS_COUNT"]);
} else {
    $arPageSections = $arSections;
}


/* HL Blocks */
// Тип объекта
$hlId = 2;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
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
        $impressionReplace = mb_strtolower($arSeoImpressions[0]["NAME"], "UTF-8");
    } else {
        $impressionReplace = "";
    }
    $titleSEO = str_replace(array("#IMPRESSIONS#", "#PAGE#"), array($impressionReplace, $page), $metaTags["/catalog/?impressions"]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $descriptionSEO = str_replace(array("#IMPRESSIONS#", "#PAGE#"), array($impressionReplace, $page), $metaTags["/catalog/?impressions"]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"]);
    $h1SEO = str_replace(array("#IMPRESSIONS#", "#PAGE#"), array($impressionReplace, $page), $metaTags["/catalog/?impressions"]["~PROPERTY_H1_VALUE"]["TEXT"]);
} elseif (isset($_GET["impressions"]) && !empty($_GET['impressions']) && !empty($metaTags["/catalog/?impressions"])) { //переход с раздела "Впечатления"
    if (!empty($arSeoImpressions)) {
        $impressionReplace = mb_strtolower($arSeoImpressions[0]["NAME"], "UTF-8");
    } else {
        $impressionReplace = "";
    }
    $titleSEO = str_replace("#IMPRESSIONS#", $impressionReplace, $metaTags["/catalog/?impressions"]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $descriptionSEO = str_replace("#IMPRESSIONS#", $impressionReplace, $metaTags["/catalog/?impressions"]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"]);
    $h1SEO = str_replace("#IMPRESSIONS#", $impressionReplace, $metaTags["/catalog/?impressions"]["~PROPERTY_H1_VALUE"]["TEXT"]);
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
    $h1SEO = "Каталог";
}

$APPLICATION->SetTitle($titleSEO);
$APPLICATION->AddHeadString('<meta name="description" content="' . $descriptionSEO . '" />');
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
                    <h1 class="page_title" <? if ($arParams["MAP"]): ?> style="visibility: hidden;"<? endif; ?>><?= $h1SEO; ?></h1>
                    <div class="crumbs__controls">
                        <!--<a class="crumbs__controls-mobile" href="#" data-map-full="data-map-full">Смотреть на карте</a>-->
<!--                        <a class="button button_transparent" target="_blank"-->
<!--                           href="https://yandex.ru/maps/?mode=routes&rtext=" data-route="data-route">Маршрут</a>-->

                    </div>
                </div>

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
                                                        <span>Заезд</span><?php endif; ?></div>
                                            </div>
                                        </div>

                                        <div class="form__item">
                                            <div class="field field_icon field_calendar">
                                                <div class="field__input" data-calendar-label="data-calendar-label"
                                                     data-date-to><?php if ($dateTo): ?><?= $dateTo ?><?php else: ?>
                                                        <span>Выезд</span><?php endif; ?></div>
                                            </div>
                                        </div>

                                        <div class="calendar__dropdown" data-calendar-dropdown="data-calendar-dropdown">
                                            <div class="calendar__navigation">
                                                <div class="calendar__navigation-item calendar__navigation-item_months">
                                                    <div class="calendar__navigation-label"
                                                         data-calendar-navigation="data-calendar-navigation">
                                                        <span><?= $currMonthName ?></span></div>
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
                                                        <span><?= $currYear ?></span></div>
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
                                            </div>
                                        </div>


                                        <button class="button button_primary" data-filter-set
                                                data-filter-catalog-front-btn="true">Найти
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="filters__controls">
                                <!--                    <button class="button button_clear" data-filter-reset>Сбросить всё</button>-->
                                <button class="button button_primary" data-filter-set
                                        data-filter-catalog-front-btn="true">Найти
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="catalog_sorter">
                    <div class="filter_btn">
                        <a class="button" href="#filters-modal" data-modal="data-modal">
                            <svg class="icon icon_filters" viewbox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                <use xlink:href="#filters"/>
                            </svg>
                            <span>Фильтры</span>
                        </a>
                    </div>

                    <div class="sort">
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
                    )
                );
                ?>
            </div>
        </section>
        <!-- section-->                
        <section class="cert-index__seo-text">
            <div class="container">
                <?if (!empty($arSeoImpressions) && reset($arSeoImpressions)['PREVIEW_TEXT'] != '') {
                    echo reset($arSeoImpressions)['PREVIEW_TEXT'];
                } else {
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                            "AREA_FILE_SHOW" => "file", 
                            "PATH" => '/include/'.$seoFile.'-seo-text.php',
                            "EDIT_TEMPLATE" => ""
                        )
                    );
                }?>
            </div>
        </section>
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
                    "arFilterServices" => $arFilterServices
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

$session = Application::getInstance()->getSession();
$session->remove('current_catalog_page');
$session->remove('catalog_showen_items');