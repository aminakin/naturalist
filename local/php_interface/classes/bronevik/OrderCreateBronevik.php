<?php

namespace Naturalist\bronevik;

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

        $orderResult = $this->bronevik->OrderCreate($offerCode, $guests, $children);

        $order = Order::load($orderId);
        $propertyCollection = $order->getPropertyCollection();
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($reservationPropId);
        $propertyValue->setValue($orderResult->getId());
        $res = $order->save();

        if ($res->isSuccess()) {
            return $orderResult->getId();
        } else {
            return [
                "ERROR" => "Ошибка сохранения ID бронирования."
            ];
        }
    }
}