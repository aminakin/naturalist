<?php

namespace Naturalist;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Object\Uhotels\Data\Search;

class UhotelsSearchService implements SearchServiceInterface
{

    public function search(int $guests, array $childrenAge, string $dateFrom, string $dateTo, bool $groupResults = true, array $sectionIds = [])
    {

        if (Loader::includeModule('object.uhotels')) {

        }
    }

    /**
     * @throws LoaderException
     */
    public function searchRooms(int $sectionId, string $externalId, string $serviceType, int $guests, array $childrenAge, string $dateFrom, string $dateTo, int $minChildAge = 0)
    {
        if (Loader::includeModule('object.uhotels')) {
            $UhotelsSearch = new Search($externalId);
            $UhotelsSearch->searchHotels($sectionId, $externalId, $guests, $childrenAge, $dateFrom, $dateTo, $minChildAge);
        }
    }
}