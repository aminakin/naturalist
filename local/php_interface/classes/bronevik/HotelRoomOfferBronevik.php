<?php

namespace Naturalist\bronevik;

use CIBlockElement;
class HotelRoomOfferBronevik
{
    public function list($filter = [], $order = ['ID' => 'ASC'], $select = ['*', 'PROPERTY_*'], string $type = ''): array
    {
        $result = [];

        $res = CIBlockElement::GetList(
            $order,
            array_merge(['IBLOCK_ID' => CATALOG_BRONEVIK_OFFERS_IBLOCK_ID], $filter),
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
    public function stage(array $data)
    {
        $iE = new CIBlockElement();
        $iE->Add($data);
    }

    public function update(int $id, array $data)
    {
        $iE = new CIBlockElement();
        $iE->Update($id, $data);
    }
}