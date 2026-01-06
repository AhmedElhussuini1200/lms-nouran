<?php

namespace App\Enums;

enum RefundStatus: int
{
    case Pending = 1;
    case Refunded  = 2;
    case Denied  = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
