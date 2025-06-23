<?
/* Впечатления */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $arSettings;
$APPLICATION->SetPageProperty("title","Путеводитель по природным чудесам | Naturalist.travel");
$APPLICATION->AddHeadString('<meta name="description" content="Откройте мир удивительных путешествий и незабываемых впечатлений с Naturalist Travel. Погрузитесь в уникальные истории наших путешественников и вдохновитесь на новые приключения." />');

$APPLICATION->SetTitle("Подборки");
?>

<main class="main">
    <section class="section section_crumbs">
        <div class="container">
            <div class="crumbs">
                <ul class="list crumbs__list" itemscope itemtype="http://schema.org/BreadcrumbList">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:breadcrumb", 
                        "main", 
                        array(
                            "PATH" => "",
                            "SITE_ID" => "s1",
                            "START_FROM" => "0",
                            "COMPONENT_TEMPLATE" => "main"
                        ),
                        false
                    );
                    ?>
                </ul>
            </div>
            <div class="wrapper_title_catalog_page">
                <h1 class="page_title"><? $APPLICATION->ShowTitle(false) ?></h1>
            </div>
        </div>
    </section>
    <!-- section-->
    
    <?if (false/*Bitrix\Main\Engine\CurrentUser::get()->isAdmin()*/) {?>
        <?
            $APPLICATION->IncludeComponent(
                "bitrix:news", 
                "impressions2", 
                array(
                    "COMPONENT_TEMPLATE" => "impressions2",
                    "IBLOCK_TYPE" => "catalog",
                    "IBLOCK_ID" => IMPRESSIONS_IBLOCK_ID,
                    "NEWS_COUNT" => "200",
                    "USE_SEARCH" => "N",
                    "USE_RSS" => "N",
                    "USE_RATING" => "N",
                    "USE_CATEGORIES" => "N",
                    "USE_REVIEW" => "N",
                    "USE_FILTER" => "N",
                    "SORT_BY1" => "SORT",
                    "SORT_ORDER1" => "ASC",
                    "SORT_BY2" => "ACTIVE_FROM",
                    "SORT_ORDER2" => "DESC",
                    "CHECK_DATES" => "Y",
                    "SEF_MODE" => "Y",
                    "SEF_FOLDER" => "/impressions/",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "SET_LAST_MODIFIED" => "N",
                    "SET_TITLE" => "N",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "Y",
                    "ADD_ELEMENT_CHAIN" => "N",
                    "USE_PERMISSIONS" => "N",
                    "STRICT_SECTION_CHECK" => "N",
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "USE_SHARE" => "N",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "LIST_FIELD_CODE" => array(
                        0 => "",
                        1 => "",
                    ),
                    "LIST_PROPERTY_CODE" => array(
                        0 => "",
                        1 => "LINK",
                        2 => "",
                    ),
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "DISPLAY_NAME" => "Y",
                    "META_KEYWORDS" => "-",
                    "META_DESCRIPTION" => "-",
                    "BROWSER_TITLE" => "-",
                    "DETAIL_SET_CANONICAL_URL" => "N",
                    "DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "DETAIL_FIELD_CODE" => array(
                        0 => "",
                        1 => "",
                    ),
                    "DETAIL_PROPERTY_CODE" => array(
                        0 => "",
                        1 => "",
                    ),
                    "DETAIL_DISPLAY_TOP_PAGER" => "N",
                    "DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
                    "DETAIL_PAGER_TITLE" => "Страница",
                    "DETAIL_PAGER_TEMPLATE" => "",
                    "DETAIL_PAGER_SHOW_ALL" => "Y",
                    "PAGER_TEMPLATE" => ".default",
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "PAGER_TITLE" => "Новости",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "SET_STATUS_404" => "N",
                    "SHOW_404" => "N",
                    "MESSAGE_404" => "",
                    "SEF_URL_TEMPLATES" => array(
                        "news" => "",
                        "section" => "#SECTION_CODE_PATH#/",
                        "detail" => "#ELEMENT_CODE#/",
                    )
                ),
                false
            );
        ?>
        <?} else {?>

        <section class="section section_title_page">
            <div class="container">
                <h1 class="page_title"><?= $h1SEO; ?></h1>
            </div>
        </section>
        <!-- section-->

        <section class="section section_impressions">
            <div class="container">
                <div class="impressions-text">
                    <?=$arSettings['impressions_text']?>
                </div>            
                    <ul class="list list_impressions">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:news.list",
                            "impressions",
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
                        );
                        ?>
                    </ul>
                
            </div>
        </section>
    <?}?>
    <!-- section-->
</main>
<!-- main-->

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>