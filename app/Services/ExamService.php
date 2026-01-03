<?php

namespace App\Services;

use App\Interfaces\ExamRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ExamService
{
    protected $examRepository;

    public function __construct(ExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function getAllExams($paginate = 15)
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            $exams = $this->examRepository->getByTeacher($user->id);
        } else {
            $exams = $this->examRepository->getByGrade($user->grade);
        }
        
        // Convert collection to paginated results
        $page = request()->get('page', 1);
        $perPage = $paginate;
        $items = $exams->forPage($page, $perPage);
        $total = $exams->count();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getExamById($id)
    {
        return $this->examRepository->find($id);
    }

    public function createExam(array $data)
    {
        $data['teacher_id'] = Auth::id();
        return $this->examRepository->create($data);
    }

    public function updateExam($id, array $data)
    {
        return $this->examRepository->update($id, $data);
    }

    public function deleteExam($id)
    {
        return $this->examRepository->delete($id);
    }
}

