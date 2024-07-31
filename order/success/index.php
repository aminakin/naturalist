<?
/* Заказ оплачен */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Naturalist\Orders;

$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if (!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="' . $metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"] . '" />');
} else {
    $APPLICATION->SetTitle("Оплата заказа - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист");
    $APPLICATION->AddHeadString('<meta name="description" content="Оплата заказа | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования." />');
}

$orderId = $_REQUEST['orderId'];
$orders = new Orders;
if ($orderId) {
    $order = $orders->get($orderId);
}

?>

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
                <h1 class="h3">Заказ успешно оплачен</h1>
                <a href="/personal/active/">Перейти на страницу заказов</a>
            </div>
        </div>
    </section>
    <!-- section-->
</main>
<!-- main-->

<? if ($orderId) { ?>
    <script>
        dataLayer.push({
            ecommerce: {
                currencyCode: "RUB",
                purchase: {
                    actionField: {
                        id: '<?= $orderId ?>',
                    },
                    products: [{
                        id: <?= $order['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_ID'] ?>,
                        name: '<?= $order['ITEMS'][0]['ITEM']['SECTION']['NAME'] ?>',
                        price: <?= $order['FIELDS']['PRICE'] ?>,
                        //category: window.orderData.sectionName,
                        quantity: 1,
                        position: 1,
                    }, ],
                },
            },
        });
    </script>
<? } ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>