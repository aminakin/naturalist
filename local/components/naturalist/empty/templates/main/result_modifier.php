<?
use Naturalist\Products;
use Naturalist\Users;
use Naturalist\Settings;

global $arUser, $isAuthorized;
global $arSettings, $srcMainBg;

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
);