<?php

namespace Naturalist\bronevik;

use Naturalist\bronevik\SearchRoomsBronevik;
use Naturalist\bronevik\SearchHotelsBronevik;
use Naturalist\SearchServiceInterface;

class BronevikSearchService implements SearchServiceInterface
{

    /**
     * @throws \SoapFault
     */
    public function search(int $guests, array $childrenAge, string $dateFrom, string $dateTo, bool $groupResults = true, array $sectionIds = [])
    {
        return (new SearchHotelsBronevik())($guests, $childrenAge, $dateFrom, $dateTo, $groupResults, $sectionIds);
    }

    /**
     * @throws \SoapFault
     */
    public function searchRooms(int $sectionId, string $externalId, string $serviceType, int $guests, array $childrenAge, string $dateFrom, string $dateTo, int $minChildAge = 0)
    {
        return (new SearchRoomsBronevik())($sectionId, $externalId, $guests, $childrenAge, $dateFrom, $dateTo, $minChildAge);
    }
}