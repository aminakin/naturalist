<?php

namespace Local\AddObjectBronevik\Repository;

use Local\AddObjectBronevik\Data\AdvanceHotelDTO;

interface IAddObjectBronevikRepository
{
    public function upsert(AdvanceHotelDTO $dto);
}