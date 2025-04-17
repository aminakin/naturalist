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
use Bronevik\HotelsConnector\Element\SearchOrderCriterionServiceReferenceId;
use Bronevik\HotelsConnector\Element\ServiceAccommodation;
use Bronevik\HotelsConnector\Enum\CurrencyCodes;
use Naturalist\bronevik\connector\HotelsConnector;
use Psr\Log\LoggerInterface;

class BronevikAdapter
{
    private static array $meals;

    private ?LoggerInterface $logger;

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected function sendLog($level, $message, array $context = []): void
    {
        if (! empty($this->logger)) {
            $this->logger->{$level}($message, $context);
        }
    }

    /**
     * @throws \SoapFault
     */
    public function test(): string
    {
        $connector = HotelsConnector::getInstance();
        $result = $connector->ping('Привет, Броневичок!');

        $this->sendLog('info', 'Method ping result: ' . $result );

        return $result;
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
        $result = $connector->searchHotelOffers(
            $arrivalDate,
            $departureDate,
            $currency,
            $cityId,
            $searchCriteria,
            $hotelIds,
            $skipElements,
            $geolocation,
        );

        $this->sendLog('info', 'Method searchHotelOffers', [
            'params' => [
                $arrivalDate,
                $departureDate,
                $currency,
                $cityId,
                $searchCriteria,
                $hotelIds,
                $skipElements,
                $geolocation,
            ],
            'result' => $result,
        ]);

        return $result;
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

        $result = $connector->searchHotelAvailability($checkInDate, $checkOutDate, $currencyCode, $cityId, $hotelIds, $geolocation, $addElements, $searchCriteria);

        $this->sendLog('info', 'Method searchHotelAvailability', [
            'params' => [
                $checkInDate,
                $checkOutDate,
                $currencyCode,
                $cityId,
                $hotelIds,
                $geolocation,
                $addElements,
                $searchCriteria,
            ],
            'result' => $result,
        ]);

        return $result;
    }

    /**
     * @throws \SoapFault
     * @throws \Exception
     */
    public function getHotelInfo(array $ids): HotelsWithInfo
    {
        $connector = HotelsConnector::getInstance();

        $result = $connector->getHotelInfo($ids);

        $this->sendLog('info', 'Method getHotelInfo', [
            'params' => $ids,
            'result' => $result,
        ]);

        return $result;
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

        $result = $connector->searchHotelOffers(
            $arrivalDate,
            $departureDate,
            $currency,
            $cityId,
            $searchCriteria,
            $hotelIds,
            $skipElements,
            $geolocation,
        );

        $this->sendLog('info', 'Method searchHotelOffers', [
            'params' => [
                $arrivalDate,
                $departureDate,
                $currency,
                $cityId,
                $searchCriteria,
                $hotelIds,
                $skipElements,
                $geolocation,
            ],
            'result' => $result,
        ]);

        return $result;
    }

    public function getMeals()
    {
        $connector = HotelsConnector::getInstance();

        if (isset(self::$meals)) {
            return self::$meals;
        }

        $resMeals = $connector->getMeals()->meal;

        $this->sendLog('info', 'Method getMeals', [
            'result' => $resMeals,
        ]);

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
    public function OrderCreate(int $orderId, string $offerCode, array $arGuests, array $arChildren): Order
    {
        $connector = HotelsConnector::getInstance();

        $request = new CreateOrderRequest();
        $request->setCurrency(CurrencyCodes::RUB);
        $service = new ServiceAccommodation();
        $service->setOfferCode($offerCode);
        $service->setReferenceId($orderId);
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

        $result = $connector->createOrder($request);

        $this->sendLog('info', 'Method createOrder', [
            'params' => $request,
            'result' => $result,
        ]);

        return $result;
    }

    public function CancelOrder(int $orderId): bool
    {
        $connector = HotelsConnector::getInstance();

        $result = $connector->cancelOrder($orderId);

        $this->sendLog('info', 'Method cancelOrder', [
            'params' => $orderId,
            'result' => $result,
        ]);

        return $result;
    }

    /**
     * @throws \SoapFault
     */
    public function getOrder(int $orderId): Order
    {
        $connector = HotelsConnector::getInstance();

        $result = $connector->getOrder($orderId);

        $this->sendLog('info', 'Method getOrder', [
            'params' => $orderId,
            'result' => $result,
        ]);

        return $result;
    }

    /**
     * @throws \SoapFault
     */
    public function searchOrderByReferenceId(int $referenceId): ?Order
    {
        $connector = HotelsConnector::getInstance();
        $criterionReferenceId = new SearchOrderCriterionServiceReferenceId();
        $criterionReferenceId->setReferenceId($referenceId);
        $criteria = [
            $criterionReferenceId,
        ];
        $orders = $connector->searchOrders($criteria);

        $this->sendLog('info', 'Method searchOrders', [
            'params' => $criteria,
            'result' => $orders,
        ]);

        if (count($orders->order)) {
            return $orders->order[0];
        }

        return null;
    }

    /**
     * @throws \SoapFault
     */
    public function getHotelOfferPricing(ServiceAccommodation $serviceAccommodation): OrderServices
    {
        $connector = HotelsConnector::getInstance();
        $services = [];
        $services[] = $serviceAccommodation;

        $result = $connector->getHotelOfferPricing($services, CurrencyCodes::RUB);

        $this->sendLog('info', 'Method getHotelOfferPricing', [
            'params' => [
                $services,
                CurrencyCodes::RUB,
            ],
            'result' => $result,
        ]);

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function getLastResponse(): string
    {
        $connector = HotelsConnector::getInstance();

        $result = $connector->getLastResponse();

        $this->sendLog('info', 'Method getLastResponse', [
            'result' => $result,
        ]);

        return $result;
    }
}