<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'grade',
        'teacher_id',
        'subject',
        'video_url',
        'thumbnail_url',
        'duration_seconds',
        'views_count',
    ];

    public function teacher()
    {
        return $this->belongsTo(Admin::class, 'teacher_id');
    }
}
