<?
use Bitrix\Highloadblock\HighloadBlockTable;

// Избранное
global $arFavourites;
$arResult["FAVOURITES"] = $arFavourites;

// Тип объекта
$hlId = 2;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
]);
$arHLTypes = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLTypes[$arEntity["ID"]] = $arEntity;
}

$arResult["HL_TYPES"] = $arHLTypes;