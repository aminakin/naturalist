<?php

namespace Naturalist\bronevik\enums;

enum RoomTypeEnum: string
{
    case SINGLE = 'single';
    case DOUBLE = 'double';
    case TWIN = 'twin';
    case TRIPLE = 'triple';
    case QUADRUPLE = 'quadruple';

    public function translateValue(): string
    {
        return match ($this) {
            RoomTypeEnum::SINGLE => 'Одноместное размещение',
            RoomTypeEnum::DOUBLE => 'Двухместное размещение (1 кровать)',
            RoomTypeEnum::TWIN => 'Двухместное размещение (2 кровати)',
            RoomTypeEnum::TRIPLE => 'Трехместное размещение',
            RoomTypeEnum::QUADRUPLE => 'Четырехместное размещение',
            default => throw new \Exception('Unexpected match value'),
        };
    }
}
