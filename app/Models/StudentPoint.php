<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPoint extends Model
{
    protected $fillable = ['student_id', 'points', 'reason', 'source_type', 'source_id'];

    public function source()
    {
        return $this->morphTo();
    }

    public function student()
    {
        return $this->belongsTo(Admin::class, 'student_id');
    }
}
