<?

use Naturalist\Products;
use Naturalist\Users;
use Naturalist\Settings;
use Naturalist\Filters\Components;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

global $arUser, $isAuthorized;
global $arSettings, $srcMainBg;

// Экземпляр класса ЧПУ хайлоад блока
$chpyDataClass = HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

// Генерация массива месяцев для фильтра
$currMonthName = FormatDate("f");
$currYear = date('Y');
$nextYear = $currYear + 1;
$arDates = Products::getDates();

// Метеоданные
$coords = (!$isAuthorized) ? $arSettings['main_meteo_coords'] : null;
$arMeteo = Users::getMeteo($coords);

//Табы на главной
$arTabs = Settings::getTabsMain();

// Типы домов
$houseTypesDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();
$houseTypeData = $houseTypesDataClass::query()
    ->addSelect('*')
    ?->fetchAll();

if (!empty($houseTypeData)) {
    foreach ($houseTypeData as $key => &$houseType) {
        $houseType['URL'] = Components::getChpyLink(SUIT_TYPES_HL_ENTITY . '_' . $houseType['ID'])['UF_NEW_URL'];
    }
    usort($houseTypeData, function ($a, $b) {
        return ($a['UF_SORT'] - $b['UF_SORT']);
    });
}

$arResult = array(
    "arSettings" => $arSettings,
    "arMeteo" => $arMeteo,
    "arUser" => $arUser,
    "isAuthorized" => $isAuthorized,
    "currMonthName" => $currMonthName,
    "currYear" => $currYear,
    "nextYear" => $nextYear,
    "arDates" => $arDates,
    "arTabs" => $arTabs,
    "srcMainBg" => $srcMainBg,
    "arHouseTypes" => $houseTypeData,
);
