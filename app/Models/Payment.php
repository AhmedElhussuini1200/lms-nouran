<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id', 'month', 'amount', 'paid_amount',
        'status_id', 'method', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2'];
    }

    public function student()
    {
        return $this->belongsTo(Admin::class, 'student_id');
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
