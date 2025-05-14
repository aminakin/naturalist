<?php

namespace Naturalist\bronevik;

use Naturalist\bronevik\SearchRoomsBronevik;
use Naturalist\SearchServiceInterface;

class BronevikSearchService implements SearchServiceInterface
{

    public function search(int $guests, array $childrenAge, string $dateFrom, string $dateTo, bool $groupResults = true, array $sectionIds = [])
    {
        return [];
    }

    /**
     * @throws \SoapFault
     */
    public function searchRooms(int $sectionId, string $externalId, string $serviceType, int $guests, array $childrenAge, string $dateFrom, string $dateTo, int $minChildAge = 0)
    {
        return (new SearchRoomsBronevik())($sectionId, $externalId, $guests, $childrenAge, $dateFrom, $dateTo, $minChildAge);
    }
}