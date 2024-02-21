<?php

namespace Naturalist\Certificates;

use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Delivery;
use Bitrix\Main\Context;
use Bitrix\Currency\CurrencyManager;
use Naturalist\Users;

/**
 * Работа с заказом сертификата.
 */

class OrderHelper
{

    private $userId;
    private $siteId;
    private $params = [];
    private $item;
    private $itemPrice;
    private $basket;
    private $order;
    private $arProps = [
        'login' => ORDER_PROP_PHONE,
        'email' => ORDER_PROP_EMAIL,
        'name' => ORDER_PROP_NAME,
        'last_name' => ORDER_PROP_LAST_NAME,
        'city' => ORDER_PROP_CITY,
        'is_cert' => ORDER_PROP_IS_CERT,
        'cert_variant' => ORDER_PROP_FIZ_VARIANT,
        'cert_pocket' => ORDER_PROP_FIZ_POCKET,
        'gift_name' => ORDER_PROP_GIFT_NAME,
        'gift_email' => ORDER_PROP_GIFT_EMAIL,
        'cert_el_variant' => ORDER_PROP_ELECTRO_VARIANT,
        'congrats' => ORDER_PROP_CONGRATS,
        'cert_format' => ORDER_PROP_CERT_FORMAT,
    ];

    public function __construct(array $params)
    {
        Loader::includeModule("sale");

        $this->siteId = Context::getCurrent()->getSite();
        $this->params = $params;
    }


    /**
     * Создаёт заказ и возаращает ссылку на оплату или ошибку          
     * 
     * @return string
     * 
     */
    public function add()
    {
        $this->checkUser();
        $this->createOrder();
        $this->createBasket();
        $this->modifyBasket();
        $this->addDelivery($this->params['cert_format'] == 'electro' ? true : false);
        $this->addPayment();
        $this->setOrderProps();
        $this->order->doFinalAction(true);
        $this->order->save();
        $orderId = $this->order->getId();
        if ($orderId) {
            return $orderId;        
        }
        return false;        
    }

    /**
     * Устанавливает ID пользователя (авторегистрация)
     *
     * @return void
     * 
     */
    private function checkUser() : void
    {
        global $userId;

        if (intval($userId) < 1) {
            $users = new Users();
            $this->userId = $users->authGetCode($this->params);
        } else {
            $this->userId = $userId;
        }
    }

    /**
     * Создаёт корзину
     *
     * @return void
     * 
     */
    private function createBasket() : void
    {
        $this->basket = Basket::create($this->siteId);
        $this->item = $this->basket->createItem('catalog', $this->params['cert_id']);
        $arFields = [
            'QUANTITY' => 1,
            'CURRENCY' => CurrencyManager::getBaseCurrency(),
            'LID' => $this->siteId,
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        ];

        $this->item->setFields($arFields);        
        $this->order->setBasket($this->basket);        
    }

    /**
     * [Description for modifyBasket]
     *
     * @return void
     * 
     */
    private function modifyBasket() : void
    {
        $arFields = [];
        $basketItems = $this->basket->getBasketItems();
        $item = $basketItems[0];

        // Проверка на польвательскую цену сертификата
        if ($this->params['cert_price'] != '') {
            $arFields['PRICE'] = $this->params['cert_price'];
            $arFields['CUSTOM_PRICE'] = 'Y';
        }

        $item->setFields($arFields);

        // Цена сертификата для добавления в свойство заказа, т.к. дальше она может измениться
        $this->itemPrice = $item->getPrice();

        // Цена сетрификата для дальнейших рассчётов
        $itemPrice = $item->getPrice();

        // Проверка на доплату за физический формат и упаковку
        if ($this->params['cert_format'] == 'fiz') {
            if (isset($this->params['cert_variant']) && $this->params['cert_variant'] != '') {
                $itemPrice += $this->params['variant_cost'];
            }

            if (isset($this->params['cert_pocket']) && $this->params['cert_pocket'] != '') {
                $itemPrice += $this->params['pocket_cost'];
            }

            $arFields['PRICE'] = $itemPrice;
            $arFields['CUSTOM_PRICE'] = 'Y';
        }

        $item->setFields($arFields);        
        $this->basket->save();
    }

    /**
     * Создаёт заказ
     *
     * @return void
     * 
     */
    private function createOrder() : void
    {
        $this->order = Order::create($this->siteId, $this->userId);        
        $this->order->setPersonTypeId(1);        
    }

    /**
     * Создаёт доставку
     *
     * @param bool $inner
     * 
     * @return void
     * 
     */
    private function addDelivery(bool $inner = false) : void
    {
        $shipmentCollection = $this->order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $service = Delivery\Services\Manager::getById($inner ? Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId() : $this->params['delivery']);
        $shipment->setFields(array(
            'DELIVERY_ID' => $service['ID'],
            'DELIVERY_NAME' => $service['NAME'],
        ));
        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        $shipmentItem = $shipmentItemCollection->createItem($this->item);
        $shipmentItem->setQuantity($this->item->getQuantity());
    }

    /**
     * Добавляет оплату
     *
     * @return void
     * 
     */
    private function addPayment() : void
    {
        $paymentCollection = $this->order->getPaymentCollection();
        $paySystemService = PaySystem\Manager::getObjectById($this->params['paysystem']);
        $payment = $paymentCollection->createItem($paySystemService);
        $payment->setFields([
            'SUM' => $this->order->getPrice(),
            'CURRENCY' => $this->order->getCurrency(),
        ]);        
    }

    /**
     * Устанавливает необходимые свойства заказа
     *
     * @return void
     * 
     */
    private function setOrderProps() : void
    {
        // Комментарий
        $this->order->setField('USER_DESCRIPTION', $this->params["comment"]);

        // Цена сертификата
        $this->setOneOrderProp(ORDER_PROP_CERT_PRICE, $this->itemPrice);

        // Остальыне свойства
        foreach ($this->arProps as $propName => $prop) {
            $this->setOneOrderProp($prop, $this->params[$propName]);
        }        
    }

    /**
     * Устанавливает конкретное свойство заказа
     *
     * @return void
     * 
     */
    private function setOneOrderProp(int $propId, $propValue) : void
    {
        $propertyCollection = $this->order->getPropertyCollection();
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($propId);
        $propertyValue->setValue($propValue);
    }
}