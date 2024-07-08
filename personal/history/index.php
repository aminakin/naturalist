<?
/* История заказов */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if(!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="'.$metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"].'" />');

} else {
	$APPLICATION->SetTitle("История путешествий - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист");
    $APPLICATION->AddHeadString('<meta name="description" content="История путешествий | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования." />');
}
?>

<?
global $isAuthorized;
if (!$isAuthorized) {
    $APPLICATION->IncludeComponent(
        "naturalist:empty", 
        "auth", 
        array()
    );
} else {
    $APPLICATION->IncludeComponent(
        "naturalist:empty", 
        "history", 
        array()
    );
}
?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>