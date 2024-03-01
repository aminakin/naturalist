<?php 

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\Elements\ElementCertificatesStepsTable;
use Naturalist\Orders;
use Naturalist\HighLoadBlockHelper;

global $arUser, $isAuthorized;
if (!$isAuthorized) {
    LocalRedirect('/');
}

$arOrders = [];

// Шаги для сертификата
$steps = ElementCertificatesStepsTable::getList([
    'select' => [
        'ID',
        'NAME',
        'SORT',
        'PREVIEW_TEXT'
    ],
    'order' => [
        'SORT' => 'ASC'
    ],
    'filter' => [
        '=ACTIVE' => 'Y'
    ]
])->fetchAll();

// Заказы
$order = new Orders();
$arTmpOrders = $order->getList(["STATUS_ID" => ["N", "P", "F"]], ['DATE_INSERT' => 'DESC']);
foreach ($arTmpOrders as $arOrder) {    
    if (array_search(CERT_CASH_PAYSYSTEM_ID, $arOrder['PAYMENTS']) === false) {
        continue;
    }
    if ($arOrder['PROPS']['IS_CERT'] == 'Y') {
        $arOrders[] = $arOrder;
    }
}

// Сертификаты
$hlEntity = new HighLoadBlockHelper('Certificates');
$hlEntity->prepareParamsQuery(['*'], [], ['UF_USER_ID' => $arUser['ID']]);
$certs = $hlEntity->getDataAll();

foreach ($certs as &$cert) {    
    $cert['ID'] = $cert['UF_ORDER_ID'];
    $cert['ORDER'] = $order->getList(['=ID' => $cert['UF_ORDER_ID']]);    
    $arOrders[] = $cert;
}

// Для единообразия отображения, добавляем сертифика в общий массив
// заказов с присвоенным ему ID заказа и сортируем по ID заказ.
// Т.е. сертификат становится заказом с ID.
function cmp($a, $b) {
    return strcmp($b["ID"], $a["ID"]);
}

usort($arOrders, "cmp");

$arResult = array(    
    "arUser" => $arUser,
    "isAuthorized" => $isAuthorized,    
    "steps" => $steps,
    "arOrders" => $arOrders,
);