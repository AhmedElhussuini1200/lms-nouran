<?php

namespace App\Repositories;

use App\Interfaces\AssignmentRepositoryInterface;
use App\Models\Assignment;

class AssignmentRepository extends BaseRepository implements AssignmentRepositoryInterface
{
    public function __construct(Assignment $model)
    {
        parent::__construct($model);
    }

    public function getByTeacher($teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByGrade($grade)
    {
        return $this->model->where('grade', $grade)
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function getPendingForStudent($studentId, $grade)
    {
        return $this->model->where('grade', $grade)
            ->where('due_date', '>=', now())
            ->whereDoesntHave('submissions', function($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function getRecent($limit = 5)
    {
        return $this->model->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

