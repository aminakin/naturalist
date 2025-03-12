<?

use Naturalist\Utils;

foreach ($arResult as $key => $value) {
    ${$key} = $value;
}
use Bitrix\Main\Localization\Loc;
?>

<div class="flights-widget" style="background: url(<?= SITE_TEMPLATE_PATH ?>/assets/img/background-widget.png);">
    <div class="flights-widget__preview d-f j-c a-c f-d-c">
        <div class="preview__title">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/naturalist_logo.png" alt="logo_naturalist">
        </div>
        <div class="preview__slogan">
            <h1>Поиск авиабилетов <br> к лучшим глэмпингам</h1>
        </div>
    </div>
</div>

<div class="flights-form d-f j-c a-c">
<? // вывод виджета с формой ?>
</div>

<div class="popular-places">
    <div class="popular-places__title">
        <h2>Популярный направления перелетов</h2>
    </div>

    <div class="popular-places__widget-items d-f j-s-b f-w ">
    <? // вывод виджетов с пополуярными перелетами ?>
    </div>
</div>

<div class="flights-map d-f j-c">
   <? //вывод виджета карты ?>
</div>

<section class="why-naturalist">
    <div class="container">
        <div class="why-naturalist__title">
            Почему Натуралист?
        </div>
        <div class="why-naturalist__wrapper">
            <div class="why-naturalist__block">
                <? $APPLICATION->IncludeFile("/include/why-naturalist/why-block1.php"); ?>
            </div>
            <div class="why-naturalist__block">
                <? $APPLICATION->IncludeFile("/include/why-naturalist/why-block2.php"); ?>
            </div>
            <div class="why-naturalist__block">
                <? $APPLICATION->IncludeFile("/include/why-naturalist/why-block3.php"); ?>
            </div>
            <div class="why-naturalist__block">
                <? $APPLICATION->IncludeFile("/include/why-naturalist/why-block4.php"); ?>
            </div>
        </div>
    </div>
</section>
<div class="tt">
<? $APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "main_slider",
    array(
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "AJAX_MODE" => "N",
        "IBLOCK_TYPE" => "",
        "IBLOCK_ID" => MAIN_SLIDER_IBLOCK_ID,
        "NEWS_COUNT" => "1000",
        "SORT_BY1" => "SORT",
        "SORT_ORDER1" => "ASC",
        "SORT_BY2" => "ACTIVE_FROM",
        "SORT_ORDER2" => "DESC",
        "FILTER_NAME" => "",
        "FIELD_CODE" => [0 => 'DETAIL_PICTURE'],
        "PROPERTY_CODE" => [0 => 'LINK'],
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
        "CACHE_TYPE" => "N",
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
</div>

<div class="gg">
<? $APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "impressions_slider",
    array(
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "AJAX_MODE" => "N",
        "IBLOCK_TYPE" => "",
        "IBLOCK_ID" => IMPRESSIONS_IBLOCK_ID,
        "NEWS_COUNT" => "1000",
        "SORT_BY1" => "SORT",
        "SORT_ORDER1" => "ASC",
        "SORT_BY2" => "ACTIVE_FROM",
        "SORT_ORDER2" => "DESC",
        "FILTER_NAME" => "",
        "FIELD_CODE" => array("ID"),
        "PROPERTY_CODE" => array("LINK", ""),
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
</div>

<!-- main-->
<title>Дешевые авиабилеты и отели</title>
<meta name="description" content="Сравниваем цены с сотен сайтов и позволяем вам выбрать самый дешевый вариант перелета или лучшую цену на отель."/>
<meta property="og:title" content="Поиск дешевых авиабилетов и отелей" />
<meta property="og:description" content="Сравниваем цены с сотен сайтов и позволяем вам выбрать самый дешевый вариант перелета или лучшую цену на отель." />
<meta content="ru_RU" property="og:locale">
<meta content="product.item" property="og:type">
<meta content="[:og_image:]" property="og:image">

<meta content="Поиск дешевых авиабилетов и отелей" name="twitter:title">
<meta content="Сравниваем цены с сотен сайтов и позволяем вам выбрать самый дешевый вариант перелета или лучшую цену на отель." name="twitter:description">
<meta content="summary_large_image" name="twitter:card">
