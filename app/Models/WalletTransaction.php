<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['wallet_id', 'kind', 'amount', 'reason'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
