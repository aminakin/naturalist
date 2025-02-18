<?

use Bitrix\Main\Application;
use Bitrix\Main\Grid\Declension;
use Bitrix\Highloadblock\HighloadBlockTable;
use Naturalist\Orders;

global $arUser, $isAuthorized;
if (!$isAuthorized) {
    LocalRedirect('/');
}
CModule::IncludeModule('highloadblock');

/* Склонения */
$guestsDeclension = new Declension('гость', 'гостя', 'гостей');
$reviewsDeclension = new Declension('отзыв', 'отзыва', 'отзывов');
$daysDeclension = new Declension('ночь', 'ночи', 'ночей');

/* Фильтрация */
$arFilter = array(
    "STATUS_ID" => ["P"]
);
$orderNum = $_REQUEST['orderNum'] ?? '';
if (isset($orderNum) && !empty($orderNum)) {
    $arFilter["ACCOUNT_NUMBER"] = $orderNum;
}

/* Сортировка */
$sort = $_REQUEST['sort'] ?? 'date_create';

/* Список заказов */
$order = new Orders();
$arOrders = $order->getList($arFilter, ['ID' => 'DESC']);

/* Кастомная сортировка по свойству DATE_FROM (старт заезда) по возрастанию */
if ($sort == 'date_from') {
    uasort($arOrders, function ($a, $b) {
        if ($a['PROPS']['DATE_FROM'] == $b['PROPS']['DATE_FROM'])
            return false;

        return ($a['PROPS']['DATE_FROM'] < $b['PROPS']['DATE_FROM']) ? 1 : -1;
    });
}

/* HL Blocks */
// Types
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

$arResult = array(
    "orderNum" => $orderNum,
    "sort" => $sort,
    "arOrders" => $arOrders,
    "arUser" => $arUser,
    "arHLTypes" => $arHLTypes,
    "guestsDeclension" => $guestsDeclension,
    "reviewsDeclension" => $reviewsDeclension,
    "daysDeclension" => $daysDeclension,
);
