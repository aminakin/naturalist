<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title","Купить подарочный сертификат | Naturalist.travel");
$APPLICATION->AddHeadString('<meta name="description" content="Купите подарочный сертификат на уникальные приключения с Naturalist Travel. Подарите своим близким незабываемые путешествия и встречи с природой." />');

$APPLICATION->SetTitle("Продажа сертификатов"); ?>
<script src="https://pay.yandex.ru/sdk/v1/pay.js" onload="onYaPayLoad()" async></script>
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
            <div class="wrapper_title_catalog_page">
                <h1 class="page_title"><? $APPLICATION->ShowTitle(false) ?></h1>
            </div>
        </div>
    </section>
    <? $APPLICATION->IncludeComponent(
        "addamant:certificates.buy",
        "",
        array(
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "POCKET_COST" => "690",
            "MIN_COST" => "500",
            "VARIANT_COST" => "300"
        )
    ); ?>
</main>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>