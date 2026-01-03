<?php

namespace App\Services;

use App\Interfaces\CourseRepositoryInterface;
use App\Interfaces\AssignmentRepositoryInterface;
use App\Interfaces\ExamRepositoryInterface;
use App\Interfaces\VideoRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\AssignmentSubmission;
use App\Models\ExamResult;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected $courseRepository;
    protected $assignmentRepository;
    protected $examRepository;
    protected $videoRepository;
    protected $userRepository;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        AssignmentRepositoryInterface $assignmentRepository,
        ExamRepositoryInterface $examRepository,
        VideoRepositoryInterface $videoRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->assignmentRepository = $assignmentRepository;
        $this->examRepository = $examRepository;
        $this->videoRepository = $videoRepository;
        $this->userRepository = $userRepository;
    }

    public function getTeacherDashboard()
    {
        $user = Auth::user();
        
        $stats = [
            'courses' => $this->courseRepository->getByTeacher($user->id)->count(),
            'assignments' => $this->assignmentRepository->getByTeacher($user->id)->count(),
            'exams' => $this->examRepository->getByTeacher($user->id)->count(),
            'videos' => $this->videoRepository->getByTeacher($user->id)->count(),
        ];

        $recentCourses = $this->courseRepository->getByTeacher($user->id)
            ->sortByDesc('scheduled_at')
            ->take(5);

        $recentAssignments = $this->assignmentRepository->getByTeacher($user->id)
            ->sortByDesc('created_at')
            ->take(5);

        return [
            'stats' => $stats,
            'recentCourses' => $recentCourses,
            'recentAssignments' => $recentAssignments,
        ];
    }

    public function getStudentDashboard()
    {
        $user = Auth::user();
        
        $stats = [
            'courses' => $this->courseRepository->getByGrade($user->grade)->count(),
            'assignments' => $this->assignmentRepository->getByGrade($user->grade)->count(),
            'exams' => $this->examRepository->getByGrade($user->grade)->count(),
            'videos' => $this->videoRepository->getByGrade($user->grade)->count(),
        ];

        $upcomingCourses = $this->courseRepository->getByGrade($user->grade)
            ->where('scheduled_at', '>=', now())
            ->sortBy('scheduled_at')
            ->take(5);

        $pendingAssignments = $this->assignmentRepository->getPendingForStudent($user->id, $user->grade)
            ->take(5);

        $recentVideos = $this->videoRepository->getByGrade($user->grade)
            ->sortByDesc('created_at')
            ->take(5);

        return [
            'stats' => $stats,
            'upcomingCourses' => $upcomingCourses,
            'pendingAssignments' => $pendingAssignments,
            'recentVideos' => $recentVideos,
        ];
    }

    public function getParentDashboard()
    {
        $user = Auth::user();
        $students = $this->userRepository->getStudentsByParent($user->id);
        
        $stats = [
            'students' => $students->count(),
            'total_assignments' => 0,
            'total_exams' => 0,
            'average_marks' => 0,
        ];

        $studentProgress = [];
        foreach ($students as $student) {
            $submissions = AssignmentSubmission::where('student_id', $student->id)->get();
            $examResults = ExamResult::where('student_id', $student->id)->get();
            
            $studentProgress[] = [
                'student' => $student,
                'assignments_completed' => $submissions->count(),
                'exams_taken' => $examResults->count(),
                'average_marks' => $examResults->avg('marks_obtained') ?? 0,
            ];
        }

        return [
            'stats' => $stats,
            'studentProgress' => $studentProgress,
        ];
    }
}

