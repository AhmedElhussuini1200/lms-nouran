<?php

namespace App\Enums;

enum ReportStatus: int
{
    case Approved = 1;
    case Rejected  = 2;

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
    public function label(): string
    {
        return match ($this) {
            self::Approved => __('Approved'),
            self::Rejected => __('Rejected'),
        };
    }
    public static function getLabel(int|string|null $value): ?string
    {
        if ($value === null) return null;

        $case = is_int($value)
            ? self::tryFrom((int) $value)
            : (self::tryFrom((int) $value) ?? self::tryFrom(constant("self::$value") ?? null));

        return $case?->label();
    }
}
