<?

namespace Naturalist;

use Bitrix\Main\EventManager;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use CIBlockSection;
use CIBlockElement;
use Naturalist\Users;
use Naturalist\Orders;
use Naturalist\Settings;
use Naturalist\CreateOrderPdf;

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

        $event->addEventHandler('main', 'OnBeforeProlog', [self::class, "initGlobals"]);
        $event->addEventHandler('main', 'OnBeforeProlog', [self::class, "tgAuth"]);
        $event->addEventHandler('main', 'OnEndBufferContent', [self::class, "deleteKernelJs"]);
        $event->addEventHandler('main', 'OnEndBufferContent', [self::class, "deleteKernelCss"]);
        $event->addEventHandler('sale', 'OnSaleOrderSaved', [self::class, "makeReservation"]);
        $event->addEventHandler('iblock', 'OnBeforeIBlockSectionDelete', [self::class, "OnBeforeIBlockSectionDeleteHandler"]);        
    }

    public static function deleteKernelJs(&$content) {
        global $USER, $APPLICATION;
        if((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/")!==false) return;
        if($APPLICATION->GetProperty("save_kernel") == "Y") return;
        $arPatternsToRemove = Array(
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

    public static function deleteKernelCss(&$content) {
        global $USER, $APPLICATION;
        if((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/")!==false) return;
        if($APPLICATION->GetProperty("save_kernel") == "Y") return;
        $arPatternsToRemove = Array(
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
    public static function initGlobals() {
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
    public static function tgAuth() {
        if($_GET['auth'] == 'tg') {
            $botToken = ($_SERVER['SERVER_NAME'] == 'naturalistbx.idemcloud.ru') ? '5741433095:AAGiC3egO3Ed8vnUbxc9miA2zbKRyOQA9zA' : '5700016629:AAGVivxrXTE8UNUMGMA2BML6v7APCx4VANA';

            $check_hash = $_GET['hash'];
            unset($_GET['hash']);
            unset($_GET['auth']);

            $data_check_arr = [];
            foreach ($_GET as $key => $value) {
                $data_check_arr[] = $key.'='.$value;
            }
            sort($data_check_arr);

            $data_check_string = implode("\n", $data_check_arr);
            $secret_key = hash('sha256', $botToken, true);
            $hash = hash_hmac('sha256', $data_check_string, $secret_key);
            if(strcmp($hash, $check_hash) !== 0) {
                return false;
            }
            if((time() - $_GET['auth_date']) > 86400) {
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
            if($arRes && !$arRes['ERROR']) {
                LocalRedirect('/personal/');
            }
        }
    }

    // Бронирование во внешнем сервисе после оплаты заказа
    public static function makeReservation($event)
    {
        $order = $event->getParameter("ENTITY");
        $oldValues = $event->getParameter("VALUES");

        // Если это не оплата заказа
        if(!$order->getField('PAYED') || !$oldValues['PAYED'] || ($order->getField('PAYED') != 'Y') && ($oldValues['PAYED'] != 'N'))
            return;

        // Если тестовый заказ, выходим
        $propertyCollection = $order->getPropertyCollection();
        $isTest = $propertyCollection->getItemByOrderPropertyId(IS_ORDER_TEST_PROP_ID)->getValue();
        if ($isTest == 'Y') {
            return;
        };

        $orderId = $order->getId();
        $orders = new Orders();
        $reservationRes = $orders->makeReservation($orderId);

        // Если успешно забронировано
        if(!isset($reservationRes["ERROR"])) {
            // Выставляем заказу статус "P" (Оплачен)
            $orders->updateStatus($orderId, "P");
        } else {
            // Отменяем заказ
            $orders->cancel($orderId, "Невозможно забронировать заказ: ".$reservationRes["ERROR"]);
        }
    }

    // Удаление данных по размещениям Биново при удалении объекта
    public static function OnBeforeIBlockSectionDeleteHandler($Id)
    {
        Loader::IncludeModule("iblock");
        $arSection = CIBlockSection::GetList(false, array(
            "IBLOCK_ID" => CATALOG_IBLOCK_ID,
            "ID" => $Id,
            "!UF_EXTERNAL_ID" => false,
            "UF_EXTERNAL_SERVICE" => '2'
        ), false, array("ID", "UF_EXTERNAL_ID"), false)->Fetch();

        if(!empty($arSection["UF_EXTERNAL_ID"])) {
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
                    echo ' Успешно удален: '.$arItem['ID'];
                } else {
                    echo 'Ошибка: ' . implode(', ', $res->getErrors()) . "";
                }

            }
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