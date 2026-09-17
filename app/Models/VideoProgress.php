<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    protected $fillable = ['video_id', 'student_id', 'watched_seconds', 'percent', 'completed'];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function student()
    {
        return $this->belongsTo(Admin::class, 'student_id');
    }
}
