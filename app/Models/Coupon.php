<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'kind', 'value', 'max_uses', 'used_count', 'expires_at', 'active', 'created_by'];

    protected function casts(): array
    {
        return ['expires_at' => 'date', 'active' => 'boolean'];
    }

    public function isValid(): bool
    {
        if (! $this->active) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    // قيمة الخصم على مبلغ معين
    public function discountFor(float $amount): float
    {
        if (! $this->isValid()) {
            return 0;
        }

        return $this->kind === 'percent'
            ? round($amount * ((float) $this->value / 100), 2)
            : min($amount, (float) $this->value);
    }
}
