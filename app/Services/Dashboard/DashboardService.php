<?php

namespace App\Services\Dashboard;

use App\Models\Video;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Admin;
use App\Models\Assignment;
use Carbon\Carbon;
use App\Repositories\Dashboard\Contracts\DashboardRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }


    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if (! $user) {
            return redirect()->route('admin.login-form');
        }

        // توجيه حسب نوع المستخدم إلى الـ views اللي عندك في resources/views/dashboard
        switch ($user->type) {
            case 'admin':
                $grades = ['1_secondary', '2_secondary', '3_secondary'];
                $contentByGrade = [];
                foreach ($grades as $g) {
                    $contentByGrade[$g] = [
                        'courses' => Course::where('grade', $g)->count(),
                        'videos' => Video::where('grade', $g)->count(),
                        'assignments' => Assignment::where('grade', $g)->count(),
                        'exams' => Exam::where('grade', $g)->count(),
                        'students' => Admin::where('type', 'student')->where('grade', $g)->count(),
                    ];
                }
                $months = [];
                for ($i = 5; $i >= 0; $i--) {
                    $m = now()->subMonths($i)->format('Y-m');
                    $months[$m] = [
                        'total' => (float) \App\Models\Payment::where('month', $m)->sum('amount'),
                        'paid' => (float) \App\Models\Payment::where('month', $m)->sum('paid_amount'),
                    ];
                }
                return view('dashboard.admin.index', [
                    'stats' => [
                        'teachers' => Admin::where('type', 'teacher')->count(),
                        'students' => Admin::where('type', 'student')->count(),
                        'parents' => Admin::where('type', 'parent')->count(),
                        'courses' => Course::count(),
                        'videos' => Video::count(),
                        'assignments' => Assignment::count(),
                        'exams' => Exam::count(),
                        'pending_reviews' => \App\Models\AssignmentSubmission::whereHas('status', fn ($q) => $q->whereIn('slug', ['submitted', 'under_review']))->count(),
                        'collected' => (float) \App\Models\Payment::sum('paid_amount'),
                        'receivable' => (float) \App\Models\Payment::sum('amount') - (float) \App\Models\Payment::sum('paid_amount'),
                    ],
                    'recentVideos' => Video::with('teacher:id,name')->latest()->limit(5)->get(),
                    'recentCourses' => Course::with('teacher:id,name')->latest()->limit(5)->get(),
                    'contentByGrade' => $contentByGrade,
                    'months' => $months,
                    'submissionStates' => [
                        'submitted' => \App\Models\AssignmentSubmission::whereHas('status', fn ($q) => $q->where('slug', 'submitted'))->count(),
                        'under_review' => \App\Models\AssignmentSubmission::whereHas('status', fn ($q) => $q->where('slug', 'under_review'))->count(),
                        'graded' => \App\Models\AssignmentSubmission::whereHas('status', fn ($q) => $q->where('slug', 'graded'))->count(),
                        'returned' => \App\Models\AssignmentSubmission::whereHas('status', fn ($q) => $q->where('slug', 'returned'))->count(),
                    ],
                    'topTeachers' => Admin::where('type', 'teacher')
                        ->withCount(['courses', 'videos', 'assignments', 'exams'])
                        ->orderByDesc('courses_count')->limit(5)->get(),
                    'pendingReviews' => \App\Models\AssignmentSubmission::with(['assignment:id,title', 'student:id,name', 'status'])
                        ->whereHas('status', fn ($q) => $q->whereIn('slug', ['submitted', 'under_review']))
                        ->latest()->limit(6)->get(),
                    'overduePayments' => \App\Models\Payment::with(['student:id,name', 'status'])
                        ->whereHas('status', fn ($q) => $q->whereIn('slug', ['pending', 'partial']))
                        ->orderBy('month')->limit(6)->get(),
                    'leaderboard' => Admin::where('type', 'student')
                        ->withCount(['assignmentSubmissions', 'examResults'])
                        ->get()
                        ->map(fn ($s) => ['student' => $s, 'points' => $s->assignment_submissions_count * 10 + $s->exam_results_count * 20])
                        ->sortByDesc('points')->take(5)->values(),
                ]);
            case 'teacher':
                return view('dashboard.teacher.teacher', [
                    'stats' => [
                        'courses' => Course::where('teacher_id', $user->id)->count(),
                        'assignments' => Assignment::where('teacher_id', $user->id)->count(),
                        'exams' => Exam::where('teacher_id', $user->id)->count(),
                        'videos' => Video::where('teacher_id', $user->id)->count(),
                    ],
                    'recentCourses' => Course::where('teacher_id', $user->id)->latest()->limit(5)->get(),
                    'recentAssignments' => Assignment::where('teacher_id', $user->id)->latest()->limit(5)->get(),
                    'recentVideos' => Video::where('teacher_id', $user->id)->latest()->limit(5)->get(),
                ]);
            case 'student':
                $grade = $user->grade;
                $submittedAssignmentIds = \App\Models\AssignmentSubmission::where('student_id', $user->id)->pluck('assignment_id');
                // نقاط التميز: 10 لكل تسليم واجب + 20 لكل امتحان مسلم + مجموع المشاهدات كنقاط تشجيعية
                $submissionsCount = \App\Models\AssignmentSubmission::where('student_id', $user->id)->count();
                $examsTaken = \App\Models\ExamResult::where('student_id', $user->id)->count();
                // أيام الالتزام: عدد الأيام المميزة اللي سلم فيها حاجة آخر 30 يوم
                $streak = \App\Models\AssignmentSubmission::where('student_id', $user->id)
                    ->where('submitted_at', '>=', now()->subDays(30))
                    ->distinct()->count(\Illuminate\Support\Facades\DB::raw('DATE(submitted_at)'));
                return view('dashboard.student', [
                    'stats' => [
                        'courses' => Course::where('grade', $grade)->count(),
                        'assignments' => Assignment::where('grade', $grade)->count(),
                        'exams' => Exam::where('grade', $grade)->count(),
                        'videos' => Video::where('grade', $grade)->count(),
                        'points' => $submissionsCount * 10 + $examsTaken * 20,
                        'streak' => $streak,
                    ],
                    'upcomingCourses' => Course::where('grade', $grade)->where('scheduled_at', '>=', now())->orderBy('scheduled_at')->limit(5)->get(),
                    'pendingAssignments' => Assignment::where('grade', $grade)->whereNotIn('id', $submittedAssignmentIds)->orderBy('due_date')->limit(5)->get(),
                    'overdueAssignments' => Assignment::where('grade', $grade)->whereNotIn('id', $submittedAssignmentIds)->where('due_date', '<', now())->count(),
                    'upcomingExams' => Exam::where('grade', $grade)->where('exam_date', '>=', now())->orderBy('exam_date')->limit(3)->get(),
                    'recentVideos' => Video::where('grade', $grade)->latest()->limit(6)->get(),
                ]);
            case 'parent':
                $children = $user->students()->with([])->get();
                $childIds = $children->pluck('id');
                $grades = $children->pluck('grade')->filter()->unique()->values();
                $progress = [];
                foreach ($children as $child) {
                    $avg = \App\Models\ExamResult::where('student_id', $child->id)->avg('marks_obtained');
                    $doneIds = \App\Models\AssignmentSubmission::where('student_id', $child->id)->pluck('assignment_id');
                    $missing = Assignment::where('grade', $child->grade)->whereNotIn('id', $doneIds)->orderBy('due_date')->limit(5)->get();
                    $progress[] = ['student' => $child, 'average' => $avg ? round($avg, 1) : null, 'missing' => $missing];
                }
                return view('dashboard.parent', [
                    'stats' => [
                        'students' => $children->count(),
                        'total_assignments' => Assignment::whereIn('grade', $grades)->count(),
                        'total_exams' => Exam::whereIn('grade', $grades)->count(),
                        'average_marks' => round(\App\Models\ExamResult::whereIn('student_id', $childIds)->avg('marks_obtained') ?? 0, 1),
                    ],
                    'children' => $children,
                    'studentProgress' => $progress,
                ]);
            default:
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login-form');
        }
    }
}