<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['admin_id', 'balance'];

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public static function for(Admin $admin): self
    {
        return static::firstOrCreate(['admin_id' => $admin->id], ['balance' => 0]);
    }

    public function credit(float $amount, ?string $reason = null): void
    {
        $this->increment('balance', $amount);
        $this->transactions()->create(['kind' => 'credit', 'amount' => $amount, 'reason' => $reason]);
    }

    public function debit(float $amount, ?string $reason = null): bool
    {
        if ((float) $this->balance < $amount) {
            return false;
        }
        $this->decrement('balance', $amount);
        $this->transactions()->create(['kind' => 'debit', 'amount' => $amount, 'reason' => $reason]);

        return true;
    }
}
