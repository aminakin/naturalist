<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

CModule::IncludeModule("highloadblock");

$HL_BLOCK_ID = 26;
$STEP_LIMIT = 3000;
$lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

$start = microtime(true);

$hlblock = HL\HighloadBlockTable::getById($HL_BLOCK_ID)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();

$rsData = $entityClass::getList([
    "select" => ["ID", "UF_DESCRIPTION"],
    "filter" => [
        ">ID" => $lastId,
        "%UF_DESCRIPTION" => "2024"
    ],
    "order" => ["ID" => "ASC"],
    "limit" => $STEP_LIMIT,
]);

$count = 0;
$maxId = $lastId;

while ($item = $rsData->fetch()) {
    $original = $item["UF_DESCRIPTION"];
    $updated = str_replace("2024", "2025", $original);

    if ($original !== $updated) {
        $entityClass::update($item["ID"], [
            "UF_DESCRIPTION" => $updated
        ]);
        $count++;
    }

    if ($item["ID"] > $maxId) {
        $maxId = $item["ID"];
    }
}

$time = microtime(true) - $start;

echo "<p>Обновлено записей: <strong>$count</strong></p>";
echo "<p>Время выполнения: <strong>" . round($time, 4) . " сек</strong></p>";

if ($count === $STEP_LIMIT) {
    echo "<a href='?last_id={$maxId}'>Продолжить с ID = {$maxId}</a>";
} else {
    echo "<p>Все подходящие записи обработаны.</p>";
}
