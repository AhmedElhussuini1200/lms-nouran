<?php

namespace App\Models;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'attempt_no',
        'status_id',
        'marks_obtained',
        'answers',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'answers' => 'array',
        ];
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Admin::class, 'student_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
