<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
CModule::IncludeModule("iblock");

$rsElements = CIBlockElement::GetList(["SORT" => "ASC"], ["IBLOCK_ID" => IMPRESSIONS_IBLOCK_ID, "ACTIVE" => "Y"], false, false, ["IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_LINK"]);
while ($obElement = $rsElements->GetNextElement()) {
	$arFields = $obElement->GetFields();
	if ($arFields['IBLOCK_SECTION_ID'] != '' || $arFields['PROPERTY_LINK_VALUE'] == '') {
		continue;
	}
	$aMenuLinksExt[] = array(
		$arFields['NAME'],
		$arFields['PROPERTY_LINK_VALUE'],
		array(),
		array(),
		""
	);
}

$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
