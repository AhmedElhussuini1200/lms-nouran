<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'attempt_no', 'started_at', 'submitted_at', 'tab_switches',
    ];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'submitted_at' => 'datetime'];
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Admin::class, 'student_id');
    }
}
