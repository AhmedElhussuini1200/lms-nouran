<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'description',
        'grade',
        'teacher_id',
        'exam_date',
        'duration_minutes',
        'total_marks',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'datetime',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}
