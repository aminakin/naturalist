<?
/* Главная страница */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if(!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="'.$metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"].'" />');

} else {
	$APPLICATION->SetTitle("Главная | Naturalist.travel");
    $APPLICATION->AddHeadString('<meta name="description" content="Главная | Naturalist.travel" />');
}
?>

<?
$APPLICATION->IncludeComponent(
    "naturalist:empty", 
    "main", 
    array()
);
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:subscribe.form", 
	"subscribe-footer", 
	array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"PAGE" => "",
		"SHOW_HIDDEN" => "N",
		"USE_PERSONALIZATION" => "N",
		"COMPONENT_TEMPLATE" => "subscribe-footer",
		"FORM_TITLE" => "Оставайтесь на связи! Подпишитесь на нашу рассылку",
		"FORM_SUBTITLE" => "Узнавайте первыми о горящих предложениях, новых маршрутах и эксклюзивных скидках!",		
		"FORM_POLITICS_LINK" => "/policy/"
	),
	false
);?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>