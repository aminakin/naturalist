<?
/* Контакты */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if (!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="' . $metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"] . '" />');
    $h1SEO = $metaTags[$currentURLDir]["~PROPERTY_H1_VALUE"]["TEXT"];
} else {
    $APPLICATION->SetTitle("Контакты - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист");
    $APPLICATION->AddHeadString('<meta name="description" content="Контакты | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования." />');
    $h1SEO = $arSettings['contacts_title'];
}
global $arSettings;
?>

<link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/contacts.css?v=1664554795104">
<main class="main">
    <section class="section section_crumbs">
        <div class="container">
            <div class="crumbs">
                <ul class="list crumbs__list">
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
        </div>
    </section>
    <!-- section-->

    <section class="section section_content">
        <div class="container">
            <div class="content">
                <div class="content__sidebar">
                    <div class="sidebar-navigation">
                        <div class="sidebar-navigation__label" data-navigation-control="data-navigation-control"><span><?= $arSettings['contacts_title'] ?></span></div>
                        <ul class="list">
                            <?
                            $APPLICATION->IncludeComponent(
                                "bitrix:menu",
                                "footer",
                                array(
                                    "ROOT_MENU_TYPE" => "content",
                                    "MAX_LEVEL" => "1",
                                    "CHILD_MENU_TYPE" => "",
                                    "USE_EXT" => "N",
                                    "DELAY" => "N",
                                    "ALLOW_MULTI_SELECT" => "Y",
                                    "MENU_CACHE_TYPE" => "N",
                                    "MENU_CACHE_TIME" => "3600",
                                    "MENU_CACHE_USE_GROUPS" => "Y",
                                    "MENU_CACHE_GET_VARS" => ""
                                ),
                                false
                            );
                            ?>
                        </ul>
                    </div>

                    <a class="button button_primary" href="#feedback" data-modal>Связаться с нами</a>
                </div>

                <div class="content__article">
                    <h1 class="h3"><?= $h1SEO; ?></h1>

                    <div class="contacts">
                        <div class="contacts__group">
                            <div class="contacts__item">
                                <div class="contacts__label"><?= $arSettings['contacts_email_label'] ?></div>
                                <div class="contacts__content">
                                    <?= $arSettings['contacts_email_content'] ?>
                                </div>
                            </div>

                            <div class="contacts__item">
                                <div class="contacts__label"><?= $arSettings['contacts_address_label'] ?></div>
                                <div class="contacts__content"><?= $arSettings['contacts_address_content'] ?></div>
                            </div>
                        </div>

                        <div class="contacts__item">
                            <div class="contacts__label"><?= $arSettings['contacts_phone_label'] ?></div>
                            <div class="contacts__content">
                                <ul class="list">
                                    <?= $arSettings['contacts_phone_content'] ?>
                                </ul>
                            </div>
                        </div>

                        <div class="contacts__item">
                            <div class="contacts__label"><?= $arSettings['contacts_socials_label'] ?></div>
                            <div class="contacts__content">
                                <ul class="list">
                                    <?
                                    global $arSocialsFilter;
                                    $arSocialsFilter = array(
                                        "PROPERTY_VIEW_CONTACTS_PAGE_VALUE" => "Y"
                                    );
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:news.list",
                                        "contacts_socials",
                                        array(
                                            "DISPLAY_DATE" => "Y",
                                            "DISPLAY_NAME" => "Y",
                                            "DISPLAY_PICTURE" => "Y",
                                            "DISPLAY_PREVIEW_TEXT" => "Y",
                                            "AJAX_MODE" => "N",
                                            "IBLOCK_TYPE" => "",
                                            "IBLOCK_ID" => SOCIALS_IBLOCK_ID,
                                            "NEWS_COUNT" => "8",
                                            "SORT_BY1" => "SORT",
                                            "SORT_ORDER1" => "ASC",
                                            "SORT_BY2" => "ACTIVE_FROM",
                                            "SORT_ORDER2" => "DESC",
                                            "FILTER_NAME" => "arSocialsFilter",
                                            "FIELD_CODE" => array("ID"),
                                            "PROPERTY_CODE" => array("LINK", "VIEW_CONTACTS_PAGE"),
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
                        </div>
                    </div>
                    <img class="map-image" src="/img/map.jpg" width="632" alt="Карта">
                </div>
            </div>
        </div>
    </section>
    <!-- section-->
</main>
<!-- main-->

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>