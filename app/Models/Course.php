<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'grade',
        'teacher_id',
        'subject',
        'scheduled_at',
        'price',
        'qr_token',
        'is_live',
        'live_url',
        'live_started_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'live_started_at' => 'datetime',
            'is_live' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(Admin::class, 'teacher_id');
    }

    public function liveMessages()
    {
        return $this->hasMany(LiveMessage::class, 'course_id')->latest()->limit(50);
    }

    // واجب الحصة + امتحان الحصة
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'course_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'course_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'course_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
