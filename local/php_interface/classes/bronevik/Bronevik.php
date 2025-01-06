<?php

namespace Naturalist\bronevik;

use Bronevik\HotelsConnector\Element\GeoLocation;
use Bronevik\HotelsConnector\Element\Meal;
use Bronevik\HotelsConnector\Element\Meals;
use Bronevik\HotelsConnector\Element\SearchOfferCriterion;
use Naturalist\bronevik\connector\HotelsConnector;
use Bronevik\HotelsConnector\Element\Hotels;
use Bronevik\HotelsConnector\Element\HotelsWithInfo;

class Bronevik
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
     */
    public function getHotelInfo(array $ids): HotelsWithInfo
    {
        $connector = HotelsConnector::getInstance();

        return $connector->getHotelInfo($ids);
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
}