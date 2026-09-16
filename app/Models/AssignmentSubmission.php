<?php

namespace App\Models;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'status_id',
        'submission_text',
        'file_path',
        'marks',
        'teacher_feedback',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
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
