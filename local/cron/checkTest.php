<?
set_time_limit(600);
if (!$_SERVER['DOCUMENT_ROOT']) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../");
}
include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Sale\Order;
use Bitrix\Sale\Basket;
use Bitrix\Sale\PaySystem;
use Bitrix\Main\Mail\Event;
Loader::includeModule( 'sberbank.ecom2' );

if (CModule::IncludeModule('sale'))
{
    $orderId = $_GET['orderId'];
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

    if(!empty($payment)) {
        $arPaymentsCollection = $order->loadPaymentCollection();
        $currentPaymentOrder = $arPaymentsCollection->current();
        do{
            $currentPaymentId = ($currentPaymentOrder->getField("PS_INVOICE_ID"));
        } while($currentPaymentOrder = $arPaymentsCollection->next());

        // module settings
        $RBS_Gateway = new \Sberbank\Payments\Gateway;

        $moduleId = 'sberbank.ecom2';

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
        xprint($gateResponse);

        //Чеки
        $moduleId = 'sberbank.ecom2';

        $RBS_Gateway = new \Sberbank\Payments\Gateway;

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
            'amount' => $order->GetPrice()*100,
            'userName' => SBR_LOGIN,
            'password' => SBR_PASSWORD,
        ));

        $gateResponse = $RBS_Gateway->closeOfdReceipt();

        xprint($gateResponse);
    }
}