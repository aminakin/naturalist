<?php

namespace Naturalist\bronevik;

use CIBlockElement;
class HotelRoomOfferPenaltyBronevik
{
    public function __invoke(int $offerId, string $date = null)
    {
        if ($date === null) {
            $date = time();
        } else {
            if (!is_numeric($date)) {
                $date = strtotime($date);
            }
        }

        if ($element = CIBlockElement::GetList(
            false,
            array("IBLOCK_ID" => CATALOG_BRONEVIK_OFFERS_IBLOCK_ID, 'ID' => $offerId ),
            false,
            false,
            ['*', 'PROPERTY_*'],
        )->GetNextElement()) {
            $properties = $element->GetProperties();
            $cancellationPolicies = json_decode($properties['CANCELLATION_POLICIES_JSON']['~VALUE']);

            $lastDate = 0;
            $penalty = 0;
            foreach($cancellationPolicies as $item) {
                if ($lastDate < strtotime($item->penaltyDateTime) && $date > strtotime($item->penaltyDateTime)) {
                    $lastDate = strtotime($item->penaltyDateTime);
                    $penalty = $item->penaltyPriceDetails->clientCurrency->gross->price;
                }
            }

            return $penalty;
        }

        return 0;
    }
}