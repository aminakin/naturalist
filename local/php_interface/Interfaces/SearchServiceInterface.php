<?php

namespace Naturalist;

interface SearchServiceInterface {

    public function search(
        int $guests,
        array $childrenAge,
        string $dateFrom,
        string $dateTo,
        bool $groupResults = true,
        array $sectionIds = []
    );

    public function searchRooms(
        int $sectionId,
        string $externalId,
        string $serviceType,
        int $guests,
        array $childrenAge,
        string $dateFrom,
        string $dateTo,
        int $minChildAge = 0
    );
}