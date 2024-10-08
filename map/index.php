<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
//$APPLICATION->SetTitle("Карта глэмпингов в России");
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
		"SEF_URL_TEMPLATES" => array(
			"section" => "#SECTION_CODE#/",
			"detail" => "#SECTION_CODE#/#ELEMENT_CODE#/"
		),
		"VARIABLE_ALIASES" => array(
			"section" => array(),
			"detail" => array(),
		),
		"ITEMS_COUNT" => 10,
		"DETAIL_ITEMS_COUNT" => 8,
		"DETAIL_REVIEWS_COUNT" => 3,
		"MAP" => true,
	)
);
?>
<div class="container">
	<section class="cert-index__seo-text catalog_map">
		<?php
		$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => '/include/map-seo-text.php',
				"EDIT_TEMPLATE" => ""
			)
		);
		?>
	</section>
	<a href="#" class="show-more-seo">Раскрыть</a>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>