<?

namespace Naturalist;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Bitrix\Sale\Basket;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Fuser;
use Bitrix\Main\Mail\Event;
use Bitrix\Sale\DiscountCouponsManager;

use CIBlockSection;
use CSaleOrder;
use CUser;
use Sberbank\Payments\Gateway;
use function NormalizePhone;
use function randString;

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
class Orders
{
    public $travelineSectionPropEnumId = '1';
    public $bnovoSectionPropEnumId = '2';

    public $arPropsIDs = array(
        'PHONE' => 1,
        'EMAIL' => 2,
        'GUEST_LIST' => 3,
        'DATE_FROM' => 4,
        'DATE_TO' => 5,
        'RESERVATION_ID' => 6,
        'TARIFF_ID' => 7,
        'CATEGORY_ID' => 17,
        'PRICES' => 18,
        'NAME' => 10,
        'LAST_NAME' => 11,
        'CHILDREN' => 12,
        'CHECKSUM' => 13,
        'OBJECT' => 14,
        'COMMISSION' => 15,
        'DATE_PAID' => 16,
    );
    public $statusNames = array(
        "N" => "Не оплачено",
        "P" => "Оплачен",
        "F" => "Завершён",
        "C" => "Отменён"
    );
    public $paymentTypeId = 4; // Сбербанк

    private $arErrors = [
        'The LastName field is required.' => 'Заполните поле Фамилия у гостя.',
        'The FirstName field is required.' => 'Заполните поле Имя у гостя.',
        'ConditionsChanged' => 'Информация о бронировании изменилась, оформите заказ заново.',
    ];

    public function __construct()
    {
        Loader::includeModule("sale");
    }

    /* Получение списка заказов текущего пользователя */

    /* авторегистрация пользователя */
    public static function autoRegisterUser($definedProps)
    {
        $userEmail = isset($definedProps['EMAIL']) ? trim((string)$definedProps['EMAIL']) : '';
        $newLogin = $userEmail;

        if (empty($userEmail)) {
            $newEmail = false;
            $normalizedPhone = NormalizePhone((string)$definedProps['PHONE'], 3);
            if (!empty($normalizedPhone))
                $newLogin = $normalizedPhone;
        } else
            $newEmail = $userEmail;

        if (empty($newLogin))
            $newLogin = randString(5) . mt_rand(0, 99999);

        $pos = strpos($newLogin, '@');
        if ($pos !== false)
            $newLogin = substr($newLogin, 0, $pos);

        if (strlen($newLogin) > 47)
            $newLogin = substr($newLogin, 0, 47);

        $newLogin = str_pad($newLogin, 3, '_');

        $dbUserLogin = CUser::GetByLogin($newLogin);
        if ($userLoginResult = $dbUserLogin->Fetch()) {
            do {
                $newLoginTmp = $newLogin . mt_rand(0, 99999);
                $dbUserLogin = CUser::GetByLogin($newLoginTmp);
            } while ($userLoginResult = $dbUserLogin->Fetch());

            $newLogin = $newLoginTmp;
        }

        $newName = $newLastName = '';
        $fio = isset($definedProps['FIO']) ? trim((string)$definedProps['FIO']) : '';
        if ($fio)
            list($newName, $newLastName) = explode(' ', $fio);

        $groupIds = [];
        $defaultGroups = Option::get('main', 'new_user_registration_def_group', '');

        if (!empty($defaultGroups))
            $groupIds = explode(',', $defaultGroups);
        $arPolicy = $GLOBALS['USER']->GetGroupPolicy($groupIds);

        $passwordMinLength = (int)$arPolicy['PASSWORD_LENGTH'] > 0 ? (int)$arPolicy['PASSWORD_LENGTH'] : 6;
        $passwordChars = array('abcdefghijklnmopqrstuvwxyz', 'ABCDEFGHIJKLNMOPQRSTUVWXYZ', '0123456789');
        if ($arPolicy['PASSWORD_PUNCTUATION'] === 'Y')
            $passwordChars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
        $newPassword = $newPasswordConfirm = randString($passwordMinLength + 2, $passwordChars);

        if (!$newEmail)
            $newEmail = $newLogin . '@' . $newLogin . '.autoreg';

        $fields = array(
            'LOGIN' => $newLogin,
            'NAME' => $newName,
            'LAST_NAME' => $newLastName,
            'PASSWORD' => $newPassword,
            'CONFIRM_PASSWORD' => $newPasswordConfirm,
            'EMAIL' => $newEmail,
            'GROUP_ID' => $groupIds,
            'ACTIVE' => 'Y',
            'LID' => Context::getCurrent()->getSite(),
            'PERSONAL_PHONE' => isset($definedProps['PHONE']) ? NormalizePhone($definedProps['PHONE']) : '',
            'PERSONAL_ZIP' => isset($definedProps['ZIP']) ? $definedProps['ZIP'] : '',
            'PERSONAL_STREET' => isset($definedProps['ADDRESS']) ? $definedProps['ADDRESS'] : '',
        );

        $user = new CUser;
        $addResult = $user->Add($fields);

        if (intval($addResult) <= 0)
            $GLOBALS['APPLICATION']->ThrowException('Ошибка регистрации пользователя: ' . ((strlen($user->LAST_ERROR) > 0) ? ': ' . $user->LAST_ERROR : ''), 'GENERATE_USER_ERROR');

        return intval($addResult);
    }

