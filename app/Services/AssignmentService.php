<?php

namespace App\Services;

use App\Interfaces\AssignmentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AssignmentService
{
    protected $assignmentRepository;

    public function __construct(AssignmentRepositoryInterface $assignmentRepository)
    {
        $this->assignmentRepository = $assignmentRepository;
    }

    public function getAllAssignments($paginate = 15)
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            $assignments = $this->assignmentRepository->getByTeacher($user->id);
        } else {
            $assignments = $this->assignmentRepository->getByGrade($user->grade);
        }
        
        // Convert collection to paginated results
        $page = request()->get('page', 1);
        $perPage = $paginate;
        $items = $assignments->forPage($page, $perPage);
        $total = $assignments->count();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getAssignmentById($id)
    {
        return $this->assignmentRepository->find($id);
    }

    public function createAssignment(array $data)
    {
        $data['teacher_id'] = Auth::id();
        return $this->assignmentRepository->create($data);
    }

    public function updateAssignment($id, array $data)
    {
        return $this->assignmentRepository->update($id, $data);
    }

    public function deleteAssignment($id)
    {
        return $this->assignmentRepository->delete($id);
    }

    public function getPendingAssignmentsForStudent($studentId, $grade)
    {
        return $this->assignmentRepository->getPendingForStudent($studentId, $grade);
    }

    public function getRecentAssignments($limit = 5)
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            return $this->assignmentRepository->getByTeacher($user->id)
                ->sortByDesc('created_at')
                ->take($limit);
        }
        
        return $this->assignmentRepository->getByGrade($user->grade)
            ->sortBy('due_date')
            ->take($limit);
    }
}

