<?php

namespace Naturalist\bronevik;

use CIBlockElement;
class HotelRoomBronevik
{
    public function list($filter = [], $order = ['ID' => 'ASC'], $select = ['*', 'PROPERTY_*']): array
    {
        $result = [];

        $res = CIBlockElement::GetList(
            $order,
            array_merge(['IBLOCK_ID' => CATALOG_IBLOCK_ID, 'PROPERTY_EXTERNAL_SERVICE' => CATALOG_IBLOCK_ELEMENT_EXTERNAL_SERVICE_ID], $filter),
            false,
            false,
            $select,
        );

        while ($element = $res->GetNextElement()) {
            $resultItem = $element->GetFields();
            $resultItem['PROPERTIES'] = $element->GetProperties();
            $result[] = $resultItem;
        }

        return $result;
    }

    public function showByExternalId(int $id): array|false
    {
        if ($result = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => CATALOG_IBLOCK_ID,
                'PROPERTY_EXTERNAL_ID' => $id,
                'PROPERTY_EXTERNAL_SERVICE' => CATALOG_IBLOCK_ELEMENT_EXTERNAL_SERVICE_ID,
            ]
        )->Fetch()) {
            return $result;
        }

        return false;
    }

    public function update(int $id, array $fields)
    {
        $iE = new CIBlockElement();

        $iE->Update($id, $fields);
    }

    public function store(array $data)
    {
        $iE = new CIBlockElement();

        return $iE->Add($data);
    }
}