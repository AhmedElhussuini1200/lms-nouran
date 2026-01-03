<?php

namespace App\Services;

use App\Interfaces\CourseRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getAllCourses($paginate = 15)
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            $courses = $this->courseRepository->getByTeacher($user->id);
        } else {
            $courses = $this->courseRepository->getByGrade($user->grade);
        }
        
        // Convert collection to paginated results
        $page = request()->get('page', 1);
        $perPage = $paginate;
        $items = $courses->forPage($page, $perPage);
        $total = $courses->count();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getCourseById($id)
    {
        return $this->courseRepository->find($id);
    }

    public function createCourse(array $data)
    {
        $data['teacher_id'] = Auth::id();
        return $this->courseRepository->create($data);
    }

    public function updateCourse($id, array $data)
    {
        return $this->courseRepository->update($id, $data);
    }

    public function deleteCourse($id)
    {
        return $this->courseRepository->delete($id);
    }

    public function getRecentCourses($limit = 5)
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            return $this->courseRepository->getByTeacher($user->id)
                ->sortByDesc('scheduled_at')
                ->take($limit);
        }
        
        return $this->courseRepository->getByGrade($user->grade)
            ->sortByDesc('scheduled_at')
            ->take($limit);
    }
}

