<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

	$APPLICATION->SetTitle("Натуралист");
    // $APPLICATION->AddHeadString('<meta name="description" content="Главная | Naturalist.travel" />');
?>

<?
$APPLICATION->IncludeComponent(
    "naturalist:empty", 
    "flights", 
    array()
);
?>
<style>
    tp-cascoon {
    z-index: 100;
    top: 56px;
    margin-top: 0px;
    position: absolute;
}
.wrapper {
    margin-top: 200px;
}
@media (max-width: 767px){
    .wrapper {
        margin-top: 420px;
    }
}
</style>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>