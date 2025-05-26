<?php

namespace Naturalist;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Object\Uhotels\Data\Search;
use Object\Uhotels\Data\SearchHotels;

class UhotelsSearchService implements SearchServiceInterface
{

    public function search(int $guests, array $childrenAge, string $dateFrom, string $dateTo, bool $groupResults = true, array $sectionIds = [])
    {
        if (Loader::includeModule('object.uhotels')) {
            return SearchHotels::search($guests, $childrenAge, $dateFrom, $dateTo, $groupResults, $sectionIds);
        }
    }

    /**
     * @throws LoaderException
     * @throws \Exception
     */
    public function searchRooms(int $sectionId, string $externalId, string $serviceType, int $guests, array $childrenAge, string $dateFrom, string $dateTo, int $minChildAge = 0)
    {
        if (Loader::includeModule('object.uhotels')) {
            $UhotelsSearch = new Search($externalId);

            return $UhotelsSearch->searchHotels($sectionId, $externalId, $guests, $childrenAge, $dateFrom, $dateTo, $minChildAge);
        }
        return [
            'arRooms' => [],
            'error' => 'Не найдено номеров на выбранные даты',
        ];
    }
}