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
use Bitrix\Main\Loader;
use Naturalist\Products;
use Naturalist\Reviews;

global $arUser, $userId, $isAuthorized;

$APPLICATION->SetAdditionalCSS('https://unpkg.com/zuck.js/dist/zuck.css');
$APPLICATION->AddHeadScript('https://unpkg.com/zuck.js/dist/zuck.js');

$request = Application::getInstance()->getContext()->getRequest();
$isAjax  = $request->isAjaxRequest();

if (!$arResult['SECTION']) {
    include($_SERVER["DOCUMENT_ROOT"] . '/404.php');
    exit;
}

$arSection = $arResult['SECTION'];

/** Услуги */
if ($arSection["UF_SERVICES"]) {
    $rsServices = CIBlockElement::GetList(false, array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "!IBLOCK_SECTION_ID" => false, "ID" => $arSection["UF_SERVICES"]), false, false, array("IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME"));
    $arServices = array();
    while ($arService = $rsServices->Fetch()) {
        $arServices[] = $arService;
    }
}

$APPLICATION->SetTitle($arSection["NAME"]);

/* Номера */

// Фильтр номеров
$arFilter = $arResult['arFilter'];

$arExternalInfo = $arResult['arExternalInfo'];

// Список номеров
$rsElements = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, false, false, array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_TEXT", "PROPERTY_PHOTOS", "PROPERTY_FEATURES", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID", "PROPERTY_SQUARE", "PROPERTY_PARENT_ID", 'PROPERTY_ROOMS', 'PROPERTY_BEDS', 'PROPERTY_ROOMTOUR'));
$arElements = array();
while ($arElement = $rsElements->Fetch()) {
    if ($arElement["PROPERTY_PHOTOS_VALUE"]) {
        foreach ($arElement["PROPERTY_PHOTOS_VALUE"] as $photoId) {
            $roomImageOriginal = CFile::GetFileArray($photoId);
            $arElement["PICTURES"][$photoId] = [
                'src' => CFile::ResizeImageGet($photoId, array('width' => 464, 'height' => 328), BX_RESIZE_IMAGE_EXACT, true)['src'],
                'big' => CFile::GetFileArray($photoId)["SRC"],
            ];
        }
    } else {
        $arElement["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/no_photo.png";
    }

    if (!empty($arExternalInfo)) {
        $roomElement = current($arExternalInfo[$arElement["ID"]]);
        $arElement["PRICE"] = $roomElement["price"];
    } else {
        $arElement["PRICE"] = 0;
    }

    $discountData = CCatalogProduct::GetOptimalPrice($arElement['ID'], 1, $USER->GetUserGroupArray(), 'N');

    if (is_array($discountData['DISCOUNT']) && count($discountData['DISCOUNT'])) {
        $arElement['DISCOUNT_DATA']['VALUE'] = $discountData['DISCOUNT']['VALUE'];
        $arElement['DISCOUNT_DATA']['VALUE_TYPE'] = $discountData['DISCOUNT']['VALUE_TYPE'];
    }

    $arElements[$arElement['ID']] = $arElement;
}

if ($arSection["UF_EXTERNAL_SERVICE"] == "bnovo") {
    foreach ($arElements as $arElement) {
        if ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] > 0) {
            if (!isset($parentExternalIds) || !in_array(
                $arElement["PROPERTY_PARENT_ID_VALUE"],
                $parentExternalIds
            )) {
                $parentExternalIds[] = $arElement["PROPERTY_PARENT_ID_VALUE"];
            }
        }
    }

    if (isset($parentExternalIds) && !empty($parentExternalIds)) {
        unset($arFilter["ID"]);
        $arFilter["?PROPERTY_EXTERNAL_ID"] = implode('|', $parentExternalIds);
        $rsElements = CIBlockElement::GetList(false, $arFilter, false, false, array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_TEXT", "PROPERTY_PHOTOS", "PROPERTY_FEATURES", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID", "PROPERTY_SQUARE", "PROPERTY_PARENT_ID"));
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

            $arElementsParent[$arElement['PROPERTY_EXTERNAL_ID_VALUE']] = $arElement;
        }
    }
}

// Сортировка номеров по убыванию цены
usort($arElements, function ($a, $b) {
    return ($a['PRICE'] - $b['PRICE']);
});

if ($arSection["UF_EXTERNAL_SERVICE"] == "bnovo") {
    $arParams["DETAIL_ITEMS_COUNT"] = 999;
}

if (!empty($arExternalInfo)) {
    $minPrice = $arElements[0]['PRICE'];
} else {
    $minPrice = $arSection['UF_MIN_PRICE'];
}

// Пагинация номеров
$allCount = count($arElements);
if ($allCount > 0) {
    $page = $_REQUEST['page'] ?? 1;
    $pageCount = ceil($allCount / $arParams["DETAIL_ITEMS_COUNT"]);
    if ($pageCount > 1) {
        $arElements = array_slice(
            $arElements,
            ($page - 1) * $arParams["DETAIL_ITEMS_COUNT"],
            $arParams["DETAIL_ITEMS_COUNT"]
        );
    }
}

