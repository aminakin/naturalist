<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if (!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="' . $metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"] . '" />');
    $h1SEO = $metaTags[$currentURLDir]["~PROPERTY_H1_VALUE"]["TEXT"];
} else {
    $APPLICATION->SetTitle("Политика отмены заказов - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист");
    $APPLICATION->AddHeadString('<meta name="description" content="Политика отмены заказов | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования." />');
    $h1SEO = $arSettings['cancel_title'];
}
global $arSettings;
?>
<link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/contacts.css?v=1664554795104">
<link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/content.min.css?v=1664554795104">
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
                        <div class="sidebar-navigation__label" data-navigation-control="data-navigation-control"><span><?= $arSettings['cancel_title'] ?></span></div>
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
                    <h1 class="h3"><?= $h1SEO ?></h1>

                    <?= $arSettings['cancel_content'] ?>
                </div>
            </div>
        </div>
    </section>
    <!-- section-->
</main>
<!-- main-->

<script defer src="<?= SITE_TEMPLATE_PATH ?>/assets/js/content.min.js?v=1666102991761"></script>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>