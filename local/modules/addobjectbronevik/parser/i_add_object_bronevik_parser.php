<?php

namespace Local\AddObjectBronevik\Parser;

use Local\AddObjectBronevik\Data\AdvanceHotelDTO;

interface IAddObjectBronevikParser
{
    public function setFilePath(string $filePath);

    public function getNextElement(): false|AdvanceHotelDTO;
}