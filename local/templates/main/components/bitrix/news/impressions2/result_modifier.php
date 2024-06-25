<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}
/** @var array $arParams */
$arParams['USE_SHARE'] = (string)($arParams['USE_SHARE'] ?? 'N');
$arParams['USE_SHARE'] = $arParams['USE_SHARE'] === 'Y' ? 'Y' : 'N';
$arParams['SHARE_HIDE'] = (string)($arParams['SHARE_HIDE'] ?? 'N');
$arParams['SHARE_HIDE'] = $arParams['SHARE_HIDE'] === 'Y' ? 'Y' : 'N';
$arParams['SHARE_TEMPLATE'] = (string)($arParams['SHARE_TEMPLATE'] ?? 'N');
$arParams['SHARE_HANDLERS'] ??= [];
$arParams['SHARE_HANDLERS'] = is_array($arParams['SHARE_HANDLERS']) ? $arParams['SHARE_HANDLERS'] : [];
$arParams['SHARE_SHORTEN_URL_LOGIN'] = (string)($arParams['SHARE_SHORTEN_URL_LOGIN'] ?? 'N');
$arParams['SHARE_SHORTEN_URL_KEY'] = (string)($arParams['SHARE_SHORTEN_URL_KEY'] ?? 'N');

$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if(!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="'.$metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"].'" />');
    $arResult['h1SEO'] = $metaTags[$currentURLDir]["~PROPERTY_H1_VALUE"]["TEXT"];
} else {
	$APPLICATION->SetTitle("Впечатления - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист");
    $APPLICATION->AddHeadString('<meta name="description" content="Впечатления | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования." />');
    $arResult['h1SEO'] = "Впечатления";
}

$entity = \Bitrix\Iblock\Model\Section::compileEntityByIblock(IMPRESSIONS_IBLOCK_ID);
$rsSectionObjects = $entity::getList(
	[
		'order' => ['SORT' => 'ASC'],
		'filter' => ['IBLOCK_ID' => IMPRESSIONS_IBLOCK_ID, 'ACTIVE' => 'Y', 'META'],
		'select' => ['ID', 'NAME', 'PICTURE', 'CODE'],		
	]
)->fetchAll();

foreach ($rsSectionObjects as &$section) {
	$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(IMPRESSIONS_IBLOCK_ID, $section["ID"]);
	$section['META'] = $ipropValues->getValues();
	if ($arResult['VARIABLES']['SECTION_ID'] == $section['ID']) {
		$arResult['CUR_SECTION'] = $section;
	}
	$arResult['IMP_SECTIONS'][] = $section;
}

