<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Naturalist\Filters\Components;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

// Экземпляр класса ЧПУ хайлоад блока
$chpyDataClass = HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

// Регионы
$regionsDataClass = HighloadBlockTable::compileEntity(REGIONS_HL_ENTITY)->getDataClass();
$regionsData = $regionsDataClass::query()
    ->addSelect('ID')
    ->addSelect('UF_NAME')
    ->addSelect('UF_ICON')
    ->addSelect('UF_SORT')
    ->where('UF_SHOW_ON_MAIN', 1)
    ?->fetchAll();

if (!empty($regionsData)) {
    foreach ($regionsData as $key => &$regions) {
        $regions['URL'] = Components::getChpyLink(REGIONS_HL_ENTITY . '_' . $regions['ID'])['UF_NEW_URL'];
    }
    usort($regionsData, function ($a, $b) {
        return ($a['UF_SORT'] - $b['UF_SORT']);
    });
}

// Водоёмы
$waterDataClass = HighloadBlockTable::compileEntity(WATER_HL_ENTITY)->getDataClass();
$waterData = $waterDataClass::query()
    ->addSelect('ID')
    ->addSelect('UF_NAME')
    ->addSelect('UF_IMG')
    ->addSelect('UF_SORT')
    ->where('UF_SHOW_ON_MAIN', 1)
    ?->fetchAll();

if (!empty($waterData)) {
    foreach ($waterData as $key => &$water) {
        $water['URL'] = Components::getChpyLink(WATER_HL_ENTITY . '_' . $water['ID'])['UF_NEW_URL'];
    }
    usort($waterData, function ($a, $b) {
        return ($a['UF_SORT'] - $b['UF_SORT']);
    });
}

$arResult['REGIONS'] = $regionsData;
$arResult['WATER'] = $waterData;
