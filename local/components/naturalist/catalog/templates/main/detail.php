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
use Bitrix\Main\Loader;

global $arUser, $userId, $isAuthorized;

$APPLICATION->SetAdditionalCSS('https://unpkg.com/zuck.js/dist/zuck.css');
$APPLICATION->AddHeadScript('https://unpkg.com/zuck.js/dist/zuck.js');

$request = Application::getInstance()->getContext()->getRequest();
$isAjax  = $request->isAjaxRequest();

if (!$arResult['SECTION']) {
    include($_SERVER["DOCUMENT_ROOT"] . '/404.php');
    exit;
}

$APPLICATION->SetTitle($arResult['SECTION']["NAME"]);
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
            "arSection" => $arResult['SECTION'],
            "arFavourites" => $arResult['FAVORITES'],
            "arHLTypes" => $arResult['arHLTypes'],
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
            "arHLFeatures" => $arResult['arHLFeatures'],
            "coords" => $arResult['SECTION']['UF_COORDS'],
            "arServices" => $arResult['SECTION']['arServices'],
            'houseTypeData' => $arResult['arHouseTypes'],
            'allCount' => $arResult['allCount'],
            "arHLRoomFeatures" => $arResult['arHLRoomFeatures'],
            "arExternalInfo" => $arResult['arExternalInfo'],
            "arElements" => $arResult['arElements'],
            "daysRange" => $arResult['daysRange'],
            "page" => $arResult['page'],
            "pageCount" => $arResult['pageCount'],
            "daysDeclension" => $arResult['daysDeclension'],
            "daysCount" => $arResult['daysCount'],
            "arElementsParent" => $arResult['arElementsParent'],
            "arReviews" => $arResult['arReviews'],
            "reviewsSortType" => $arResult['reviewsSortType'],
            "arReviewsLikesData" => $arResult['arReviewsLikesData'],
            "arReviewsUsers" => $arResult['arReviewsUsers'],
            "reviewsPage" => $arResult['reviewsPage'],
            "reviewsPageCount" => $arResult['reviewsPageCount'],
            "isUserReview" => $arResult['isUserReview'],
            'roomsDeclension' => $arResult['roomsDeclension'],
            'bedsDeclension' => $arResult['bedsDeclension'],
            'arObjectComforts' => $arResult['arObjectComforts'],
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
                "!ID" => $arResult['SECTION']["ID"]
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
                    "SECTION_RATING" => $arResult['avgRating'],
                    "SECTION_COORDS" => explode(",", $arResult['SECTION']["UF_COORDS"]),
                    "RATING_RANGE" => 0.5,
                    "COORDS_RANGE" => 2,
                    "ITEMS_COUNT" => 8,
                    "AR_SECTION" => $arResult['SECTION'],
                    "arHLTypes" => $arResult['arHLTypes'],
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->
    <? if (isset($arResult['SECTION']['UF_DOP_SEO_TEXT']) && $arResult['SECTION']['UF_DOP_SEO_TEXT'] != '') { ?>
        <section class="cert-index__seo-text">
            <div class="container">
                <?= $arResult['SECTION']['UF_DOP_SEO_TEXT'] ?>
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
        'SECTION_IMGS' => $arResult['SECTION']["PICTURES"],
        'TITLE' => $arResult['h1SEO'],
        'OBJECT_COMFORTS' => $arResult['arObjectComforts'],
        'OBJECT_FUN' => $arResult['arHLFeatures'],
        'HOUSE_TYPES' => $arResult['arHouseTypes'],
        'SECTION' => $arResult['SECTION']
    )
);
?>

<? if ($arResult['SECTION']["COORDS"]) : ?>
    <?
    $APPLICATION->IncludeComponent(
        "naturalist:empty",
        "object_scripts",
        array(
            "VARS" => array(
                "arSection" => $arResult['SECTION'],
                "arElements" => $arResult['arElements'],
                "arExternalInfo" => $arResult['arExternalInfo'],
                "arHLRoomFeatures" => $arResult['arHLRoomFeatures'],
                "avgRating" => $arResult['avgRating'],
                "minPrice" => $arResult['minPrice'],
            ),
            "CACHE_TYPE" => "N",
        )
    );
    ?>
<? endif; ?>