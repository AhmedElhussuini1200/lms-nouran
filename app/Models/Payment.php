<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id', 'paid_by', 'payer_name', 'month', 'amount', 'paid_amount',
        'status_id', 'method', 'notes', 'created_by',
        'receipt_image', 'receipt_verified', 'verified_by', 'unverified_amount',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'receipt_verified' => 'boolean'];
    }

    public function student()
    {
        return $this->belongsTo(Admin::class, 'student_id');
    }

    // اللي دفع فعلاً (قد يكون ولي الأمر) — الفاتورة تفضل باسم الطالب
    public function payer()
    {
        return $this->belongsTo(Admin::class, 'paid_by');
    }

    public function getPayerLabelAttribute(): string
    {
        if ($this->payer) {
            return $this->payer->name . ($this->payer->type === 'parent' ? ' (' . __('ولي أمر') . ')' : '');
        }

        return $this->payer_name ?: '—';
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getRemainingAttribute()
    {
        return (float) $this->amount - (float) $this->paid_amount;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->remaining <= 0 && (float) $this->amount > 0;
    }
}
