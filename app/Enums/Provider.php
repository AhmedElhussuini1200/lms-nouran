<?php

namespace App\Enums;

enum Provider: int
{
    case Application = 1;
    case Google = 2;
    case Apple = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
