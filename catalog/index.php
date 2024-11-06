<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
?>
<script src="https://pay.yandex.ru/sdk/v1/pay.js" onload="onYaPayLoad()" async></script>
<?
$APPLICATION->IncludeComponent(
	"naturalist:catalog",
	"main",
	array(
		"IBLOCK_ID" => CATALOG_IBLOCK_ID,
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "Y",
		"SEF_FOLDER" => "/catalog/",
		"SEF_URL_TEMPLATES" => array(
			"section" => "#SECTION_CODE#/",
			"detail" => "#SECTION_CODE#/#ELEMENT_CODE#/"
		),
		"VARIABLE_ALIASES" => array(
			"section" => array(),
			"detail" => array(),
		),
		"ITEMS_COUNT" => 12,
		"DETAIL_ITEMS_COUNT" => 8,
		"DETAIL_REVIEWS_COUNT" => 3
	)
);
?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>