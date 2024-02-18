<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Iblock;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Main\Type\Date;
use Naturalist\Certificates\CatalogHelper;
use Naturalist\Certificates\OrderHelper;
use Naturalist\Orders;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Delivery\Services;

Loc::loadMessages(__FILE__);

class CertBuy extends \CBitrixComponent 
{
    private $certificates;
    private $postList = [];

    public function onPrepareComponentParams($arParams)
	{        
        return $arParams;
    }
    
    protected function prepareResultArray()
	{
        Loader::includeModule("sale");

        $this->certificates = new CatalogHelper();

        $this->arResult = [
            'LOCAL_HOST' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'],
            'CERTIFICATES' => $this->getCertificates(),
            'VARIANT' => $this->certificates->hlVariantsValues,
            'VARIANT_EL' => $this->certificates->hlElVariantsValues,
            'POCKET' => $this->certificates->hlPocketsValues,
            'PAY_SYSTEMS' => PaySystem\Manager::getList([
                'order' => ['SORT' => 'ASC'],
                'filter'  => [
                    'ACTIVE' => 'Y',
                    '!ACTION_FILE' => 'inner',
                    'CODE' => 'CERT',
                ]
            ])->fetchAll(),
            'DELIVERIES' => Services\Table::getList([
                'filter' => [
                    'ACTIVE'=>'Y',
                    'PARENT_ID' => CERT_DELIVERY_PARENT_ID,
                ]
            ])->fetchAll(),
        ];
    }

    public function executeComponent()
	{
		global $APPLICATION;		
        $this->prepareResultArray();        
        $this->handleRequest();
		$this->includeComponentTemplate();
	}

    /**
     * Возвращает список сертификатов из каталога
     *
     * @return array
     * 
     */
    private function getCertificates() : array
    {        
        return $this->certificates->getProducts();
    }

    /**
     * Обрабатывает входящий POST запрос     
     */
    protected function handleRequest() : void
    {
        $request = Context::getCurrent()->getRequest();
        $this->postList = $request->getPostList()->toArray();
        if (count($this->postList)) {
            $order = new OrderHelper($this->postList);
            $orderId = $order->add();
            if ($orderId) {
                $this->getPaymentUrl($orderId);                
            } else {
                $this->arResult['ERROR'] = 'Произошла ошибка. Пожалуйста, попробуйте позже.';
            }
        }
    }

    /**
     * [Description for getPaymentUrl]
     *
     * @param int $orderId
     * 
     * @return void
     * 
     */
    private function getPaymentUrl(int $orderId) : void
    {
        if ($this->postList['paysystem'] == CERT_CASH_PAYSYSTEM_ID) {
            $this->arResult['PAYMENT_URL'] = '/certificates/success/';
            return;
        }

        $order = new Orders;

        if ($this->postList['paysystem'] == CERT_SBER_PAYSYSTEM_ID) {
            $this->arResult['PAYMENT_URL'] = $order->getPaymentUrl($orderId, false);
            return;
        }        

        $url = $this->arResult['LOCAL_HOST']."/bitrix/services/yandexpay.pay/trading/orders";
        $data = $order->getPaymentUrl($orderId, false, true);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json",
                'method'  => 'POST',
                'content' => json_encode($data),
            ),
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            // Обработка ошибки
            $this->arResult['ERROR'] = "Ошибка при отправке запроса.";
        } else {
            $result = json_decode($response, true);

            if ($result['status'] === "success") {
                $this->arResult['PAYMENT_URL'] = $result['data']['paymentUrl'];
            } else {
                $this->arResult['ERROR'] = "orders error - " . $result['reasonCode'] . $result['reason'];
            }
        }
    }
}