<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Exam;
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

        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
            'user' => $user->only(['id', 'name', 'email', 'type', 'grade']),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function courses(Request $request)
    {
        $u = $request->user();
        $q = Course::with('teacher:id,name')->latest();
        if ($u->type === 'student') {
            $q->where('grade', $u->grade);
        }

        return \App\Http\Resources\Api\CourseResource::collection($q->paginate(20));
    }

    public function videos(Request $request)
    {
        $u = $request->user();
        $q = Video::with('teacher:id,name')->latest();
        if ($u->type === 'student') {
            $q->where('grade', $u->grade);
        }

        return \App\Http\Resources\Api\VideoResource::collection($q->paginate(20));
    }

    public function exams(Request $request)
    {
        $u = $request->user();
        $q = Exam::with('questions')->latest();
        if ($u->type === 'student') {
            $q->where('grade', $u->grade);
        }

        return \App\Http\Resources\Api\ExamResource::collection($q->paginate(20));
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $u = $request->user();
        abort_unless($u->type === 'student', 403);
        if (! $exam->canAttempt($u->id)) {
            return response()->json(['message' => 'غير متاح'], 422);
        }
        $request->validate(['answers' => ['nullable', 'array']]);
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
        $result = ExamResult::create([
            'exam_id' => $exam->id, 'student_id' => $u->id, 'answers' => $request->answers ?? [],
            'marks_obtained' => $marks,
            'status_id' => \App\Models\Status::idFor($hasEssay ? 'under_review' : 'graded'),
            'submitted_at' => now(),
        ]);

        return response()->json(['marks' => $marks, 'result_id' => $result->id]);
    }

    public function payments(Request $request)
    {
        $u = $request->user();
        $q = Payment::with('student:id,name')->latest();
        if ($u->type === 'student') {
            $q->where('student_id', $u->id);
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
            ->latest()->limit(20)->get(['id', 'title', 'video_url', 'duration_seconds']);

        return response()->json(['videos' => $videos, 'generated_at' => now()]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
