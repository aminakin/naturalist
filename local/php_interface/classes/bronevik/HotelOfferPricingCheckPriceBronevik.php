<?php

namespace Naturalist\bronevik;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\BasketBase;
use Bitrix\Sale\BasketItem;
use Bitrix\Sale\BasketPropertyItem;
use Bronevik\HotelsConnector\Element\Child;
use Bronevik\HotelsConnector\Element\Guest;
use Bronevik\HotelsConnector\Element\Guests;
use Bronevik\HotelsConnector\Element\OrderServiceAccommodation;
use Bronevik\HotelsConnector\Element\ServiceAccommodation;
use Naturalist\bronevik\repository\Bronevik;

class HotelOfferPricingCheckPriceBronevik
{
    private Bronevik $bronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
    }

    /**
     * @throws ObjectNotFoundException
     * @throws \SoapFault
     * @throws ObjectPropertyException
     * @throws NotImplementedException
     * @throws ArgumentNullException
     * @throws ArgumentException
     * @throws SystemException
     */
    public function __invoke(BasketBase $basket, array $guest)
    {
        $items = $basket->getBasketItems();
        $isValidate = true;
        /** @var BasketItem $item */
        foreach ($items as $item) {
            $lastName = $guest['LAST_NAME'];
            $firstName = $guest['FIRST_NAME'];
            list($guestsCount, $children, $offerId) = $this->buildVariables($item);

            $service = $this->buildService($guestsCount, $lastName, $firstName, $children, $offerId);

            $orderService = $this->bronevik->getHotelOfferPricing($service);

            /** @var OrderServiceAccommodation $service */
            $orderServiceAccommodation = current($orderService->service);

            $servicePrice = floatval($orderServiceAccommodation->priceDetails->client->clientCurrency->gross->price);
            if ($servicePrice != floatval($item->getField('PRICE'))) {
                $item->setPrice($servicePrice);
                $item->setField('CUSTOM_PRICE', 'Y');
                $item->save();

                $isValidate = false;
            }
        }

        $basket->save();

        return $isValidate;
    }

    /**
     * @throws ArgumentNullException
     * @throws ObjectNotFoundException
     * @throws ArgumentException
     * @throws NotImplementedException
     */
    private function buildVariables(BasketItem $item): array
    {
        $propertyCollection = $item->getPropertyCollection();
        $orderPropertyCollection = $item->getPropertyCollection();

        $guestsCount = 0;
        $offerId = '';
        $children = null;
        /** @var BasketPropertyItem $propertyItem */
        foreach ($propertyCollection as $propertyItem) {
            if ('GUESTS_COUNT' == $propertyItem->getField('CODE')) {
                $guestsCount = $propertyItem->getFieldValues()['VALUE'];
            }
            if ('CHILDREN' == $propertyItem->getField('CODE')) {
                $children = $propertyItem->getFieldValues()['VALUE'];
            }
        }

        /** @var BasketPropertyItem $propertyItem */
        foreach ($orderPropertyCollection as $propertyItem) {
            if ('BRONEVIK_OFFER_ID' == $propertyItem->getField('CODE')) {
                $offerId = $propertyItem->getFieldValues()['VALUE'];
            }
        }

        return [$guestsCount, $children, $offerId];
    }

    private function buildService($guestsCount, $lastName, $firstName, $children, $offerId): ServiceAccommodation
    {
        $guests = new Guests();
        for ($i = 0; $i < $guestsCount; $i++) {
            $guest = new Guest();
            $guest->setLastName($lastName);
            $guest->setFirstName($firstName);
            $guests->add($guest);
        }

        if ($children !== null) {
            $arChildren = explode(',', $children);
            foreach ($arChildren as $childItem) {
                $child = new Child();
                $child->setCount(1);
                $child->setAge($childItem);
                $guests->addChild($child);
            }
        }

        $service = new ServiceAccommodation();
        $service->setOfferCode($offerId);
        $service->setGuests($guests);

        return $service;
    }
}