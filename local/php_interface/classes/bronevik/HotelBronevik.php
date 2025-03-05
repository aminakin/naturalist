<?php

namespace Naturalist\bronevik;

use CIBlockSection;
class HotelBronevik
{
    const EXTERNAL_SERVICE_ID = 6;

    const CATALOG_IBLOCK_ID = 1;

    public function showByExternalId(int $externalId): array|false
    {
        return CIBlockSection::GetList([], [
            'IBLOCK_ID' => CATALOG_IBLOCK_ID,
            'UF_EXTERNAL_SERVICE' => self::EXTERNAL_SERVICE_ID,
            'UF_EXTERNAL_ID' => $externalId,
        ])->fetch();
    }

    public function listFetch($filter = [], $order = ['ID' => 'ASC'], $select = ['*', 'PROPERTY_*']): array
    {
        $result = [];
        $res = CIBlockSection::GetList(
            $order,
            array_merge(
                array(
                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                ),
                $filter
            ),
            false,
            $select,
            false
        );

        while ($element = $res->Fetch()) {
            $result[] = $element;
        }

        return $result;
    }

    public function store()
    {

    }

    public function update(?int $id, ?int $externalId, array $data): bool|null
    {
        $iS = new CIBlockSection();

        if ($externalId) {
            $hotel = $this->showByExternalId($externalId);
            $id = $hotel['ID'];
        }

        return $iS->Update($id, $data);
    }
}