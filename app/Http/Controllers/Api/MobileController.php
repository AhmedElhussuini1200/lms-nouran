<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\Payment;
use App\Models\StudentPoint;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        $user = Admin::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }
        if ($user->is_blocked) {
            return response()->json(['message' => 'تم إيقاف حسابك'], 403);
        }

        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
            'user' => $user->only(['id', 'name', 'email', 'type', 'grade', 'current_teacher_id']),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->only([
            'id', 'name', 'email', 'type', 'grade', 'subject', 'phone', 'current_teacher_id',
        ]));
    }

    // مدرسين الطالب + اختيار الحالي (كارت الدخول للموبايل)
    public function myTeachers(Request $request)
    {
        $u = $request->user();
        abort_unless($u->type === 'student', 403);

        return response()->json([
            'teachers' => $u->enrolledTeachers()->get(['admins.id', 'name', 'subject', 'brand_name', 'brand_primary'])->map(fn ($t) => [
                'id' => $t->id, 'name' => $t->brand_name ?? $t->name, 'subject' => $t->subject,
                'color' => $t->brand_primary,
            ]),
            'current_teacher_id' => $u->current_teacher_id,
        ]);
    }

    public function selectTeacher(Request $request)
    {
        $u = $request->user();
        abort_unless($u->type === 'student', 403);
        $request->validate(['teacher_id' => ['required', 'exists:admins,id']]);
        abort_unless($u->enrolledTeachers()->where('admins.id', $request->teacher_id)->exists(), 403);
        $u->update(['current_teacher_id' => $request->teacher_id]);

        return response()->json(['current_teacher_id' => (int) $request->teacher_id]);
    }

    protected function teacherScope(Request $request, $query)
    {
        $u = $request->user();
        if ($u->type === 'student') {
            $query->where('grade', $u->grade);
            $tid = $request->get('teacher_id', $u->current_teacher_id);
            if ($tid) {
                $query->where('teacher_id', $tid);
            }
        }

        return $query;
    }

    public function courses(Request $request)
    {
        $q = $this->teacherScope($request, Course::with('teacher:id,name,brand_name')->latest());

        return \App\Http\Resources\Api\CourseResource::collection($q->paginate(20));
    }

    public function videos(Request $request)
    {
        $q = $this->teacherScope($request, Video::with('teacher:id,name,brand_name')->latest());

        return \App\Http\Resources\Api\VideoResource::collection($q->paginate(20));
    }

    public function exams(Request $request)
    {
        $q = $this->teacherScope($request, Exam::with('questions')->latest());

        return \App\Http\Resources\Api\ExamResource::collection($q->paginate(20));
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $u = $request->user();
        abort_unless($u->type === 'student', 403);
        if (! $exam->canAttempt($u->id)) {
            return response()->json(['message' => 'غير متاح'], 422);
        }
        $request->validate(['answers' => ['nullable', 'array'], 'tab_switches' => ['nullable', 'integer', 'min:0']]);
        $exam->loadMissing('questions');
        $marks = 0;
        $hasEssay = false;
        foreach ($exam->questions as $qq) {
            if ($qq->type === 'essay') {
                $hasEssay = true;
                continue;
            }
            $given = $request->answers[$qq->id] ?? null;
            if ($qq->isAutoGradable() && $given !== null && trim((string) $given) === trim((string) $qq->correct_answer)) {
                $marks += (float) $qq->marks;
            }
        }
        // قيد المحاولة في نفس جدول الويب (منع تجاوز العدد من الموبايل)
        $maxNo = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $u->id)->max('attempt_no') ?: 0;
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id, 'student_id' => $u->id, 'attempt_no' => $maxNo + 1,
            'started_at' => now(), 'submitted_at' => now(),
            'tab_switches' => (int) ($request->tab_switches ?? 0),
        ]);
        $result = ExamResult::create([
            'exam_id' => $exam->id, 'student_id' => $u->id, 'attempt_no' => $attempt->attempt_no,
            'answers' => $request->answers ?? [],
            'marks_obtained' => $marks,
            'status_id' => \App\Models\Status::idFor($hasEssay ? 'under_review' : 'graded'),
            'submitted_at' => now(),
        ]);

        return response()->json(['marks' => $marks, 'result_id' => $result->id, 'attempt_no' => $attempt->attempt_no]);
    }

    public function payments(Request $request)
    {
        $u = $request->user();
        $q = Payment::with(['student:id,name', 'payer:id,name'])->latest();
        if ($u->type === 'student') {
            $q->where('student_id', $u->id);
        }
        if ($u->type === 'parent') {
            $q->whereIn('student_id', $u->students()->pluck('admins.id'));
        }

        return \App\Http\Resources\Api\PaymentResource::collection($q->paginate(20));
    }

    public function leaderboard(Request $request)
    {
        $grade = $request->get('grade', $request->user()->grade ?? '3_secondary');
        $board = Admin::where('type', 'student')->where('grade', $grade)->get()->map(function ($s) {
            $s->points = (int) StudentPoint::where('student_id', $s->id)->sum('points');
            $s->avg = round((float) (\App\Models\ExamResult::where('student_id', $s->id)->selectRaw('exam_id, MAX(marks_obtained) as best')->groupBy('exam_id')->get()->avg('best') ?? 0), 1);
            return $s->only(['id', 'name', 'points', 'avg']);
        })->sortByDesc('points')->values()->take(20);

        return response()->json($board);
    }

    // قائمة روابط التحميل الأوفلاين (فيديوهات + ملازم)
    public function offlineManifest(Request $request)
    {
        $u = $request->user();
        $videos = Video::when($u->type === 'student', fn ($q) => $q->where('grade', $u->grade))
            ->when($u->current_teacher_id, fn ($q) => $q->where('teacher_id', $u->current_teacher_id))
            ->latest()->limit(20)->get(['id', 'title', 'video_url', 'duration_seconds']);

        return response()->json(['videos' => $videos, 'generated_at' => now()]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
