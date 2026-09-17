<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['student_id', 'exam_id', 'course_id', 'code', 'pdf_path', 'score'];

    public function student()
    {
        return $this->belongsTo(Admin::class, 'student_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
