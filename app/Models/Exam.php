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
        'subject',
        'exam_date',
        'duration_minutes',
        'total_marks',
        'shuffle_questions',
        'max_attempts',
        'passing_marks',
        'starts_at',
        'ends_at',
        'anti_cheat',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'shuffle_questions' => 'boolean',
            'anti_cheat' => 'boolean',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(Admin::class, 'teacher_id');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('sort')->orderBy('id');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function isOpen(): bool
    {
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function attemptsUsed(int $studentId): int
    {
        return $this->attempts()->where('student_id', $studentId)->whereNotNull('submitted_at')->count();
    }

    public function canAttempt(int $studentId): bool
    {
        return $this->isOpen() && $this->attemptsUsed($studentId) < ($this->max_attempts ?: 1);
    }
}
