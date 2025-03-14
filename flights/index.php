<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

	$APPLICATION->SetTitle("Натуралист");
    // $APPLICATION->AddHeadString('<meta name="description" content="Главная | Naturalist.travel" />');
?>
<div class="wrapper-f">
<?
$APPLICATION->IncludeComponent(
    "naturalist:empty", 
    "flights", 
    array()
);
?>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>