if (!isset($minPrice)) {
    $minPrice = $arSection['UF_MIN_PRICE'];
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
// Особенности объекта
$hlId = 5;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "filter" => ['ID' => $arSection['UF_FEATURES']],
    "order" => ["UF_SORT" => "ASC"],
]);
$arHLFeatures = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLFeatures[$arEntity["UF_XML_ID"]] = $arEntity;
    $arHLFeaturesIds[] = $arEntity["UF_XML_ID"];
}
// Особенности номера
$hlId = 8;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
]);

$arHLRoomFeatures = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLRoomFeatures[$arEntity["UF_XML_ID"]] = $arEntity;
}

$houseTypesDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();
$houseTypeData = $houseTypesDataClass::query()
    ->addSelect('*')
    ->setOrder(['UF_SORT' => 'ASC'])
    ?->fetchAll();
foreach ($houseTypeData as $houseType) {
    $arHouseTypes[$houseType['ID']] = $houseType;
}

// Удобства объекта
$objectComfortsDataClass = HighloadBlockTable::compileEntity(OBJECT_COMFORT_HL_ENTITY)->getDataClass();
$objectComfortsData = $objectComfortsDataClass::query()
    ->addSelect('*')
    ->where('ID', 'in', $arSection['UF_OBJECT_COMFORTS'])
    ->setOrder(['UF_SORT' => 'ASC'])
    ?->fetchAll();
foreach ($objectComfortsData as $objectComfort) {
    $arObjectComforts[$objectComfort['UF_XML_ID']] = $objectComfort;
    $arObjectComfortsIds[] = $objectComfort['UF_XML_ID'];
}

// Поиск детального описания удобства или развлечения
$featuresDetailList = \Bitrix\Iblock\Elements\ElementFeaturesdetailTable::getList([
    'select' => ['ID', 'NAME', 'FUN_VALUE' => 'FUN.VALUE', 'COMFORT_VALUE' => 'COMFORT.VALUE'],
    'filter' => [
        'OBJECT.VALUE' => $arSection['ID'],
        [
            "LOGIC" => "OR",
            ["COMFORT.VALUE" => $arObjectComfortsIds],
            ["FUN.VALUE" => $arHLFeaturesIds]
        ],
    ]
])->fetchAll();

foreach ($featuresDetailList as $featuresDetail) {
    if (isset($arObjectComforts[$featuresDetail['COMFORT_VALUE']])) {
        $arObjectComforts[$featuresDetail['COMFORT_VALUE']]['ELEMENT'] = $featuresDetail['ID'];
    }
    if (isset($arHLFeatures[$featuresDetail['FUN_VALUE']])) {
        $arHLFeatures[$featuresDetail['FUN_VALUE']]['ELEMENT'] = $featuresDetail['ID'];
    }
}

$APPLICATION->SetTitle($arResult['titleSEO']);
$APPLICATION->AddHeadString('<meta name="description" content="' . $arResult['descriptionSEO'] . '" />');
/**/
?>

