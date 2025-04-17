<?php

namespace Naturalist\bronevik\repository;

use Bitrix\Main\Diag\Logger;
use Bronevik\HotelsConnector\Element\GeoLocation;
use Bronevik\HotelsConnector\Element\Hotels;
use Bronevik\HotelsConnector\Element\HotelsWithInfo;
use Bronevik\HotelsConnector\Element\Order;
use Bronevik\HotelsConnector\Element\OrderServices;
use Bronevik\HotelsConnector\Element\ServiceAccommodation;
use Exception;
use Logger\Channels\LoggerChannel;
use Psr\Log\LoggerInterface;
use Throwable;
use SoapFault;
use Closure;

/**
 * @method string test
 * @method Hotels getHotelOffers($arrivalDate, $departureDate, $currency, $cityId = null, $searchCriteria = [], $hotelIds = [], $skipElements = [], $geolocation = null)
 * @method array getHotelAvailability($checkInDate, $checkOutDate, $currencyCode, $cityId = null, ?array $hotelIds = null, GeoLocation $geolocation = null, array $addElements = [], array $searchCriteria = [],)
 * @method HotelsWithInfo getHotelInfo(array $ids)
 * @method Hotels searchHotelOffersResponse(string $arrivalDate, string $departureDate, string $currency, ?int $cityId = null, array $searchCriteria = [], array $hotelIds = [], array $skipElements = [], ?GeoLocation $geolocation = null)
 * @method array getMeals()
 * @method string getMeal(int $id)
 * @method Order OrderCreate(int $orderId, string $offerCode, array $arGuests, array $arChildren)
 * @method bool CancelOrder(int $orderId)
 * @method Order getOrder(int $orderId)
 * @method Order|null searchOrderByReferenceId(int $orderId)
 * @method OrderServices getHotelOfferPricing(ServiceAccommodation $serviceAccommodation)
 * @method string getLastResponse()
 * @see BronevikAdapter
 */
class Bronevik
{
    private BronevikAdapter $bronevikAdapter;

    private int $attempt;

    private int $sleepSecond = 5;

    private ?Closure $checkCallback;

    private ?LoggerInterface $logger;

    public function __construct()
    {
        $this->checkCallback = null;
        $this->bronevikAdapter = new BronevikAdapter();
        $this->logger = LoggerChannel::create('BronevikLogger');
        $this->bronevikAdapter->setLogger($this->logger);

        $this->setAttempt();
    }

    public function setAttempt(int $attempt = 0): void
    {
        $this->attempt = $attempt;
    }

    /**
     * @throws Throwable
     */
    public function __call($methodName, $arguments = array())
    {
        try {
            if ($this->attempt > 0) {
                return $this->attemptCustomCall($methodName, $arguments);
            } else {
                return $this->customCall($methodName, $arguments);
            }
        } catch (Throwable $e) {
            $this->logger->error($methodName . ':' . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            throw $e;
        }
    }

    public function setSleepSecond(int $seconds): void
    {
        $this->sleepSecond = $seconds;
    }

    /**
     * @param Closure|null $callback return false need if skip exception.
     * @return void
     */
    public function setCheckBeforeAttemptCallback(?Closure $callback): void
    {
        $this->checkCallback = $callback;
    }
    /**
     * @throws Exception
     */
    private function customCall($methodName, $arguments = array())
    {
        if (method_exists($this->bronevikAdapter, $methodName)) {
            return call_user_func_array([$this->bronevikAdapter, $methodName], $arguments);
        } else {
            // Метод не существует, возвращаем ошибку
            throw new Exception("Метод {$methodName} не существует");
        }
    }

    /**
     * @throws Exception
     */
    private function attemptCustomCall($methodName, $arguments = array(), int $attempt = 0)
    {
        try {
            return $this->customCall($methodName, $arguments);
        }
        catch (SoapFault $e) {
            $check = true;
            if ($this->checkCallback !== null) {
                $check = call_user_func($this->checkCallback, $arguments, $attempt);
            }

            if ($check) {
                if ($attempt < $this->attempt) {
                    sleep($this->sleepSecond);
                    return $this->attemptCustomCall($methodName, $arguments, ++$attempt);
                } else {
                    throw new Exception($methodName . '; code: ' . $e->getCode() . '; message: ' . $e->getMessage());
                }
            } else {
                return null;
            }
        }
        catch (Throwable $e) {
            $check = true;
            if ($this->checkCallback !== null) {
                $check = call_user_func($this->checkCallback, $arguments, $attempt);
            }

            if ($check) {
                if ($attempt < $this->attempt) {
                    sleep($this->sleepSecond);
                    return $this->attemptCustomCall($methodName, $arguments, ++$attempt);
                } else {
                    throw new Exception($methodName . ' end attempts ' . $e->getMessage());
                }
            } else {
                return null;
            }
        }
    }
}