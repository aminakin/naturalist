<?php

namespace Naturalist\bronevik\enums;

use Bronevik\HotelsConnector\Enum\RateTypeNames as RateTypeNamesBase;
enum RateTypeNamesEnum: string// extends RateTypeNamesBase
{
    case REFUNDABLE_RATE = RateTypeNamesBase::REFUNDABLE_RATE;
    case NON_REFUNDABLE_RATE = RateTypeNamesBase::NON_REFUNDABLE_RATE;
    case NON_REFUNDABLE_RATE_SPECIAL_BRONEVIK_RATE = RateTypeNamesBase::NON_REFUNDABLE_RATE_SPECIAL_BRONEVIK_RATE;
    case LHP = RateTypeNamesBase::LHP;
    case NON_REFUNDABLE_LONG_STAY_SPECIAL_BRONEVIK_RATE = 'NonRefundableLongStaySpecialBronevikRate';
    case STANDARD_RATE = 'StandardRate';
    case NET_RATE = 'NetRate';
    case MIN_STAY_RATE = 'MinStayRate';
    case LONG_STAY_RATE = 'LongStayRate';
    case EARLY_BIRD_RATE = 'EarlyBirdRate';
    case BED_AND_BREAKFAST_RATE = 'Bed&BreakfastRate';
    case HALF_BOARD_RATE = 'HalfBoardRate';
    case FULL_BOARD_RATE = 'FullBoardRate';
    case SPECIAL_HOTEL_RATE = 'SpecialHotelRate';
    case ALL_INCLUSIVE_RATE = 'AllInclusiveRate';
    case ULTRA_ALL_INCLUSIVE_RATE = 'UltraAllInclusiveRate';
    case PUBLIC_NET_RATE = 'PublicNetRate';

    public function translateValue(): string
    {
        return match($this) {
            RateTypeNamesEnum::REFUNDABLE_RATE => 'Возвратный тариф',
            RateTypeNamesEnum::NON_REFUNDABLE_RATE => 'Не возвратный тариф',
            RateTypeNamesEnum::NON_REFUNDABLE_RATE_SPECIAL_BRONEVIK_RATE => 'Невозвратный специальный тариф Bronevik.com',
            RateTypeNamesEnum::LHP => 'Локальная отдельная программа',
            RateTypeNamesEnum::NON_REFUNDABLE_LONG_STAY_SPECIAL_BRONEVIK_RATE => 'Невозвратный специальный тариф длительного проживания Bronevik.com',
            RateTypeNamesEnum::STANDARD_RATE => 'Стандартный тариф',
            RateTypeNamesEnum::NET_RATE => 'Нетто тариф',
            RateTypeNamesEnum::MIN_STAY_RATE => 'Минимальное количество ночей',
            RateTypeNamesEnum::LONG_STAY_RATE => 'Тариф длительного проживания',
            RateTypeNamesEnum::EARLY_BIRD_RATE => 'Тариф раннего бронирования',
            RateTypeNamesEnum::BED_AND_BREAKFAST_RATE => 'Тариф с завтраком',
            RateTypeNamesEnum::HALF_BOARD_RATE => 'Тариф с полупансионом',
            RateTypeNamesEnum::FULL_BOARD_RATE => 'Тариф с пансионом',
            RateTypeNamesEnum::SPECIAL_HOTEL_RATE => 'Специальный отельный тариф',
            RateTypeNamesEnum::ALL_INCLUSIVE_RATE => 'Тариф все включено',
            RateTypeNamesEnum::ULTRA_ALL_INCLUSIVE_RATE => 'Тариф ультра все включено',
            RateTypeNamesEnum::PUBLIC_NET_RATE => 'Открытый нетто тариф, специальный тариф для отелей СНГ',
            default => throw new \Exception('Unexpected match value'),
        };
    }
}