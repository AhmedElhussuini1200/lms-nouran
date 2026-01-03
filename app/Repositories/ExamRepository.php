<?php

namespace App\Repositories;

use App\Interfaces\ExamRepositoryInterface;
use App\Models\Exam;

class ExamRepository extends BaseRepository implements ExamRepositoryInterface
{
    public function __construct(Exam $model)
    {
        parent::__construct($model);
    }

    public function getByTeacher($teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)
            ->orderBy('exam_date', 'desc')
            ->get();
    }

    public function getByGrade($grade)
    {
        return $this->model->where('grade', $grade)
            ->orderBy('exam_date', 'asc')
            ->get();
    }
}

