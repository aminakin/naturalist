<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$iMainKey = false;
$iSelectedCount = 0;
foreach ($arResult as $key => $arItem) {
    $iSelectedCount += ($arItem["SELECTED"]) ? 1 : 0;
    if ($arItem["LINK"] === SITE_DIR) $iMainKey = $key;
}
if (is_numeric($iMainKey) && $iSelectedCount > 1) {
    $arResult[$iMainKey]["SELECTED"] = false;
}