<main class="main object__detail">
    <section class="section section_crumbs">
        <div class="container">
            <?
            $APPLICATION->IncludeComponent(
                "naturalist:empty",
                "object_breadcrumbs",
                array(
                    "arSection" => $arResult['SECTION'],
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->


    <?
    $APPLICATION->IncludeComponent(
        "naturalist:empty",
        "object_hero",
        array(
            "arSection" => $arSection,
            "arFavourites" => $arResult['FAVORITES'],
            "arHLTypes" => $arHLTypes,
            "dateFrom" => $arResult['arUriParams']['dateFrom'],
            "dateTo" => $arResult['arUriParams']['dateTo'],
            "arDates" => $arResult['arDates'],
            "currMonthName" => $arResult['currMonthName'],
            "currYear" => $arResult['currYear'],
            "nextYear" => $arResult['nextYear'],
            "guests" => $arResult['arUriParams']['guests'],
            "children" => $arResult['arUriParams']['children'],
            "guestsDeclension" => $arResult['guestsDeclension'],
            "childrenDeclension" => $arResult['childrenDeclension'],
            "arChildrenAge" => $arResult['arUriParams']['childrenAge'],
            "reviewsDeclension" => $arResult['reviewsDeclension'],
            "reviewsCount" => $arResult['reviewsCount'],
            "avgRating" => $arResult['avgRating'],
            "arAvgCriterias" => $arResult['arAvgCriterias'],
            "h1SEO" => $arResult['h1SEO'],
            "arHLFeatures" => $arHLFeatures,
            "coords" => $arResult['SECTION']['UF_COORDS'],
            "arServices" => $arServices,
            'houseTypeData' => $arHouseTypes,
            'allCount' => $allCount,
            "arHLRoomFeatures" => $arHLRoomFeatures,
            "arExternalInfo" => $arResult['arExternalInfo'],
            "arElements" => $arElements,
            "daysRange" => $arResult['daysRange'],
            "page" => $page,
            "pageCount" => $pageCount,
            "daysDeclension" => $arResult['daysDeclension'],
            "daysCount" => $arResult['daysCount'],
            "arElementsParent" => $arElementsParent ?? [],
            "arReviews" => $arResult['arReviews'],
            "reviewsSortType" => $arResult['reviewsSortType'],
            "arReviewsLikesData" => $arResult['arReviewsLikesData'],
            "arReviewsUsers" => $arResult['arReviewsUsers'],
            "reviewsPage" => $arResult['reviewsPage'],
            "reviewsPageCount" => $arResult['reviewsPageCount'],
            "isUserReview" => $arResult['isUserReview'],
            'roomsDeclension' => $arResult['roomsDeclension'],
            'bedsDeclension' => $arResult['bedsDeclension'],
            'arObjectComforts' => $arObjectComforts,
            'searchError' => $arResult['searchError'],
        )
    );
    ?>

    <? if ($arResult['FAQ']) { ?>
        <section class="section section_faq">
            <div class="container">
                <div class="faq__title">FAQ</div>
                <ul class="faq__list list">
                    <? foreach ($arResult['FAQ'] as $faq) { ?>
                        <li class="faq__item">
                            <div class="faq__item-title">
                                <?= $faq['NAME'] ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0697 7.99932C11.1326 6.93643 12.8675 6.93643 13.9304 7.99932L20.4504 14.5193C20.7433 14.8122 20.7433 15.2871 20.4504 15.58C20.1575 15.8729 19.6826 15.8729 19.3897 15.58L12.8697 9.05998C12.3926 8.58287 11.6075 8.58287 11.1304 9.05998L4.61041 15.58C4.31752 15.8729 3.84264 15.8729 3.54975 15.58C3.25685 15.2871 3.25685 14.8122 3.54975 14.5193L10.0697 7.99932Z" fill="black" />
                                </svg>
                            </div>
                            <div class="faq__item-content" style="display:none">
                                <?= $faq['PREVIEW_TEXT'] ?>
                            </div>
                        </li>
                    <? } ?>
                </ul>
            </div>
        </section>
    <? } ?>

    <section class="section section_related">
        <div class="container related-projects">
            <?
            global $arRelatedOffersFilter;
            $arRelatedOffersFilter = array(
                "!ID" => $arSection["ID"]
            );
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "catalog_offers",
                array(
                    "VIEW_MODE" => "TEXT",
                    "SHOW_PARENT_NAME" => "Y",
                    "IBLOCK_TYPE" => "",
                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                    "SECTION_ID" => "",
                    "SECTION_CODE" => "",
                    "SECTION_URL" => "",
                    "FILTER_NAME" => "arRelatedOffersFilter",
                    "COUNT_ELEMENTS" => "Y",
                    "TOP_DEPTH" => "1",
                    "SECTION_FIELDS" => "",
                    "SECTION_USER_FIELDS" => array("UF_*"),
                    "ADD_SECTIONS_CHAIN" => "Y",
                    "CACHE_TYPE" => "N",
                    "CACHE_TIME" => "36000000",
                    "CACHE_NOTES" => "",
                    "CACHE_GROUPS" => "N",
                    "SECTION_RATING" => $avgRating,
                    "SECTION_COORDS" => explode(",", $arSection["UF_COORDS"]),
                    "RATING_RANGE" => 0.5,
                    "COORDS_RANGE" => 2,
                    "ITEMS_COUNT" => 8,
                    "AR_SECTION" => $arSection,
                    "arHLTypes" => $arHLTypes,
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->
    <? if (isset($arSection['UF_DOP_SEO_TEXT']) && $arSection['UF_DOP_SEO_TEXT'] != '') { ?>
        <section class="cert-index__seo-text">
            <div class="container">
                <?= $arSection['UF_DOP_SEO_TEXT'] ?>
            </div>
        </section>
        <div class="container">
            <a href="#" class="show-more-seo">Раскрыть</a>
        </div>
    <? } ?>
</main>

<?
$APPLICATION->IncludeComponent(
    "naturalist:empty",
    "object_modals",
    array(
        'SECTION_IMGS' => $arSection["PICTURES"],
        'TITLE' => $arResult['h1SEO'],
        'OBJECT_COMFORTS' => $arObjectComforts,
        'OBJECT_FUN' => $arHLFeatures,
        'HOUSE_TYPES' => $arHouseTypes,
        'SECTION' => $arSection
    )
);
?>

<? if ($arSection["COORDS"]) : ?>
    <?
    $APPLICATION->IncludeComponent(
        "naturalist:empty",
        "object_scripts",
        array(
            "VARS" => array(
                "arSection" => $arSection,
                "arElements" => $arElements,
                "arExternalInfo" => $arResult['arExternalInfo'],
                "arHLRoomFeatures" => $arHLRoomFeatures,
                "avgRating" => $avgRating,
                "minPrice" => $minPrice,
            ),
            "CACHE_TYPE" => "N",
        )
    );
    ?>
<? endif; ?>