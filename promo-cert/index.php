<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Промо сертификаты");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/index.css');
$APPLICATION->SetAdditionalCSS('https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css');
$APPLICATION->AddHeadScript('https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js');
$APPLICATION->AddHeadScript('https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js');

?>
<div class="certs__container">
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:news.detail",
        "cert-promo",
        array(
            "COMPONENT_TEMPLATE" => ".default",
            "IBLOCK_TYPE" => "main",
            "IBLOCK_ID" => "29",
            "ELEMENT_ID" => "",
            "ELEMENT_CODE" => "cert_promo",
            "CHECK_DATES" => "Y",
            "FIELD_CODE" => array(
                0 => "ID",
                1 => "",
            ),
            "PROPERTY_CODE" => array(
                0 => "BLOCK1_TITLE",
                1 => "",
            ),
            "IBLOCK_URL" => "",
            "DETAIL_URL" => "",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000",
            "CACHE_GROUPS" => "Y",
            "SET_TITLE" => "N",
            "SET_CANONICAL_URL" => "N",
            "SET_BROWSER_TITLE" => "N",
            "BROWSER_TITLE" => "-",
            "SET_META_KEYWORDS" => "N",
            "META_KEYWORDS" => "-",
            "SET_META_DESCRIPTION" => "N",
            "META_DESCRIPTION" => "-",
            "SET_LAST_MODIFIED" => "N",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "N",
            "ADD_ELEMENT_CHAIN" => "N",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "USE_PERMISSIONS" => "N",
            "STRICT_SECTION_CHECK" => "N",
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "USE_SHARE" => "N",
            "PAGER_TEMPLATE" => ".default",
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "PAGER_TITLE" => "Страница",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "SET_STATUS_404" => "N",
            "SHOW_404" => "N",
            "MESSAGE_404" => "",
        ),
        false
    );
    ?>
    <section class="certs__form">
        <? $APPLICATION->IncludeComponent(
            "bitrix:subscribe.form",
            "subscribe-footer",
            array(
                "CACHE_TIME" => "3600",
                "CACHE_TYPE" => "A",
                "PAGE" => "",
                "SHOW_HIDDEN" => "N",
                "USE_PERSONALIZATION" => "N",
                "COMPONENT_TEMPLATE" => "subscribe-footer",
                "FORM_TITLE" => "Подпишитесь на рассылку",
                "FORM_SUBTITLE" => "Будьте в курсе выгодных цен и первыми узнавайте о новых локациях!",
                "FORM_POLITICS_LINK" => "/policy/"
            ),
            false
        ); ?>
    </section>
    <section class="certs__faq">
        <?php
        $APPLICATION->includeComponent(
            'naturalist:certificates.index',
            'promo',
            []
        );
        ?>
    </section>
</div>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>