<?php

namespace Local\AddObjectBronevik\Repository;

use Local\AddObjectBronevik\Data\AdvanceHotelDTO;
use Local\AddObjectBronevik\Orm\AddObjectBronevikTable;

class AddObjectBronevikMysqlHotelRepository implements IAddObjectBronevikRepository
{

    public function upsert(AdvanceHotelDTO $dto)
    {
        global $DB;
        $data = $dto->toArray();
        $result = AddObjectBronevikTable::getList([
            'filter' => [
                '=CODE' => $dto->code,
            ],
        ]);

        if ($row = $result->fetch()) {
            $data['LAST_MODIFIED'] = new \Bitrix\Main\Type\DateTime();
            AddObjectBronevikTable::update($row['ID'], $data);
        } else {
            AddObjectBronevikTable::add($data);
        }
    }
}