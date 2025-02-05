<?php

namespace Naturalist\bronevik\repository;

use Bronevik\HotelsConnector\Element\Child;
use Bronevik\HotelsConnector\Element\CreateOrderRequest;
use Bronevik\HotelsConnector\Element\GeoLocation;
use Bronevik\HotelsConnector\Element\Guest;
use Bronevik\HotelsConnector\Element\Guests;
use Bronevik\HotelsConnector\Element\Hotels;
use Bronevik\HotelsConnector\Element\HotelsWithInfo;
use Bronevik\HotelsConnector\Element\Meal;
use Bronevik\HotelsConnector\Element\Order;
use Bronevik\HotelsConnector\Element\OrderServices;
use Bronevik\HotelsConnector\Element\SearchOfferCriterion;
use Bronevik\HotelsConnector\Element\ServiceAccommodation;
use Bronevik\HotelsConnector\Enum\CurrencyCodes;
use Naturalist\bronevik\connector\HotelsConnector;

class BronevikAdapter
{
    private static array $meals;
    /**
     * @throws \SoapFault
     */
    public function test(): string
    {
        $connector = HotelsConnector::getInstance();

        return $connector->ping('Привет, Броневичок!');
    }

    /**
     * @throws \SoapFault
     */
    public function getHotelOffers(
        $arrivalDate,
        $departureDate,
        $currency,
        $cityId = null,
        $searchCriteria = [],
        $hotelIds = [],
        $skipElements = [],
        $geolocation = null
    ): Hotels
    {
        $connector = HotelsConnector::getInstance();
        return $connector->searchHotelOffers(
            $arrivalDate,
            $departureDate,
            $currency,
            $cityId,
            $searchCriteria,
            $hotelIds,
            $skipElements,
            $geolocation,
        );
    }

    /**
     * @throws \SoapFault
     */
    public function getHotelAvailability(
        $checkInDate,
        $checkOutDate,
        $currencyCode,
        $cityId = null,
        ?array $hotelIds = null,
        GeoLocation $geolocation = null,
        array $addElements = [],
        array $searchCriteria = [],
    ): array
    {
        $connector = HotelsConnector::getInstance();

        return $connector->searchHotelAvailability($checkInDate, $checkOutDate, $currencyCode, $cityId, $hotelIds, $geolocation, $addElements, $searchCriteria);
    }

    /**
     * @throws \SoapFault
     * @throws \Exception
     */
    public function getHotelInfo(array $ids): HotelsWithInfo
    {
        $connector = HotelsConnector::getInstance();

        try {
            return $connector->getHotelInfo($ids);
        } catch (\Exception $e) {
            throw new \Exception($this->getLastResponse());
        }
    }

    /**
     * Поиск предложений
     *
     * @param string                         $arrivalDate
     * @param string                         $departureDate
     * @param string                         $currency
     * @param int|null                       $cityId
     * @param SearchOfferCriterion[]         $searchCriteria
     * @param int[]                          $hotelIds
     * @param string[]                       $skipElements
     * @param GeoLocation|null               $geolocation
     *
     * @return Hotels
     * @throws \SoapFault
     */
    public function searchHotelOffersResponse(
        string $arrivalDate,
        string $departureDate,
        string $currency,
        ?int $cityId = null,
        array $searchCriteria = [],
        array $hotelIds = [],
        array $skipElements = [],
        ?GeoLocation $geolocation = null,
    ): Hotels
    {
        $connector = HotelsConnector::getInstance();

        return $connector->searchHotelOffers(
            $arrivalDate,
            $departureDate,
            $currency,
            $cityId,
            $searchCriteria,
            $hotelIds,
            $skipElements,
            $geolocation,
        );
    }

    public function getMeals()
    {
        $connector = HotelsConnector::getInstance();

        if (isset(self::$meals)) {
            return self::$meals;
        }

        $resMeals = $connector->getMeals()->meal;
        /** @var Meal $meal */
        foreach ($resMeals as $meal) {
            self::$meals[$meal->id] = $meal->name;
        }

        return self::$meals;
    }

    public function getMeal(int $id)
    {
        $meals = $this->getMeals();

        return $meals[$id];
    }

    /**
     * @throws \SoapFault
     */
    public function OrderCreate(string $offerCode, array $arGuests, array $arChildren): Order
    {
        $connector = HotelsConnector::getInstance();

        $request = new CreateOrderRequest();
        $request->setCurrency(CurrencyCodes::RUB);
        $service = new ServiceAccommodation();
        $service->setOfferCode($offerCode);
        $guests = new Guests();
        foreach($arGuests as $guestItem) {
            $guest = new Guest();
            $guest->setFirstName($guestItem['firstName']);
            $guest->setLastName($guestItem['lastName']);

            $guests->add($guest);
        }

        foreach ($arChildren as $childItem) {
            $child = new Child();
            $child->setCount($childItem['count']);
            $child->setAge($childItem['age']);

            $guests->addChild($child);
        }

        $service->setGuests($guests);

        $request->addServices($service);

        return $connector->createOrder($request);
    }

    public function CancelOrder(int $orderId): bool
    {
        $connector = HotelsConnector::getInstance();

        return $connector->cancelOrder($orderId);
    }

    /**
     * @throws \SoapFault
     */
    public function getOrder(int $orderId): Order
    {
        $connector = HotelsConnector::getInstance();

        return $connector->getOrder($orderId);
    }

    /**
     * @throws \SoapFault
     */
    public function getHotelOfferPricing(ServiceAccommodation $serviceAccommodation): OrderServices
    {
        $connector = HotelsConnector::getInstance();
        $services = [];
        $services[] = $serviceAccommodation;

        return $connector->getHotelOfferPricing($services, CurrencyCodes::RUB);
    }

    /**
     * @throws \Exception
     */
    public function getLastResponse(): string
    {
        $connector = HotelsConnector::getInstance();

        return $connector->getLastResponse();
    }
}