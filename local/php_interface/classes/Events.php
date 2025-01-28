<?

namespace Naturalist;

use Bitrix\Main\EventManager;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Bitrix\Main\Engine\CurrentUser;
use CIBlockSection;
use CIBlockElement;
use CFile;
use Naturalist\Users;
use Naturalist\Orders;
use Naturalist\Settings;
use Naturalist\Certificates;
use Naturalist\CreateCertPdf;
use Naturalist\Crest;
use Naturalist\CatalogCustomProp;
use Bitrix\Main\Diag\Debug;
use Naturalist\HighLoadBlockHelper;
use Naturalist\Filters\EventsHandler;

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

class Events
{
    private $bnovoSectionPropEnumId = '2';

    public static function bindEvents()
    {
        $event = EventManager::getInstance();

        $event->addEventHandler("main", "OnPageStart", [EventsHandler::class, 'PageStart']);
        $event->addEventHandler("main", "OnEpilog", [EventsHandler::class, 'OnEpilog']);
        $event->addEventHandler('main', 'OnBeforeProlog', [self::class, "initGlobals"]);
        $event->addEventHandler('main', 'OnBeforeProlog', [self::class, "tgAuth"]);
        $event->addEventHandler('main', 'OnEndBufferContent', [self::class, "deleteKernelJs"]);
        $event->addEventHandler('main', 'OnEndBufferContent', [self::class, "deleteKernelCss"]);
        $event->addEventHandler('sale', 'OnSaleOrderSaved', [self::class, "createB24Deal"]);
        $event->addEventHandler('sale', 'OnSaleOrderSaved', [self::class, "makeReservation"]);
        $event->addEventHandler('sale', 'OnSaleOrderSaved', [self::class, "makeOrderCert"]);
        $event->addEventHandler('sale', 'OnSaleOrderSaved', [self::class, "cancelOrder"]);
        $event->addEventHandler('iblock', 'OnBeforeIBlockSectionDelete', [self::class, "OnBeforeIBlockSectionDeleteHandler"]);
        $event->addEventHandler('iblock', 'OnBeforeIBlockElementDelete', [self::class, "OnBeforeIBlockElementDeleteHandler"]);
    }

