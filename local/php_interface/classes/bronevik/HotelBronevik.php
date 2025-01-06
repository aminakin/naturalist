<?php

namespace Naturalist\bronevik;

use CIBlockSection;
class HotelBronevik
{
    const EXTERNAL_SERVICE_ID = 6;

    public function showByExternalId(int $externalId): array|false
    {
        return CIBlockSection::GetList([], [
            'IBLOCK_ID' => CATALOG_IBLOCK_ID,
            'UF_EXTERNAL_SERVICE' => self::EXTERNAL_SERVICE_ID,
            'UF_EXTERNAL_ID' => $externalId,
        ])->fetch();
    }
}