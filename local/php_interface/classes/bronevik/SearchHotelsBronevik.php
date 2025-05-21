<?php

namespace Naturalist\bronevik;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Diag\Debug;
use Bronevik\HotelsConnector\Element\AvailableMeal;
use Bronevik\HotelsConnector\Element\Child;
use Bronevik\HotelsConnector\Element\HotelOffer;
use Bronevik\HotelsConnector\Element\HotelOfferCancellationPolicy;
use Bronevik\HotelsConnector\Element\Hotels;
use Bronevik\HotelsConnector\Element\HotelWithCheapestOffer;
use Bronevik\HotelsConnector\Element\HotelWithOffers;
use Bronevik\HotelsConnector\Element\OfferPolicy;
use Bronevik\HotelsConnector\Element\RateType;
use Bronevik\HotelsConnector\Element\SearchOfferCriterionNumberOfGuests;
use Bronevik\HotelsConnector\Element\SearchOfferCriterionOnlyOnline;
use Bronevik\HotelsConnector\Element\SearchOfferCriterionPaymentRecipient;
use Bronevik\HotelsConnector\Element\SkipElements;
use Bronevik\HotelsConnector\Enum\CurrencyCodes;
use CIBlockElement;
use Naturalist\bronevik\repository\Bronevik;

class SearchHotelsBronevik
{
    private Bronevik $bronevik;

    private HotelRoomBronevik $hotelRoomBronevi;
    private HotelRoomOfferBronevik $hotelRoomOfferBronevik;

    private HotelBronevik $hotelBronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
        $this->hotelRoomBronevi = new HotelRoomBronevik();
        $this->hotelBronevik = new HotelBronevik();
        $this->hotelRoomOfferBronevik = new HotelRoomOfferBronevik();
    }

    /**
     * @param $guests - кол-во гостей
     * @param $childrenAge - возраст детей
     * @param $dateFrom - Дата от
     * @param $dateTo - Дата до
     * @param $groupResults
     * @param $sectionIds
     * @return array
     */
    public function __invoke($guests, $childrenAge, $dateFrom, $dateTo, $groupResults, $sectionIds): array
    {

        $cache = Cache::createInstance();
        $cacheKey = 'SearchHotelsBronevik' . $dateFrom . $dateTo . $guests;

        if (!$sectionIds) {
            if ($cache->initCache(3600, $cacheKey)) {
                $sectionIds = $cache->getVars();
            } elseif ($cache->startDataCache()) {
                $rooms = $this->hotelBronevik->listFetch(
                    [
                        'ACTIVE' => 'Y',
                        'UF_EXTERNAL_SERVICE' => CATALOG_IBLOCK_SECTION_UF_EXTERNAL_SERVICE_ID
                    ],
                    ['ID' => 'ASC'],
                    ['UF_EXTERNAL_ID']);

                $sectionIds = array_column($rooms, 'UF_EXTERNAL_ID');
                $cache->endDataCache($sectionIds);
            }
        }

        //api броневика не принимает более 300 отелей в запросе
        $chunks = array_chunk($sectionIds, 300, false);
        $hotels = [];

        foreach ($chunks as $chunk) {
            $result = $this->bronevik->getHotelAvailability(
                date('Y-m-d', strtotime($dateFrom)),
                date('Y-m-d', strtotime($dateTo)),
                CurrencyCodes::RUB,
                null,
                $chunk
            );


            if ($result) {
                $hotels = array_merge($hotels, $result);
            }
        }


        return ($hotels) ? $this->getPriceByArrayHotels($hotels) : [];
    }

    private function getPriceByArrayHotels(array $hotels): array
    {
        $result = [];

        /** @var HotelWithCheapestOffer $hotel */
        foreach($hotels as $hotel) {
            $result[$hotel->id]['PRICE'] = $hotel->minimalPriceDetails->client->clientCurrency->gross->price;
        }

        return $result;
    }
}