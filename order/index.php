<?
/* Оформление заказа */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if(!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="'.$metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"].'" />');

} else {
	$APPLICATION->SetTitle("Оформление заказа - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист");
    $APPLICATION->AddHeadString('<meta name="description" content="Оформление заказа | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования." />');
}
?>
<script src="https://pay.yandex.ru/sdk/v1/pay.js" onload="onYaPayLoad()" async></script>

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
        </div>
    </section>
    <!-- section-->

    <section class="section section_reservation">
        <div class="container">
            <?
            $APPLICATION->IncludeComponent(
                "naturalist:empty", 
                "order", 
                array()
            );
            ?>
        </div>
    </section>
</main>
<!-- main-->

<div class="modal modal_form modal_auth" id="attention">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Ошибка!</div>

        <p>
            Ошибка оформления заказа!
        </p>
    </div>
</div>

<script>
    $('document').ready(function(){
        window.modal.open("attention");
    });
</script>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>