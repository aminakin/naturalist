<?php

namespace Object\Uhotels\Enum;

enum OccupancyEnum: int
{
    case MAIN_P1 = 1;
    case MAIN_P2 = 2;
    case MAIN_P3 = 3;
    case WPLACE_BABY = 4;
    case ADDINGS_BABY = 5;
    case ADDINGS_ADULT = 6;

    /**
     * Получить числовое значение по occupancy_code.
     *
     * @param string $code
     * @return int|null
     */
    public static function getValueByCode(string $code): ?int
    {
        return match ($code) {
            'main_p1' => self::MAIN_P1->value,
            'main_p2' => self::MAIN_P2->value,
            'main_p3' => self::MAIN_P3->value,
            'wplace_baby' => self::WPLACE_BABY->value,
            'addings_baby' => self::ADDINGS_BABY->value,
            'addings_adult' => self::ADDINGS_ADULT->value,
            default => null, // Если код не найден
        };
    }

    /**
     * Получить occupancy_code по числовому значению.
     *
     * @param int $value
     * @return string|null
     */
    public static function getCodeByValue(int $value): ?string
    {
        return match ($value) {
            self::MAIN_P1->value => 'main_p1',
            self::MAIN_P2->value => 'main_p2',
            self::MAIN_P3->value => 'main_p3',
            self::WPLACE_BABY->value => 'wplace_baby',
            self::ADDINGS_BABY->value => 'addings_baby',
            self::ADDINGS_ADULT->value => 'addings_adult',
            default => null, // Если значение не найдено
        };
    }
}