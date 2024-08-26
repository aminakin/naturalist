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

//Все элементы водоёмы
$waterFullDataClass = HighloadBlockTable::compileEntity(WATER_HL_ENTITY)->getDataClass();
$waterFullData = $waterFullDataClass::query()
    ->addSelect('ID')
    ->addSelect('UF_NAME')
    ->addSelect('UF_SORT')
    ?->fetchAll();

if (!empty($waterFullData)) {
    foreach ($waterFullData as $key => &$water) {
        $water['URL'] = Components::getChpyLink(WATER_HL_ENTITY . '_' . $water['ID'])['UF_NEW_URL'];
    }
    usort($waterFullData, function ($a, $b) {
        return ($a['UF_SORT'] - $b['UF_SORT']);
    });
}

//Все элементы регионы
$regionsFullDataClass = HighloadBlockTable::compileEntity(REGIONS_HL_ENTITY)->getDataClass();
$regionsFullData = $regionsFullDataClass::query()
    ->addSelect('ID')
    ->addSelect('UF_NAME')
    ->addSelect('UF_SORT')
    ?->fetchAll();

if (!empty($regionsFullData)) {
    foreach ($regionsFullData as $key => &$regions) {
        $regions['URL'] = Components::getChpyLink(REGIONS_HL_ENTITY . '_' . $regions['ID'])['UF_NEW_URL'];
    }
    usort($regionsFullData, function ($a, $b) {
        return ($a['UF_SORT'] - $b['UF_SORT']);
    });
}

$arResult['HTML'] = function (array $arResult, string $locationsKey)
{
    $alphabit = ['А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Э','Ю','Я',];
    foreach($alphabit as $letter) { ?>
        <div 
        class="location-alphabet__letter<?= checkLetterHasLocations($letter, $arResult, $locationsKey)? '': ' disabled'?>" 
        data-letter='<?=$letter?>'>
            <?=$letter?>
            <?= checkLetterHasLocations($letter, $arResult, $locationsKey)? '': '<div class="no-location">На эту букву пока ничего нет, но это временно. Ожидайте обновлений!</div>'?>
        </div>
    <?php }
    
};

function checkLetterHasLocations(string $letter, array $locations, string $locationsKey){
    $flag = false;
    foreach($locations as $location) {
        $locationName = mb_strtolower($location[$locationsKey]);
        $locationName= str_replace('республика ', "", $locationName);
        $locationName= str_replace('озеро ', "", $locationName);
        $locationName= str_replace('река ', "", $locationName);
        $locationFirstChar = mb_substr($locationName, 0, 1);
        if($locationFirstChar === mb_strtolower($letter)) {
            $flag = true;
            break;
        }
    }
    return $flag;
}

$arResult['REGIONS'] = $regionsData;
$arResult['WATER'] = $waterData;

$arResult['REGIONS_FULL'] = $regionsFullData;
$arResult['WATER_FULL'] = $waterFullData;