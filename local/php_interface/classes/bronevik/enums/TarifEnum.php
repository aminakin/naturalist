<?php

namespace Naturalist\bronevik\enums;

enum TarifEnum: string
{

    case RefundableRate = 'RefundableRate';
    case NonrefundableRate = 'NonrefundableRate';
    case NonRefundableSpecialBronevikRate = 'NonRefundableSpecialBronevikRate';
    case LHP = 'LHP';
    case NonRefundableLongStaySpecialBronevikRate = 'NonRefundableLongStaySpecialBronevikRate';
    case StandardRate = 'StandardRate';
    case NetRate = 'NetRate';
    case MinStayRate = 'MinStayRate';
    case LongStayRate = 'LongStayRate';
    case EarlyBirdRate = 'EarlyBirdRate';
    case BedBreakfastRate = 'Bed&BreakfastRate';
    case HalfBoardRate = 'HalfBoardRate';
    case FullBoardRate = 'FullBoardRate';
    case SpecialHotelRate = 'SpecialHotelRate';
    case AllInclusiveRate = 'AllInclusiveRate';
    case UltraAllInclusiveRate = 'UltraAllInclusiveRate';
    case PublicNetRate = 'PublicNetRate';

    /**
     * Ассоциативный массив для хранения локализованных значений.
     */
    private const LOCALIZED_VALUES = [
        'RefundableRate' => 'Возвратный тариф',
        'NonrefundableRate' => 'Невозвратный тариф',
        'NonRefundableSpecialBronevikRate' => 'Невозвратный специальный тариф Bronevik.com',
        'LHP' => 'Локальная отельная программа',
        'NonRefundableLongStaySpecialBronevikRate' => 'Невозвратный специальный тариф длительного проживания Bronevik.com',
        'StandardRate' => 'Стандартный тариф',
        'NetRate' => 'Нетто тариф',
        'MinStayRate' => 'Минимальное количество ночей',
        'LongStayRate' => 'Тариф длительного проживания',
        'EarlyBirdRate' => 'Тариф раннего бронирования',
        'Bed&BreakfastRate' => 'Тариф с завтраком', // Используем исходное значение с &
        'HalfBoardRate' => 'Тариф с полупансионом',
        'FullBoardRate' => 'Тариф с пансионом',
        'SpecialHotelRate' => 'Специальный отельный тариф',
        'AllInclusiveRate' => 'Тариф все включено',
        'UltraAllInclusiveRate' => 'Тариф ультра все включено',
        'PublicNetRate' => 'Открытый нетто тариф, специальный тариф для отелей СНГ',
    ];

    /**
     * Возвращает локализованное значение текущего варианта перечисления.
     *
     * @return string Локализованное значение.
     */
    public function value(): string
    {
        return self::LOCALIZED_VALUES[$this->value] ?? throw new \Exception("Localized value not found for {$this->value}");
    }

    /**
     * Возвращает локализованное значение по строке.
     *
     * @param string $name Название тарифа.
     * @return string|null Локализованное значение или null, если тариф не найден.
     * @throws \Exception
     */
    public static function getLocalizedValue(string $name): ?string
    {
        // Удаляем символ & из строки перед поиском
        $cleanName = preg_replace('/&/', '', $name);
        $case = self::tryFrom($cleanName);

        return $case?->value();
    }

}
