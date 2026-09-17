<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    protected $fillable = ['admin_id', 'kind', 'prompt', 'response'];
}
