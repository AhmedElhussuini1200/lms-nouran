<?php

namespace App\Repositories;

use App\Interfaces\CourseRepositoryInterface;
use App\Models\Course;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    public function getByTeacher($teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    public function getByGrade($grade)
    {
        return $this->model->where('grade', $grade)
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    public function getRecent($limit = 5)
    {
        return $this->model->orderBy('scheduled_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

