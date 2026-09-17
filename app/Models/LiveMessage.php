<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveMessage extends Model
{
    protected $fillable = ['course_id', 'admin_id', 'message'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function author()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
