<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
//$APPLICATION->SetTitle("Карта");
?>

<?
$APPLICATION->IncludeComponent(
	"naturalist:catalog",
	"main",
	array(
		"IBLOCK_ID" => CATALOG_IBLOCK_ID,
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "Y",
		"SEF_FOLDER" => "/map/",
		"SEF_URL_TEMPLATES" => Array(
			"section" => "#SECTION_CODE#/",
			"detail" => "#SECTION_CODE#/#ELEMENT_CODE#/"
		),
		"VARIABLE_ALIASES" => Array(
			"section" => Array(),
			"detail" => Array(),
		),
		"ITEMS_COUNT" => 10,
		"DETAIL_ITEMS_COUNT" => 8,
		"DETAIL_REVIEWS_COUNT" => 3,
		"MAP" => true,
	)
);
?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>