<?php

set_time_limit(600);
if (!$_SERVER['DOCUMENT_ROOT']) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../../");
}
include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

\Bitrix\Main\Loader::includeModule('iblock');

// Поиск всех внешних ID тарифов
$tariffData = \Bitrix\Iblock\Iblock::wakeUp(TARIFFS_IBLOCK_ID)->getEntityDataClass()::query()
    ->setSelect(['ID', "EXTERNALID" => 'EXTERNAL_ID.VALUE'])
    ->fetchAll();

$hlEntity = new \Naturalist\HighLoadBlockHelper('RoomOffers');

// Поиск всех записей с несуществующими тарифами
$hlEntity->prepareParamsQuery(
    ["ID", "UF_TARIFF_ID"],
    ["ID" => "ASC"],
    ["!UF_TARIFF_ID" => array_column($tariffData, 'EXTERNALID')],
);

$rows = $hlEntity->getDataAll();

if (!empty($rows)) {
    // Удалить из таблицы выбранные записи
    foreach ($rows as $row) {
        $hlEntity->delete($row['ID']);
    }
}

