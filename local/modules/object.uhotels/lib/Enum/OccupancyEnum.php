<?php

namespace Object\Uhotels\Enum;

enum OccupancyEnum: int
{
    case MAIN_P1 = 1;
    case MAIN_P2 = 2;
    case ADDINGS_ADULT = 3; // Обозначение для всех значений > 2

    /**
     * Получить наименование по числовому значению.
     *
     * @param int $value
     * @return string
     */
    public static function getNameByValue(int $value): string
    {
        return match (true) {
            $value === self::MAIN_P1->value => 'main_p1',
            $value === self::MAIN_P2->value => 'main_p2',
            $value > self::ADDINGS_ADULT->value - 1 => 'addings_adult', // доп места
            default => 'unknown', // Если значение не соответствует ни одному из случаев
        };
    }
}
