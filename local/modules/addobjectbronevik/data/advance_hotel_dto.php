<?php

namespace Local\AddObjectBronevik\Data;

class AdvanceHotelDTO
{
    public function __construct(
        public string $name,
        public string $code,
        public string $type,
        public string $address,
        public string $city,
        public string $country,
        public ?string $zip,
        public string $lat,
        public string $lon,
        public string $description,
        public string $photos,
        public int $line,
    ) {}

    public function toArray(): array
    {
        return [
            'NAME' => $this->name,
            'CODE' => $this->code,
            'TYPE' => $this->type,
            'ADDRESS' => $this->address,
            'CITY' => $this->city,
            'COUNTRY' => $this->country,
            'ZIP' => $this->zip,
            'LAT' => $this->lat,
            'LON' => $this->lon,
            'DESCRIPTION' => $this->description,
            'PHOTOS' => $this->photos,
        ];
    }
}