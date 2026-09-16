<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'exam_id', 'type', 'question', 'options', 'correct_answer', 'marks', 'sort',
    ];

    protected function casts(): array
    {
        return ['options' => 'array', 'marks' => 'decimal:2'];
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function isAutoGradable(): bool
    {
        return in_array($this->type, ['mcq', 'true_false']) && $this->correct_answer !== null;
    }
}
