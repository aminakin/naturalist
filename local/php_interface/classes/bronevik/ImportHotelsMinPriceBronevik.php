<?php

namespace Naturalist\bronevik;

use Bronevik\HotelsConnector\Element\HotelWithCheapestOffer;
use Bronevik\HotelsConnector\Enum\CurrencyCodes;
use CIBlockSection;
use Naturalist\bronevik\repository\Bronevik;

class ImportHotelsMinPriceBronevik
{
    private HotelBronevik $hotelBronevik;
    private Bronevik $bronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
        $this->hotelBronevik = new HotelBronevik();
    }

    public function __invoke()
    {
        $arSectionIds = $this->getSectionIds();
        $arHotelsPrice = $this->getHotelAvailabilityMinPrice($arSectionIds);
        $this->updateSectionPrice($arHotelsPrice);
    }

    private function updateSectionPrice(array $arSectionIds): void
    {
        $iS = new CIBlockSection();
        foreach ($arSectionIds as $hotelId => $price) {
            $iS->Update($hotelId, array(
                "UF_MIN_PRICE" => $price,
            ));
        }
    }

    private function getHotelAvailabilityMinPrice(array $arSectionIds): array
    {
        $arHotels = array_keys($arSectionIds);

        $hotels = $this->bronevik->getHotelAvailability(
            date('Y-m-d'),
            date('Y-m-d'),
            CurrencyCodes::RUB,
            null,
            $arHotels,
        );

        return $this->getMinimalPriceByArrayHotels($arSectionIds, $hotels);
    }

    private function getMinimalPriceByArrayHotels(array $arSectionIds, array $hotels): array
    {
        $result = [];

        /** @var HotelWithCheapestOffer $hotel */
        foreach($hotels as $hotel) {
            $result[$arSectionIds[$hotel->id]] = $hotel->minimalPriceDetails->client->clientCurrency->gross->price;
        }

        return $result;
    }

    private function getSectionIds(): array
    {
        $rsSections = $this->hotelBronevik->listFetch(["ACTIVE" => "Y", "!UF_EXTERNAL_ID" => false, "UF_EXTERNAL_SERVICE" => ImportHotelsBronevik::EXTERNAL_SERVICE_ID], false, ["ID", "UF_EXTERNAL_ID"]);
        $arSectionExternalIDs = [];
        foreach ($rsSections as $arSection) {
            $arSectionExternalIDs[$arSection["UF_EXTERNAL_ID"]] = (string)$arSection['ID'];
        }

        return $arSectionExternalIDs;
    }
}