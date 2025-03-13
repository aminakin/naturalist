<?php

namespace Naturalist\bronevik;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Loader;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Order;
use Naturalist\bronevik\enums\StatusOrderEnum;

class OrderChangeStatus
{
    /**
     * @throws ObjectPropertyException
     * @throws NotImplementedException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ArgumentException
     * @throws SystemException
     */
    public function __invoke(int $siteOrderId, int $bronevikStatusId)
    {
        Loader::includeModule("sale");

        $bronevikStatusEnum = StatusOrderEnum::tryFrom($bronevikStatusId);

        $order = Order::load($siteOrderId);
        $order->setField('STATUS_ID', $bronevikStatusEnum->siteStatusCode());
        $bronevikStatusValue = $order->getPropertyCollection()->getItemByOrderPropertyCode('BRONEVIK_STATUS');
        $bronevikStatusValue->setValue($bronevikStatusEnum->value);

        $order->save();
    }
}