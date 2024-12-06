<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if(!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="'.$metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"].'" />');

} else {
	$APPLICATION->SetTitle("Локации | Naturalist.travel");
    $APPLICATION->AddHeadString('<meta name="description" content="Локации | Naturalist.travel" />');
}

global $arSettings;
?>
<style>*{
  font-family: 'Lato', sans-serif!important;
}
</style>
<section class="section section_crumbs">
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
    </div>
    <? $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "location_page",
        array(
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "AJAX_MODE" => "N",
            "IBLOCK_TYPE" => "",
            "IBLOCK_ID" => "",
            "NEWS_COUNT" => "1000",
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "ACTIVE_FROM",
            "SORT_ORDER2" => "DESC",
            "FILTER_NAME" => "",
            "FIELD_CODE" => [],
            "PROPERTY_CODE" => [],
            "CHECK_DATES" => "Y",
            "DETAIL_URL" => "",
            "PREVIEW_TRUNCATE_LEN" => "",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "SET_TITLE" => "N",
            "SET_BROWSER_TITLE" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_LAST_MODIFIED" => "N",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "N",
            "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
            "PARENT_SECTION" => "",
            "PARENT_SECTION_CODE" => "",
            "INCLUDE_SUBSECTIONS" => "Y",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "N",
            "DISPLAY_TOP_PAGER" => "Y",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "PAGER_TITLE" => "",
            "PAGER_SHOW_ALWAYS" => "Y",
            "PAGER_TEMPLATE" => "",
            "PAGER_DESC_NUMBERING" => "Y",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "Y",
            "PAGER_BASE_LINK_ENABLE" => "Y",
            "SET_STATUS_404" => "N",
            "SHOW_404" => "N",
            "MESSAGE_404" => "",
            "PAGER_BASE_LINK" => "",
            "PAGER_PARAMS_NAME" => "arrPager",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
        )
    ); ?>

<section class="cert-index__seo-text">
    <div class="container">
        <?php
        $APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => '/include/regions-seo-text.php',
                "EDIT_TEMPLATE" => ""
            )
        );
        ?>
    </div>
</section>
<div class="container">
    <a href="#" class="show-more-seo">Раскрыть</a>
</div>

</section>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>