<?php

namespace Naturalist\bronevik\enums;

enum PaymentRecipientEnum: string
{
    case AGENCY = 'agency';
    case HOTEL = 'hotel';

    public function translateValue(): string
    {
        return match($this) {
            self::AGENCY => 'На сайте',
            self::HOTEL => 'В отеле',
        };
    }
}
