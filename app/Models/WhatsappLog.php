<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $fillable = [
        'admin_id', 'phone', 'message', 'provider', 'status', 'error', 'sent_by',
    ];

    public function recipient()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function sender()
    {
        return $this->belongsTo(Admin::class, 'sent_by');
    }
}
