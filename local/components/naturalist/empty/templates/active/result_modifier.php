<?

use Bitrix\Main\Application;
use Bitrix\Main\Grid\Declension;
use Bitrix\Highloadblock\HighloadBlockTable;
use Naturalist\Orders;
use Naturalist\SearchServiceFactory;
use Naturalist\Products;

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

$factory = new SearchServiceFactory();
$products = new Products($factory);

foreach ($arOrders as $id => $order) {
    $item = $order['ITEMS'][0];

    $service = CUserFieldEnum::GetList(
        [], 
        ['ID' => $item['ITEM']['SECTION']['UF_EXTERNAL_SERVICE']]
    )->Fetch();

    $arExternalResult = $products->searchRooms(
        $item['ITEM']['SECTION']['ID'],
        $item['ITEM']['SECTION']['UF_EXTERNAL_ID'],
        $service['XML_ID'],
        0,
        [],
        $item['ITEM_BAKET_PROPS']['DATE_FROM']['VALUE'],
        $item['ITEM_BAKET_PROPS']['DATE_TO']['VALUE'],
        0
    )['arRooms'] ?: [];

    if (is_array($arExternalResult) && !empty($arExternalResult)) {
        foreach ($arExternalResult as $idNumber => $arTariffs) {
          
            foreach ($arTariffs as $keyTariff => $arTariff) {
                $cancelation = [];

                if (!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '2') {
                    if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                      array_push($cancelation, $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                    } else {
                      array_push($cancelation, 'Штраф за отмену бронирования — ' . $arTariff['price'] * ($arTariff['value']['PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE'] / 100) . ' ₽');
                    }
                } elseif (!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '5') {
                    if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                      array_push($cancelation, $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                    }

                    array_push($cancelation, 'Штраф за отмену бронирования — ' . $arTariff['price'] . ' ₽');
                } elseif (!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '4') {
                    if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                      array_push($cancelation, $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                    }

                    array_push($cancelation, 'Штраф за отмену бронирования — ' . array_shift($arTariff['prices']) . ' ₽');
                } elseif (!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'])) {
                    if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                      array_push($cancelation,  $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                    }

                    array_push($cancelation,  'Штраф за отмену бронирования — ' . $arTariff['value']['PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE'] . ' ₽');
                } else {
                    if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                      array_push($cancelation, $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                    }

                    array_push($cancelation, 'Бесплатная отмена бронирования');
                }
            }
        }
    }

    $arOrders[$id]['CANCEL_INFO'] = $cancelation;
}

$propReasonEnum = CIBlockPropertyEnum::GetList(
    ['ID' => 'ASC'],
    [
        'IBLOCK_ID' => REASONS_CANCEL_ORDER,
        'CODE' => 'REASONS'
    ]
);

$propReasonEnumValues = [];

while ($field = $propReasonEnum->GetNext()) {
    $propReasonEnumValues[] = [
        'ID' => $field['ID'],
        'VALUE' => $field['VALUE'],
        'XML_ID' => $field['XML_ID']
    ];
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
    "reasonCancel" => $propReasonEnumValues
);