    /* Получение заказа по ID */

    public function beforeCancel($orderId)
    {
        $orderId = intval($orderId);
        $arOrder = $this->get($orderId);
        if ($arOrder["ERROR"]) {
            return json_encode($arOrder);
        }

        $service = $arOrder['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_SERVICE'];
        if ($service == $this->travelineSectionPropEnumId) {
            $penaltyAmount = Traveline::beforeCancelReservation($arOrder);
        } else {
            $penaltyAmount = false;
        }

        if ($penaltyAmount !== false) {
            return json_encode([
                "PENALTY" => $penaltyAmount
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при получении информации о штрафе."
            ]);
        }
    }

    /* Добавление нового заказа */

    public function get($orderId)
    {
        $orderId = intval($orderId);
        $order = Order::load($orderId);
        if (!$order) {
            return [
                "ERROR" => "Заказ не найден."
            ];
        }

        $arAvailibleFields = $order->getAvailableFields();
        foreach ($arAvailibleFields as $fieldName) {
            $arFields[$fieldName] = $order->getField($fieldName);
        }
        $arFields["IS_PAYED"] = $order->isPaid();

        // Список товаров
        $basket = $order->getBasket();
        $arFields["BASE_PRICE"] = $basket->getBasePrice();
        $arBasketItems = $basket->getBasketItems();

        $totalPrice = 0;
        $totalCount = 0;
        $arItems = array();
        $products = new Products();
        foreach ($arBasketItems as $item) {
            $productId = $item->getProductId();
            $arProduct = $products->get($productId);

            $price = $arProduct["CATALOG_PRICE_1"];
            $count = intval($item->getQuantity());
            $cost = floatval($price * $count);

            $arItems[] = array(
                "ID" => $productId,
                "QUANTITY" => $count,
                "PRODUCT_PRICE" => $price,
                "PRODUCT_COST" => $cost,
                "ITEM" => $arProduct,
            );

            $totalPrice += $cost;
            $totalCount += $count;
        }

        // Свойства заказа
        $propertyCollection = $order->getPropertyCollection();
        $arProps = array();
        foreach ($this->arPropsIDs as $key => $propId) {
            $arProps[$key] = $propertyCollection->getItemByOrderPropertyId($propId)->getValue();
        }
        if ($arProps["GUEST_LIST"]) {
            $arProps["GUESTS_COUNT"] = count($arProps["GUEST_LIST"]);
        }
        if ($arProps["CHILDREN"]) {
            $arProps["CHILDREN_AGE"] = explode(',', $arProps["CHILDREN"]);
        }

        $arData = array(
            "TOTAL_COUNT" => $totalCount,
            "TOTAL_PRICE" => $totalPrice,
            "STATUS" => $this->statusNames[$arFields["STATUS_ID"]]
        );

        $arOrder = array(
            "ID" => $orderId,
            "ITEMS" => $arItems,
            "FIELDS" => $arFields,
            "PROPS" => $arProps,
            "DATA" => $arData,
            "PAYMENT_URL" => $paymentUrl ?? ''
        );

        return $arOrder;
    }

