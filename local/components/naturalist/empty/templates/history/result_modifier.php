<?
use Bitrix\Main\Application;
use Bitrix\Main\Grid\Declension;
use Bitrix\Highloadblock\HighloadBlockTable;
use Naturalist\Orders;

global $arUser, $isAuthorized;
if (!$isAuthorized) {
    LocalRedirect('/');
}

/* Склонения */
$guestsDeclension = new Declension('гость', 'гостя', 'гостей');
$reviewsDeclension = new Declension('отзыв', 'отзыва', 'отзывов');
$daysDeclension = new Declension('ночь', 'ночи', 'ночей');

/* Фильтрация */
$status = $_REQUEST['status'] ?? "F";
$arFilter = array(
    "STATUS_ID" => $status
);

$orderNum = $_REQUEST['orderNum'] ?? '';
if(isset($orderNum) && !empty($orderNum)) {
    $arFilter["ACCOUNT_NUMBER"] = $orderNum;
}

/* Список заказов */
$order = new Orders();
$arOrders = $order->getList($arFilter, ['ID' => 'DESC']);

/* Список свойств ИБ Отзывы */
$rsProps = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => REVIEWS_IBLOCK_ID));
$arProps = array();
while($arProp = $rsProps->GetNext()) {
    $arProps[$arProp["CODE"]] = $arProp["NAME"];
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
    "status" => $status,
    "arOrders" => $arOrders,
    "arUser" => $arUser,
    "isAuthorized" => $isAuthorized,
    "arProps" => $arProps,
    "arHLTypes" => $arHLTypes,
    "guestsDeclension" => $guestsDeclension,
    "reviewsDeclension" => $reviewsDeclension,
    "daysDeclension" => $daysDeclension,
);