<?php

namespace App\Services\Dashboard;

use App\Models\Video;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Admin;
use App\Models\Assignment;
use Carbon\Carbon;
use App\Repositories\Dashboard\Contracts\DashboardRepositoryInterface;
use App\Services\InsightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected $dashboardRepository;
    protected $insights;

    public function __construct(DashboardRepositoryInterface $dashboardRepository, InsightService $insights)
    {
        $this->dashboardRepository = $dashboardRepository;
        $this->insights = $insights;
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
                $insight = $this->insights->profile($user->id);
                return view('dashboard.student', [
                    'stats' => [
                        'courses' => Course::where('grade', $grade)->count(),
                        'assignments' => Assignment::where('grade', $grade)->count(),
                        'exams' => Exam::where('grade', $grade)->count(),
                        'videos' => Video::where('grade', $grade)->count(),
                        'points' => $insight['points'],
                        'streak' => \App\Models\AssignmentSubmission::where('student_id', $user->id)
                            ->where('submitted_at', '>=', now()->subDays(30))
                            ->distinct()->count(\Illuminate\Support\Facades\DB::raw('DATE(submitted_at)')),
                    ],
                    'insight' => $insight,
                    'upcomingCourses' => Course::where('grade', $grade)->where('scheduled_at', '>=', now())->orderBy('scheduled_at')->limit(5)->get(),
                    'pendingAssignments' => Assignment::where('grade', $grade)->whereNotIn('id', $submittedAssignmentIds)->orderBy('due_date')->limit(5)->get(),
                    'overdueAssignments' => Assignment::where('grade', $grade)->whereNotIn('id', $submittedAssignmentIds)->where('due_date', '<', now())->count(),
                    'upcomingExams' => Exam::where('grade', $grade)->where('exam_date', '>=', now())->orderBy('exam_date')->limit(3)->get(),
                    'recentVideos' => Video::where('grade', $grade)->latest()->limit(6)->get(),
                    'myPayments' => \App\Models\Payment::where('student_id', $user->id)->orderBy('month', 'desc')->limit(3)->get(),
                    'myCertificates' => \App\Models\Certificate::where('student_id', $user->id)->latest()->limit(3)->get(),
                ]);
            case 'parent':
                $children = $user->students()->get();
                $childIds = $children->pluck('id');
                $profiles = [];
                foreach ($children as $child) {
                    $profiles[] = $this->insights->profile($child->id);
                }
                return view('dashboard.parent', [
                    'stats' => [
                        'students' => $children->count(),
                        'due' => (float) \App\Models\Payment::whereIn('student_id', $childIds)->get()->sum(fn ($p) => $p->remaining),
                        'avg' => count($profiles) ? round(collect($profiles)->avg(fn ($p) => $p['avg'] ?? 0), 1) : 0,
                        'alerts' => collect($profiles)->where('risk', 'high')->count(),
                    ],
                    'children' => $children,
                    'profiles' => $profiles,
                    'invoices' => \App\Models\Payment::with(['student:id,name', 'payer:id,name,type'])->whereIn('student_id', $childIds)->orderBy('month', 'desc')->limit(10)->get(),
                ]);
            default:
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login-form');
        }
    }
}