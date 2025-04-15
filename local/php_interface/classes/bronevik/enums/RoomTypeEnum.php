<?php

namespace Naturalist\bronevik\enums;

enum RoomTypeEnum: string
{
    case SINGLE = 'single';
    case DOUBLE = 'double';
    case TWIN = 'twin';
    case TRIPLE = 'triple';
    case QUADRUPLE = 'quadruple';

    /**
     * Возвращает локализованное описание типа размещения.
     *
     * @return string Локализованное описание типа размещения.
     * @throws \Exception Если значение перечисления неожиданное.
     */
    public function translateValue(): string
    {
        return match ($this) {
            RoomTypeEnum::SINGLE => 'Одноместное размещение',
            RoomTypeEnum::DOUBLE => 'Двухместное размещение (1 кровать. Тип кровати не гарантирован)',
            RoomTypeEnum::TWIN => 'Двухместное размещение (2 кровати. Тип кровати не гарантирован)',
            RoomTypeEnum::TRIPLE => 'Трехместное размещение',
            RoomTypeEnum::QUADRUPLE => 'Четырехместное размещение',
            default => throw new \Exception('Unexpected match value'),
        };
    }

    /**
     * Возвращает вариант перечисления по числу.
     *
     * @param int $number
     * @return self|null
     */
    public static function fromNumber(int $number): ?self
    {
        return match ($number) {
            1 => self::SINGLE,
            2 => self::DOUBLE,
            3 => self::TRIPLE,
            4 => self::QUADRUPLE,
            default => null, // Если число не соответствует ни одному варианту
        };
    }

    /**
     * Возвращает вариант перечисления по строковому значению.
     *
     * @param string $value Строковое значение типа размещения (например, "single", "double").
     * @return self|null Соответствующий вариант перечисления или null, если значение не найдено.
     */
    public static function fromString(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null; // Если строка не соответствует ни одному варианту
    }

    /**
     * Возвращает локализованное описание типа размещения по строковому значению.
     *
     * @param string $value Строковое значение типа размещения (например, "single", "double").
     * @return string|null Локализованное описание или null, если значение не найдено.
     */
    public static function getDescriptionByString(string $value): ?string
    {
        $roomType = self::fromString($value);
        return $roomType?->translateValue();
    }
}