    /* Изменение статуса заказа */

    public function cancel($orderId, $reason = "")
    {
        $orderId = intval($orderId);
        $arOrder = $this->get($orderId);
        if ($arOrder["ERROR"]) {
            return json_encode($arOrder);
        }

        $reservationId = $arOrder['PROPS']['RESERVATION_ID'];
        if (!empty($reservationId)) {
            $cancelReservation = $this->cancelReservation($arOrder);
            if ($cancelReservation["ERROR"]) {
                return json_encode($cancelReservation);
            }
        }

        $res = CSaleOrder::CancelOrder($orderId, "Y", $reason);
        if ($res) {
            // Выставляем заказу статус "С" (Отменен)
            $updStatusRes = $this->updateStatus($orderId, "C");

            if (!$updStatusRes["ERROR"]) {
                return json_encode([
                    "MESSAGE" => "Заказ отменен.",
                    "RELOAD" => true
                ]);

            } else {
                return json_encode($updStatusRes);
            }

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при отмене заказа."
            ]);
        }
    }

    /* Отменить заказ - до отмены (Traveline) */

    public function cancelReservation($arOrder)
    {
        global $arUser;

        $orderId = $arOrder['ID'];
        $service = $arOrder['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_SERVICE'];
        $reservationId = $arOrder['PROPS']['RESERVATION_ID'];


        if ($service == $this->bnovoSectionPropEnumId) {
            $bnovo = new Bnovo();
            $res = $bnovo->cancelReservation($arOrder);

        } elseif ($service == $this->travelineSectionPropEnumId) {
            $res = Traveline::cancelReservation($arOrder);
        }

        if ($res) {
            if (!isset($res["ERROR"])) {
                // Отсылка уведомлений на почту
                if ($arUser["UF_SUBSCRIBE_EMAIL_1"]) {
                    $penaltyAmount = Traveline::beforeCancelReservation($arOrder);

                    $sendRes = Users::sendEmail("USER_RESERVATION_CANCEL", "56", array(
                        "EMAIL" => $arOrder['PROPS']['EMAIL'],
                        "ORDER_ID" => $orderId,
                        "PENALTY_AMOUNT" => $penaltyAmount,
                        "RESERVATION_ID" => $reservationId,
                        "LINK" => 'https://' . $_SERVER['SERVER_NAME'] . '/personal/history/?status=C'
                    ));
                }
                // Отсылка уведомления на СМС
                if ($arUser["UF_SUBSCRIBE_SMS_1"]) {
                }
            }

            return $res;

        } else {
            return [
                "ERROR" => "Ошибка получения кода внешнего сервиса из заказа."
            ];
        }
    }

    /* Отменить заказ */

    public function updateStatus($orderId, $statusCode)
    {
        $orderId = intval($orderId);
        $order = Order::load($orderId);
        if (!$order) {
            return [
                "ERROR" => "Заказ не найден."
            ];
        }

        $order->setField("DATE_STATUS", time());
        $order->setField("STATUS_ID", $statusCode);
        // Сохранение изменений в заказе
        $res = $order->save();

        if ($res->isSuccess()) {
            return true;

        } else {
            return [
                "ERROR" => "Ошибка при смене статуса заказа."
            ];
        }
    }

    /* Выставление статуса F ("Завершен") всем заказам, у которых наступила дата завершения */

    public function checkDates()
    {
        $rsOrders = Order::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'DESC']
        ]);

        while ($order = $rsOrders->Fetch()) {
            $arOrder = $this->get($order["ID"]);

            if (time() >= strtotime($arOrder["PROPS"]["DATE_FROM"]) && $arOrder["FIELDS"]["STATUS_ID"] != "F" && $arOrder["FIELDS"]["STATUS_ID"] != "C") {
                $order = Order::load($arOrder["ID"]);
                if (!$order) {
                    return json_encode([
                        "ERROR" => "Заказ не найден."
                    ]);
                }

                $paymentCollection = $order->getPaymentCollection();
                $payment = $paymentCollection[0];

                if (!empty($payment) && !empty($arOrder["FIELDS"]["IS_PAYED"])) {
                    $this->updateStatus($arOrder["ID"], "F");

                    $this->updatePayment($order);
                } else {
                    $this->updateStatus($arOrder["ID"], "C");
                }
            } elseif (time() >= strtotime("+4 day", $arOrder["FIELDS"]["DATE_INSERT"]->getTimestamp()) && $arOrder["FIELDS"]["STATUS_ID"] != "F" && $arOrder["FIELDS"]["STATUS_ID"] != "C") {
                $order = Order::load($arOrder["ID"]);
                if (!$order) {
                    return json_encode([
                        "ERROR" => "Заказ не найден."
                    ]);
                }

                $paymentCollection = $order->getPaymentCollection();
                $payment = $paymentCollection[0];

                if (!empty($payment) && !empty($arOrder["FIELDS"]["IS_PAYED"])) {
                    $this->updatePayment($order);
                } else {
                    $this->updateStatus($arOrder["ID"], "C");
                }
            }
        }
    }

    public function getList($filter = array(), $sort = array('DATE_INSERT' => 'ASC'))
    {
        global $userId;

        $arFilter = array(
            "USER_ID" => $userId
        );
        if (is_array($filter) && $filter) {
            $arFilter = array_merge($arFilter, $filter);
        }

        $rsOrders = Order::getList([
            'select' => ['ID'],
            'filter' => $arFilter,
            'order' => $sort
        ]);
        $arOrdersList = array();
        while ($arOrder = $rsOrders->Fetch()) {
            $arOrdersList[$arOrder["ID"]] = $this->get($arOrder["ID"]);
        }

        return $arOrdersList;
    }

    /* Бронирование объекта из заказа во внешнем сервисе */

    public function updatePayment($order)
    {
        Loader::includeModule('sberbank.ecom2');

        $arPaymentsCollection = $order->loadPaymentCollection();
        $currentPaymentOrder = $arPaymentsCollection->current();
        do {
            $currentPaymentId = ($currentPaymentOrder->getField("PS_INVOICE_ID"));
        } while ($currentPaymentOrder = $arPaymentsCollection->next());

        // Второй этап оплаты
        $RBS_Gateway = new Gateway;
        $RBS_Gateway->setOptions(array(
            'module_id' => 'sberbank.ecom2',
            'gate_url_prod' => 'https://securepayments.sberbank.ru/payment/rest/',
            //'gate_url_prod' => 'https://3dsec.sberbank.ru/payment/rest/',
            'gate_url_test' => 'https://3dsec.sberbank.ru/payment/rest/',
            'cms_version' => 'Bitrix ' . SM_VERSION,
            'language' => 'ru',
        ));

        $RBS_Gateway->buildData(array(
            'amount' => 0,
            'userName' => SBR_LOGIN,
            'password' => SBR_PASSWORD,
            'orderId' => $currentPaymentId,
        ));
        $gateResponse = $RBS_Gateway->deposit();

        // Закрывающий чек
        $RBS_Gateway = new Gateway;

        $RBS_Gateway->setOptions(array(
            'module_id' => 'sberbank.ecom2',
            'gate_url_prod' => 'https://securepayments.sberbank.ru/payment/rest/',
            //'gate_url_prod' => 'https://3dsec.sberbank.ru/payment/rest/',
            'gate_url_test' => 'https://3dsec.sberbank.ru/payment/rest/',
            'cms_version' => 'Bitrix ' . SM_VERSION,
            'language' => 'ru',
        ));

        $RBS_Gateway->buildData(array(
            'mdOrder' => $currentPaymentId,
            'amount' => $order->GetPrice() * 100,
            'userName' => SBR_LOGIN,
            'password' => SBR_PASSWORD,
        ));

        $gateResponse = $RBS_Gateway->closeOfdReceipt();
    }

    /* Отмена бронирования объекта из заказа во внешнем сервисе */

    public function makeReservation($orderId)
    {
        $orderId = intval($orderId);
        $arOrder = $this->get($orderId);
        if ($arOrder["ERROR"]) {
            return $arOrder;
        }

        global $arUser;
        if (!$arUser) {
            $userId = $arOrder["FIELDS"]["USER_ID"];
            $arUser = CUser::GetByID($userId)->GetNext();
        }
        $arUser["EMAIL"] = !empty($arUser["EMAIL"]) ? $arUser["EMAIL"] : $arOrder['PROPS']["EMAIL"];
        $arUser["PERSONAL_PHONE"] = !empty($arUser["PERSONAL_PHONE"]) ? $arUser["PERSONAL_PHONE"] : $arOrder['PROPS']["PHONE"];

        $service = $arOrder['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_SERVICE'];
        $reservationPropId = $this->arPropsIDs['RESERVATION_ID'];

        if ($service == $this->bnovoSectionPropEnumId) {
            $bnovo = new Bnovo();
            $reservationRes = $bnovo->makeReservation($orderId, $arOrder, $arUser, $reservationPropId);

        } elseif ($service == $this->travelineSectionPropEnumId) {
            $reservationRes = Traveline::makeReservation($orderId, $arOrder, $arUser, $reservationPropId);
        }

        if ($reservationRes) {
            /* Устанавливаем свойства заказа (пользовательские) */
            $order = Order::load($orderId);
            $propertyCollection = $order->getPropertyCollection();
            // АК (агентская комиссия)
            $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['DATE_PAID']);
            $propertyValue->setValue(date('d.m.Y H:i:s'));

            // Сохранение изменений в заказе
            $orderRes = $order->save();

            if (!isset($reservationRes["ERROR"])) {
                // Отсылка уведомлений на почту
                if ($arUser["UF_SUBSCRIBE_EMAIL_1"]) {
                    $sendRes = Users::sendEmail("USER_RESERVATION", "55", array(
                        "EMAIL" => $arUser["EMAIL"],
                        "ORDER_ID" => $orderId,
                        "RESERVATION_ID" => $reservationRes,
                        "LINK" => 'https://' . $_SERVER['SERVER_NAME'] . '/personal/active/'
                    ));
                }
                // Отсылка уведомления на СМС
                if ($arUser["UF_SUBSCRIBE_SMS_1"]) {
                }
            }

            return $reservationRes;

        } else {
            return [
                "ERROR" => "Ошибка получения кода внешнего сервиса из заказа."
            ];
        }
    }

    /* Получение ссылки на оплату */

    public function getCancellationAmount($params)
    {
        $service = $params['service'];
        $arChildrenAge = [];
        if ($params['childrenAge']) {
            $arChildrenAge = explode(',', $params['childrenAge']);
        }

        if ($service == $this->travelineSectionPropEnumId) {
            $arData = Traveline::getCancellationAmount($params['externalId'], $params['guests'], $arChildrenAge, $params['dateFrom'], $params['dateTo'], $params['checksum'], $params['externalElementId'], $params['travelineCategoryId']);
        } else {
            $arData = false;
        }

        if ($arData !== false) {
            return json_encode([
                "FREE" => $arData['possible'],
                "DATE" => $arData['date'],
                "PENALTY" => $arData['cancelAmount']
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при получении информации о штрафе."
            ]);
        }
    }

    /* Получение информации о штрафе */

    public function enterCoupon($coupon)
    {
        $coupon = htmlspecialchars_decode(trim($coupon));
        if (!empty($coupon)) {
            $getCoupon = DiscountCouponsManager::getData($coupon);
            if ($getCoupon['ACTIVE'] === 'Y') {
                DiscountCouponsManager::add($coupon);
                return json_encode([
                    "MESSAGE" => "Купон применён",
                    "STATUS" => "SUCCESS"
                ]);
            } else {
                return json_encode([
                    "MESSAGE" => "Такого промокода не существует или его срок действия истёк. Пожалуйста воспользуйтесь другим промокодом.",
                    "STATUS" => "ERROR"
                ]);
            }
        }
    }

    /* Добавляет купон к заказу */

    public function add($params)
    {
        global $arUser, $userId;
        if (intval($userId) < 1) {
            $userId = self::autoRegisterUser([
                'FIO' => trim($params["last_name"]) . " " . trim($params["name"]),
                'EMAIL' => $params["email"],
                'PHONE' => $params["phone"]
            ]);


//            return json_encode([
//                "ERROR" => "Необходимо авторизоваться."
//            ]);
        }

        // Список гостей
        $arSaveGuests = [];
        $arGuestList = [];
        $arGuestList[] = trim($params["last_name"]) . " " . trim($params["name"]);
        foreach ($params["guests"] as $key => $arItem) {
            $arGuestList[] = trim($arItem["surname"]) . " " . trim($arItem["name"]) . " " . trim($arItem["lastname"]);

            if ($arItem["save"]) {
                unset($arItem["save"]);
                $arSaveGuests[$key] = $arItem;
            }
        }

        // Получение товаров из корзины
        $baskets = new Baskets();
        $arBasketItems = $baskets->get();

        // Получение кода внешнего сервиса (1 - Traveline, 2 - Bnovo)
        $externalService = $arBasketItems["ITEMS"][0]["ITEM"]["SECTION"]["UF_EXTERNAL_SERVICE"];

        // Проверка возможности бронирования перед созданием заказа и отмена создания заказа в случае невозможности бронирования (только для Traveline)
        // if($externalService == $this->travelineSectionPropEnumId) {
        //     $externalSectionId = $arBasketItems['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_ID'];
        //     $sectionName = $arBasketItems['ITEMS'][0]['ITEM']['SECTION']['NAME'];
        //     $sectionCommission = $arBasketItems['ITEMS'][0]['ITEM']['SECTION']['UF_AGENT'];
        //     $externalElementId = $arBasketItems['ITEMS'][0]['ITEM']['PROPERTIES']['EXTERNAL_ID']['VALUE'];
        //     $externalCategoryId = $arBasketItems['ITEMS'][0]['ITEM']['PROPERTIES']['EXTERNAL_CATEGORY_ID']['VALUE'];
        //     $dateFrom = $params['dateFrom'];
        //     $dateTo = $params['dateTo'];
        //     $guests = count($arGuestList);
        //     $price = $arBasketItems['ITEMS'][0]["PRICE"];
        //     $checksum = $params['checksum'];
        //     $arChildrenAge = ($params['childrenAge']) ? explode(',', $params['childrenAge']) : [];

        //     if(empty($arUser["EMAIL"])) {
        //         $arUser["EMAIL"] = $params["email"];
        //     }
        //     if(empty($arUser["PERSONAL_PHONE"])) {
        //         $arUser["PERSONAL_PHONE"] = $params["phone"];
        //     }

        //     $arVerifyResponse = Traveline::verifyReservation($externalSectionId, $externalElementId, $externalCategoryId, $guests, $arChildrenAge, $dateFrom, $dateTo, $price, $checksum, $arGuestList, $arUser);

        //     if (!empty($arVerifyResponse['warnings'][0]['code']) || empty($arVerifyResponse["booking"])) {
        //         $errorText = $arVerifyResponse['warnings'][0]['code'] ?? $arVerifyResponse['errors'][0]['message'];
        //         return json_encode([
        //             "ERROR" => "Невозможно бронирование на выбранные даты. " . $this->arErrors[$errorText]
        //         ]);
        //     }
        // }

        // Создание корзины
        $siteId = Context::getCurrent()->getSite();
        $basket = Basket::loadItemsForFUser(Fuser::getId(), $siteId);

        // Создание нового заказа
        $order = Order::create($siteId, $userId);
        // Выбор типа плательщика
        $order->setPersonTypeId(1);
        // Прикрепление корзины к заказу
        $order->setBasket($basket);
        // Свойство заказа USER_DESCRIPTION (дефолтное)
        if ($params["comment"]) {
            $order->setField('USER_DESCRIPTION', $params["comment"]);
        }

        /* Оплата */
        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem(
            PaySystem\Manager::getObjectById($this->paymentTypeId)
        );
        $payment->setField("SUM", $order->getPrice());
        $payment->setField("CURRENCY", $order->getCurrency());

        /* Устанавливаем свойства заказа (пользовательские) */
        $propertyCollection = $order->getPropertyCollection();
        // Телефон
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['PHONE']);
        $propertyValue->setValue($params["phone"]);
        // E-mail
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['EMAIL']);
        $propertyValue->setValue($params["email"]);
        // Имя
        $arUser["NAME"] = !empty($arUser["NAME"]) ? $arUser["NAME"] : $params["name"];
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['NAME']);
        $propertyValue->setValue($arUser["NAME"]);
        // Фамилия
        $arUser["LAST_NAME"] = !empty($arUser["LAST_NAME"]) ? $arUser["LAST_NAME"] : $params["last_name"];
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['LAST_NAME']);
        $propertyValue->setValue($arUser["LAST_NAME"]);
        // Дата заезда
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['DATE_FROM']);
        $propertyValue->setValue($params["dateFrom"]);
        // Дата выезда
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['DATE_TO']);
        $propertyValue->setValue($params["dateTo"]);
        // Список гостей
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['GUEST_LIST']);
        $propertyValue->setValue($arGuestList);
        // Кол-во детей
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['CHILDREN']);
        $propertyValue->setValue($params["childrenAge"]);
        // Checksum
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['CHECKSUM']);
        $propertyValue->setValue($params["checksum"]);
        // Объект
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['OBJECT']);
        $propertyValue->setValue($sectionName);
        // АК (агентская комиссия)
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['COMMISSION']);
        $propertyValue->setValue($sectionCommission);

        if ($externalService == $this->bnovoSectionPropEnumId) {
            // ID тарифа Bnovo
            $tariffId = $arBasketItems['ITEMS'][0]['PROPS']['TARIFF_ID'];
            $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['TARIFF_ID']);
            $propertyValue->setValue($tariffId);

            // ID категории Bnovo
            $categoryId = $arBasketItems['ITEMS'][0]['PROPS']['CATEGORY_ID'];
            $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['CATEGORY_ID']);
            $propertyValue->setValue($categoryId);

            // Список цен Bnovo
            $prices = $arBasketItems['ITEMS'][0]['PROPS']['PRICES'];
            $propertyValue = $propertyCollection->getItemByOrderPropertyId($this->arPropsIDs['PRICES']);
            $propertyValue->setValue(serialize($prices));
        }

        // Пересчёт заказа
        $order->doFinalAction(true);
        // Сохранение изменений в заказе
        $orderRes = $order->save();
        if ($orderRes) {
            // Получение ID заказа
            $orderId = $order->getId();

            if ($orderId) {
                // Очистка корзины
                $baskets->deleteAll();

                // Ссылка на оплату
                $paymentUrl = $this->getPaymentUrl($orderId, false);

                // Сохранение гостей в пользовательском поле UF_GUESTS_DATA для текущего пользователя
                if ($arSaveGuests) {
                    $user = new CUser();
                    $user->Update($userId, array(
                        "UF_GUESTS_DATA" => json_encode($arSaveGuests)
                    ));
                }

                // Обновление Имени и Фамилии пользователя
                if ($arUser["NAME"] == '' && $params["name"] != '') {
                    $user = new CUser();
                    $user->Update($userId, array(
                        "NAME" => json_encode($arSaveGuests)
                    ));
                }

                if ($arUser["LAST_NAME"] == '' && $params["last_name"] != '') {
                    $user = new CUser();
                    $user->Update($userId, array(
                        "LAST_NAME" => json_encode($arSaveGuests)
                    ));
                }

                // Изменение свойства UF_RESERVE_COUNT у раздела товара из заказа
                $iS = new CIBlockSection();
                foreach ($arBasketItems["ITEMS"] as $arItem) {
                    $arSection = $arItem["ITEM"]["SECTION"];

                    $newOrdersCount = (int)$arSection["UF_RESERVE_COUNT"] + 1;
                    $iS->Update($arSection["ID"], array(
                        "UF_RESERVE_COUNT" => $newOrdersCount
                    ));
                }

                return json_encode([
                    "ID" => $orderId,
                    "MESSAGE" => "Заказ успешно добавлен.",
                    "REDIRECT_URL" => $paymentUrl
                ]);

            } else {
                return json_encode([
                    "ERROR" => "Произошла ошибка при создании заказа."
                ]);
            }

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при создании заказа: " . implode(", ", $orderRes->getErrorMessages())
            ]);
        }
    }

    /* Удаляет купон из заказа */

    public function getPaymentUrl($orderId, $isJSON = true)
    {
        $orderId = intval($orderId);
        $order = Order::load($orderId);
        if (!$order) {
            return json_encode([
                "ERROR" => "Заказ не найден."
            ]);
        }

        $request = Application::getInstance()->getContext()->getRequest();
        $_REQUEST["ORDER_ID"] = $orderId;

        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection[0];

        if (!empty($payment)) {
            $paymentId = $payment->getPaymentSystemId();
            $service = PaySystem\Manager::getObjectById($paymentId);
            $initResult = $service->initiatePay($payment, $request, PaySystem\BaseServiceHandler::STRING);

            if ($initResult->isSuccess()) {
                $link = $initResult->getTemplate();
                return ($isJSON) ? json_encode([
                    "LINK" => $link
                ]) : $link;

            } else {
                return ($isJSON) ? json_encode($initResult->getErrorMessages()) : $initResult->getErrorMessages();
            }
        }
    }

    /* Получает информацию по всем применённым в заказе купонам */

    public function removeCoupon($coupon)
    {
        $coupon = htmlspecialchars_decode(trim($coupon));
        if (!empty($coupon)) {
            return DiscountCouponsManager::delete($coupon);
        }
    }

    public function getActivatedCoupons()
    {
        $result = [];
        $arCoupons = DiscountCouponsManager::get(true, [], true, true);
        if (!empty($arCoupons)) {
            foreach ($arCoupons as &$oneCoupon) {
                if ($oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_NOT_FOUND || $oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_FREEZE) {
                    $oneCoupon['JS_STATUS'] = 'BAD';
                } elseif ($oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_NOT_APPLYED || $oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_ENTERED) {
                    $oneCoupon['JS_STATUS'] = 'ENTERED';
                } else {
                    $oneCoupon['JS_STATUS'] = 'APPLIED';
                }

                $oneCoupon['JS_CHECK_CODE'] = '';
                if (isset($oneCoupon['CHECK_CODE_TEXT'])) {
                    $oneCoupon['JS_CHECK_CODE'] = is_array($oneCoupon['CHECK_CODE_TEXT'])
                        ? implode(', ', $oneCoupon['CHECK_CODE_TEXT'])
                        : $oneCoupon['CHECK_CODE_TEXT'];
                }

                $result[] = $oneCoupon;
            }

            unset($oneCoupon);
            $result = array_values($arCoupons);
        }
        unset($arCoupons);
        return $result;
    }
}
