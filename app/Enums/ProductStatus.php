<?php

namespace App\Enums;

enum ProductStatus: int
{
    case Visible = 1;
    case Invisible  = 2;

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