    public static function deleteKernelJs(&$content)
    {
        global $USER, $APPLICATION;
        if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) return;
        if ($APPLICATION->GetProperty("save_kernel") == "Y") return;
        $arPatternsToRemove = array(
            '/<script.+?src=".+?kernel_main\/kernel_main_v1\.js\?\d+"><\/script\>/',
            '/<script.+?src=".+?bitrix\/js\/main\/jquery\/jquery[^"]+"><\/script\>/',
            '/<script.+?src=".+?bitrix\/js\/main\/core\/core[^"]+"><\/script\>/',
            '/<script.+?src=".+?bitrix\/js\/pull\/protobuf\/protobuf[^"]+"><\/script\>/',
            '/<script.+?src=".+?bitrix\/js\/pull\/protobuf\/model[^"]+"><\/script\>/',
            '/<script.+?src=".+?bitrix\/js\/pull\/client\/pull[^"]+"><\/script\>/',
            '/<script.+?src=".+?bitrix\/js\/rest\/client\/rest[^"]+"><\/script\>/',
            '/<script.+?>BX\.(setCSSList|setJSList)\(\[.+?\]\).*?<\/script>/',
            '/<script.+?>if\(\!window\.BX\)window\.BX.+?<\/script>/',
            '/<script[^>]+?>\(window\.BX\|\|top\.BX\)\.message[^<]+<\/script>/',
            '/BX\.(setCSSList|setJSList)\(\[.+?\]\);/',
        );
        $content = preg_replace($arPatternsToRemove, "", $content);
        $content = preg_replace("/\n{2,}/", "\n\n", $content);
    }

    public static function deleteKernelCss(&$content)
    {
        global $USER, $APPLICATION;
        if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) return;
        if ($APPLICATION->GetProperty("save_kernel") == "Y") return;
        $arPatternsToRemove = array(
            '/<link.+?href=".+?kernel_main\/kernel_main_v1\.css\?\d+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/js\/main\/core\/css\/core[^"]+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/panel\/main\/popup[^"]+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/styles.css[^"]+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/template_styles.css[^"]+"[^>]+>/',
        );
        $content = preg_replace($arPatternsToRemove, "", $content);
        $content = preg_replace("/\n{2,}/", "\n\n", $content);
    }

    // Установка глобальных переменных для текущего пользователя
    public static function initGlobals()
    {
        global $arUser, $userId, $isAuthorized;
        $arUser = Users::getUser();
        $userId = $arUser['ID'] ?? 0;
        $isAuthorized = (int)($arUser && intval($arUser['ID']) > 0);

        global $arSettings;
        $arSettings = Settings::get();

        global $srcMainBg;
        $srcMainBg = Settings::getMainBg();

        global $arFavourites;
        $arFavourites = Users::getFavourites();
    }

    // Авторизация через Telegram
    public static function tgAuth()
    {
        if ($_GET['auth'] == 'tg') {
            $botToken = ($_SERVER['SERVER_NAME'] == 'naturalistbx.idemcloud.ru') ? '5741433095:AAGiC3egO3Ed8vnUbxc9miA2zbKRyOQA9zA' : '5700016629:AAGVivxrXTE8UNUMGMA2BML6v7APCx4VANA';

            $check_hash = $_GET['hash'];
            unset($_GET['hash']);
            unset($_GET['auth']);

            $data_check_arr = [];
            foreach ($_GET as $key => $value) {
                $data_check_arr[] = $key . '=' . $value;
            }
            sort($data_check_arr);

            $data_check_string = implode("\n", $data_check_arr);
            $secret_key = hash('sha256', $botToken, true);
            $hash = hash_hmac('sha256', $data_check_string, $secret_key);
            if (strcmp($hash, $check_hash) !== 0) {
                return false;
            }
            if ((time() - $_GET['auth_date']) > 86400) {
                return false;
            }

            $users = new Users();
            $params = array(
                'type' => 'telegram',
                'login' => $_GET['id'] ?? $_GET['username'],
                'name' => $_GET['first_name'],
                'lastname' => $_GET['last_name'],
                'photo' => $_GET['photo']
            );
            $res = $users->loginBySocnets($params);
            $arRes = json_decode($res, true);
            if ($arRes && !$arRes['ERROR']) {
                LocalRedirect('/personal/');
            }
        }
    }

    // Бронирование во внешнем сервисе после оплаты заказа
    public static function makeReservation($event)
    {
        $order = $event->getParameter("ENTITY");
        $propertyCollection = $order->getPropertyCollection();
        $paymentCollection = $order->getPaymentCollection();
        $oldValues = $event->getParameter("VALUES");

        $isNew = $order->isNew();
        $isCertProp = $propertyCollection->getItemByOrderPropertyId(ORDER_PROP_IS_CERT);
        $isCert = false;
        if ($isCertProp !== null) {
            $isCert = $isCertProp->getValue();
        }

        if ($isNew && $isCert) {
            foreach ($paymentCollection as $payment) {
                if ($payment->getPaymentSystemId() == CERT_CASH_PAYSYSTEM_ID) {
                    self::certCashOrderHandler($order);
                    return;
                }
            }
        }

        // Если это не оплата заказа
        if (!$order->getField('PAYED') || !$oldValues['PAYED'] || ($order->getField('PAYED') != 'Y') && ($oldValues['PAYED'] != 'N'))
            return;

        // Если тестовый заказ, выходим        
        $isTest = $propertyCollection->getItemByOrderPropertyId(IS_ORDER_TEST_PROP_ID)->getValue();
        if ($isTest == 'Y') {
            return;
        };

        // Если это оплата сертификата, запускаем другую функцию        
        if ($isCert == 'Y') {
            self::certOrderHandler($order);
            return;
        };

        $orderId = $order->getId();
        $orders = new Orders();
        $reservationRes = $orders->makeReservation($orderId);

        // Если успешно забронировано
        if (!isset($reservationRes["ERROR"])) {
            // Выставляем заказу статус "P" (Оплачен)
            $orders->updateStatus($orderId, "P");
        } else {
            // Отменяем заказ
            $orders->cancel($orderId, "Невозможно забронировать заказ: " . $reservationRes["ERROR"]);
        }
    }

    private static function certCashOrderHandler($order)
    {
        $propertyCollection = $order->getPropertyCollection();
        $clientEmail = $propertyCollection->getItemByOrderPropertyId(2)->getValue();
        $address = $propertyCollection->getItemByOrderPropertyId(ORDER_PROP_CERT_ADDRESS)->getValue();

        $fields = [
            "ORDER_ID" => $order->getId(),
            "COST" => $order->getPrice() . 'Р',
            "ADDRESS" => $address ? $address : 'Самовывоз',
            "EMAIL" => $clientEmail,
        ];
        Users::sendEmail("NEW_CERT_ORDER_CASH", "67", $fields);
    }

    private static function certOrderHandler($order, $noMail = false)
    {
        $orderId = $order->getId();
        $propertyCollection = $order->getPropertyCollection();
        $nominal = $propertyCollection->getItemByOrderPropertyId(ORDER_PROP_CERT_PRICE)->getValue();
        $type = $propertyCollection->getItemByOrderPropertyId(ORDER_PROP_CERT_FORMAT)->getValue();
        $clientName = $propertyCollection->getItemByOrderPropertyId(10)->getValue();
        $clientLastName = $propertyCollection->getItemByOrderPropertyId(11)->getValue();
        $clientEmail = $propertyCollection->getItemByOrderPropertyId(2)->getValue();
        $giftEmail = $propertyCollection->getItemByOrderPropertyId(ORDER_PROP_GIFT_EMAIL)->getValue();

        if (!self::checkCert($orderId)) {
            $cert = new Certificates\Create();
            $cert->add($nominal, $orderId);
        }

        // Создание pdf с сертификатом        
        $PDF = new CreateCertPdf();
        $file = json_decode($PDF->getPdfLink($orderId))->SHORT;

        if ($noMail) {
            $certFileProp = $propertyCollection->getItemByOrderPropertyId(ORDER_PROP_CERT_FILE);
            $arFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'] . $file);
            $fileId = CFile::SaveFile($arFile, 'order_certs');
            $certFileProp->setValue($fileId);
            unlink($_SERVER['DOCUMENT_ROOT'] . $file);
            $propertyCollection->save();
        } else {
            // Отправка писем в зависимости от формата сертификата
            $fields = [
                "EMAIL" => $clientEmail,
                "NAME" => $clientLastName . ' ' . $clientName,
            ];
            if ($type == 'fiz') {
                Users::sendEmail("FIZ_CERT_PURCHASED", "66", $fields, [$_SERVER["DOCUMENT_ROOT"] . $file]);
            } else {
                Users::sendEmail("EL_CERT_PURCHASED", "65", $fields, [$_SERVER["DOCUMENT_ROOT"] . $file]);
                if ($giftEmail != '') {
                    $fields['EMAIL'] = $giftEmail;
                    Users::sendEmail("EL_CERT_PURCHASED", "65", $fields, [$_SERVER["DOCUMENT_ROOT"] . $file]);
                }
            }
        }
    }

    private static function checkCert($orderId)
    {
        $hlEntity = new HighLoadBlockHelper('Certificates');
        $hlEntity->prepareParamsQuery(['*'], [], ['UF_ORDER_ID' => $orderId]);

        if ($hlEntity->getData()) {
            return true;
        }

        return false;
    }

    public static function makeOrderCert($event)
    {
        $order = $event->getParameter("ENTITY");
        $propertyCollection = $order->getPropertyCollection();
        $isCertProp = $propertyCollection->getItemByOrderPropertyId(ORDER_PROP_IS_CERT);
        if ($isCertProp == null) {
            return;
        }
        if ($isCertProp->getValue() == 'Y') {
            self::certOrderHandler($order, true);
        }
    }

    public static function cancelOrder($event)
    {
        $order = $event->getParameter("ENTITY");
        if ($order->isCanceled()) {
            $orderId = $order->getId();
            $orders = new Orders();
            $orders->cancel($orderId, 'Отмена заказа из админки');
        }
    }

    // Отправка данных в Б24
    public static function createB24Deal($event)
    {
        $order = $event->getParameter("ENTITY");
        $oldValues = $event->getParameter("VALUES");

        if (!$order->getField('PAYED') || !$oldValues['PAYED'] || ($order->getField('PAYED') != 'Y') && ($oldValues['PAYED'] != 'N')) {
            return;
        }

        $propertyCollection = $order->getPropertyCollection();
        $arFields = $propertyCollection->getArray();
        $basket = $order->getBasket();
        $basketItems = $basket->getBasketItems();

        foreach ($basketItems as $basketItem) {
            $roomName = $basketItem->getField('NAME');
            $prodId = $basketItem->getProductId();
            $price = $basketItem->getPrice();
        }

        foreach ($arFields['properties'] as $field) {
            $arProps[$field['CODE']] = $field;
        }

        // Промокод из заказа
        $couponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList(array(
            'select' => array('COUPON'),
            'filter' => array('=ORDER_ID' => $order->getId())
        ));
        while ($coupon = $couponList->fetch()) {
            $promocode = $coupon['COUPON'];
        }

        $deal = CRest::call(
            'crm.deal.add',
            [
                'fields' => [
                    'TITLE' => 'Заказ с сайта №' . $order->getId(),
                    'CATEGORY_ID' => 2,
                    'UF_CRM_64CC9F9675E53' => $arProps['OBJECT']['VALUE'][0],
                    'UF_CRM_1704886320' => $arProps['CHECKSUM']['VALUE'][0] ? ["ID" => 88] : ["ID" => 86],
                    'UF_CRM_1711465448624' => $arProps['OBJECT_ADDRESS']['VALUE'][0],
                    'UF_CRM_1711465517830' => $arProps['ROOM_PHOTO']['VALUE'][0],
                    'UF_CRM_1711466043420' => $arProps['CHECKIN_TIME']['VALUE'][0],
                    'UF_CRM_1711466052905' => $arProps['CHECOUT_TIME']['VALUE'][0],
                    'UF_CRM_1711466190125' => $arProps['DATES_NIGHTS']['VALUE'][0],
                    'UF_CRM_1711466269179' => $arProps['GUESTS_LINE_UP']['VALUE'][0],
                    'UF_CRM_1711467509210' => $arProps['GUESTS_PLACE']['VALUE'][0],
                    'SOURCE_ID' => 'STORE',
                    'UF_CRM_1711467774213' => $arProps['GUEST_LIST']['VALUE'][0],
                    'UF_CRM_1691496469' => $arProps['PHONE']['VALUE'][0],
                    'UF_CRM_1691496489' => $arProps['EMAIL']['VALUE'][0],
                    'UF_CRM_1712652449037' => $promocode ? $promocode : '',
                ]
            ]
        );

        $dealProds = CRest::call(
            'crm.deal.productrows.set',
            [
                'id' => $deal['result'],
                'rows' => [
                    [
                        // 'PRODUCT_ID' => $prodId,
                        'PRODUCT_NAME' => $roomName,
                        'PRICE' => $price,
                        'QUANTITY' => 1,
                    ]
                ]
            ]
        );
    }

    // Удаление данных по размещениям Биново при удалении объекта
    public static function OnBeforeIBlockSectionDeleteHandler($Id)
    {
        $userGroups = CurrentUser::get()->getUserGroups();
        if (in_array(MODERATOR_USER_GROUP, $userGroups)) {
            global $APPLICATION;
            $APPLICATION->throwException("Нет прав на удаление раздела");
            return false;
        }

        Loader::IncludeModule("iblock");
        $arSection = CIBlockSection::GetList(false, array(
            "IBLOCK_ID" => CATALOG_IBLOCK_ID,
            "ID" => $Id,
            "!UF_EXTERNAL_ID" => false,
            "UF_EXTERNAL_SERVICE" => '2'
        ), false, array("ID", "UF_EXTERNAL_ID"), false)->Fetch();

        if (!empty($arSection["UF_EXTERNAL_ID"])) {
            $entityClass = self::getEntityClass();
            $rsData = $entityClass::getList([
                "select" => ["*"],
                "filter" => [
                    "UF_HOTEL_ID" => $arSection["UF_EXTERNAL_ID"],
                ]
            ]);

            while ($arItem = $rsData->Fetch()) {
                $res = $entityClass::delete($arItem['ID']);

                if ($res->isSuccess()) {
                    echo ' Успешно удален: ' . $arItem['ID'];
                } else {
                    echo 'Ошибка: ' . implode(', ', $res->getErrors()) . "";
                }
            }
        }
    }

    // Запрет на удаление элемента для группы пользователей
    public static function OnBeforeIBlockElementDeleteHandler($id)
    {
        $userGroups = CurrentUser::get()->getUserGroups();
        if (in_array(MODERATOR_USER_GROUP, $userGroups)) {
            global $APPLICATION;
            $APPLICATION->throwException("Нет прав на удаление элемента");
            return false;
        }
    }

    private static function getEntityClass($hlId = 11)
    {
        Loader::IncludeModule('highloadblock');
        $hlblock = HighloadBlockTable::getById($hlId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}
