<?php

namespace Naturalist\bronevik\enums;

enum StatusOrderEnum: int
{
    case NEW = 1;
    case IN_PROCESSING = 2;
    case AWAITING_CONFIRMATION = 3;
    case CONFIRMED = 4;
    case NOT_CONFIRMED = 5;
    case WAITING_FOR_CLIENT_CONFIRMATION = 6;
    case CANCELLATION_ORDERED = 7;
    case AWAITING_CANCELLATION = 8;
    case CANCELLED_WITHOUT_PENALTY = 9;
    case CANCELLED_FINE = 10;

    public function translateValue(): string
    {
        return match ($this) {
            StatusOrderEnum::NEW => 'Новый',
            StatusOrderEnum::IN_PROCESSING => 'В обработке',
            StatusOrderEnum::AWAITING_CONFIRMATION => 'Ожидает подтверждения',
            StatusOrderEnum::CONFIRMED => 'Подтвержден',
            StatusOrderEnum::NOT_CONFIRMED => 'Не подтвержден',
            StatusOrderEnum::WAITING_FOR_CLIENT_CONFIRMATION => 'Ожидает подтверждения клиента',
            StatusOrderEnum::CANCELLATION_ORDERED => 'Заказана аннуляция',
            StatusOrderEnum::AWAITING_CANCELLATION => 'Ожидает аннуляции',
            StatusOrderEnum::CANCELLED_WITHOUT_PENALTY => 'Аннулировано, без штрафа',
            StatusOrderEnum::CANCELLED_FINE => 'Аннулировано, штраф',
        };
    }

    public function siteStatusCode(): string
    {
        return match ($this) {
            StatusOrderEnum::NEW => 'N',//'Новый',
            StatusOrderEnum::IN_PROCESSING => 'N',//'В обработке',
            StatusOrderEnum::AWAITING_CONFIRMATION => 'N', //'Ожидает подтверждения',
            StatusOrderEnum::CONFIRMED => 'N', // 'Подтвержден',
            StatusOrderEnum::NOT_CONFIRMED => 'C', //'Не подтвержден',
            StatusOrderEnum::WAITING_FOR_CLIENT_CONFIRMATION => 'N', // 'Ожидает подтверждения клиента',
            StatusOrderEnum::CANCELLATION_ORDERED => 'C', // 'Заказана аннуляция',
            StatusOrderEnum::AWAITING_CANCELLATION => 'N', // 'Ожидает аннуляции',
            StatusOrderEnum::CANCELLED_WITHOUT_PENALTY => 'C', //'Аннулировано, без штрафа',
            StatusOrderEnum::CANCELLED_FINE => 'C', //'Аннулировано, штраф',
        };
    }
}
