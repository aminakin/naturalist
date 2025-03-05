<?php

namespace Naturalist\bronevik;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Order;
use Naturalist\bronevik\repository\Bronevik;
use SoapFault;

class OrderCreateBronevik
{
    private Bronevik $bronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
    }

    /**
     * @throws SoapFault
     */
    public function __invoke($orderId, $arOrder, $arUser, $reservationPropId)
    {
        $offerCode = $arOrder['PROPS']['BRONEVIK_OFFER_ID'];

        $guestItem = current($arOrder['PROPS']['GUEST_LIST']);
        $arGuestItem = explode(' ', $guestItem, 2);
        $arGuestItem = ['lastName' => $arGuestItem[0], 'firstName' => $arGuestItem[1]];
        $guests = [];
        for ($i = 0; $i < $arOrder['ITEMS'][0]['ITEM_BAKET_PROPS']['GUESTS_COUNT']['VALUE']; $i++) {
            $guests[] = $arGuestItem;
        }

        $children = [];
        $childrenStr = $arOrder['ITEMS'][0]['ITEM_BAKET_PROPS']['CHILDREN']['VALUE'];
        if (!empty(trim($childrenStr))) {
            $arChild = explode(',', $childrenStr);
            foreach ($arChild as $childItem) {
                $children[] = ['count' => 1, 'age' => $childItem];
            }
        }

        $this->bronevik->setAttempt(2);
        $this->bronevik->setCheckBeforeAttemptCallback(function ($arguments, $attempt) use ($orderId, $reservationPropId) {
            if (($order = $this->bronevik->searchOrderByReferenceId($orderId)) === null) {
                return true;
            }

            $this->setExternalIdToOrder($orderId, $order->getId(), $reservationPropId);

            return false;
        });
        $orderResult = $this->bronevik->OrderCreate($orderId, $offerCode, $guests, $children);
        $this->bronevik->setCheckBeforeAttemptCallback(null);

        return $this->setExternalIdToOrder($orderId, $orderResult->getId(), $reservationPropId);

    }

    /**
     * @throws ObjectPropertyException
     * @throws NotImplementedException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ArgumentException
     * @throws SystemException
     */
    private function setExternalIdToOrder($orderId, $externalId, $reservationPropId)
    {
        $order = Order::load($orderId);
        $propertyCollection = $order->getPropertyCollection();
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($reservationPropId);
        $propertyValue->setValue($externalId);
        $res = $order->save();

        if ($res->isSuccess()) {
            return $externalId;
        } else {
            return [
                "ERROR" => "Ошибка сохранения ID бронирования."
            ];
        }
    }
}