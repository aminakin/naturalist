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

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>