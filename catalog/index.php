<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetAdditionalCSS('https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css');
$APPLICATION->AddHeadScript('https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js');
?>
<script src="https://pay.yandex.ru/sdk/v1/pay.js" onload="onYaPayLoad()" async></script>
<?php
initCatalog();

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
    "DETAIL_ITEMS_COUNT" => 6,
    "DETAIL_REVIEWS_COUNT" => 3
  )
);
?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>