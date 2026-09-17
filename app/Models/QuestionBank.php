<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $table = 'question_bank';

    protected $fillable = [
        'teacher_id', 'subject', 'grade', 'type', 'difficulty', 'topic',
        'question', 'options', 'correct_answer', 'marks',
    ];

    protected function casts(): array
    {
        return ['options' => 'array', 'marks' => 'decimal:2'];
    }

    public function teacher()
    {
        return $this->belongsTo(Admin::class, 'teacher_id');
    }
}
