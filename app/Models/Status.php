<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['slug', 'name_ar', 'name_en', 'color', 'scope'];

    public const SUBMITTED = 'submitted';
    public const UNDER_REVIEW = 'under_review';
    public const GRADED = 'graded';
    public const RETURNED = 'returned';
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const PARTIAL = 'partial';
    public const OVERDUE = 'overdue';

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function idFor(string $slug): ?int
    {
        return static::where('slug', $slug)->value('id');
    }
}
