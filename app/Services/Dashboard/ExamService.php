<?php

namespace App\Services\Dashboard;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\StoreExamRequest;
use App\Http\Requests\Dashboard\UpdateExamRequest;
use App\Http\Requests\Dashboard\StoreQuestionRequest;
use App\Repositories\Dashboard\Contracts\ExamRepositoryInterface;
use App\Repositories\Dashboard\Contracts\QuestionRepositoryInterface;
use App\Services\WhatsappService;

class ExamService
{
    protected $examRepository;
    protected $questionRepository;
    protected $whatsapp;

    public function __construct(ExamRepositoryInterface $examRepository, QuestionRepositoryInterface $questionRepository, WhatsappService $whatsapp)
    {
        $this->examRepository = $examRepository;
        $this->questionRepository = $questionRepository;
        $this->whatsapp = $whatsapp;
    }

    public function index(Request $request)
    {
        $exams = $this->examRepository->index($request);
        $grades = ['' => __('الكل'), '1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        $teachers = auth('admin')->user()->type === 'admin'
            ? \App\Models\Admin::where('type', 'teacher')->orderBy('name')->get(['id', 'name', 'subject'])
            : collect();

        return view('dashboard.exams.index', compact('exams', 'grades', 'teachers'));
    }

    public function create()
    {
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.exams.create', compact('grades'));
    }

    public function store(StoreExamRequest $request)
    {
        $data = $request->validated();
        $data['teacher_id'] = auth('admin')->id();

        $exam = $this->examRepository->store($data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم إضافة الامتحان بنجاح'), 'url' => route('admin.exams.show', $exam->id)]);
        }

        return redirect()->route('admin.exams.show', $exam->id)->with('success', __('تم إضافة الامتحان بنجاح'));
    }

    public function show(Exam $exam)
    {
        $this->authorizeView($exam);
        $exam = $this->examRepository->show($exam);
        $user = auth('admin')->user();
        $myResult = $user->type === 'student' ? $this->examRepository->myResult($exam, $user->id) : null;

        // خلط الأسئلة لو مفعّل (للطالب فقط)
        $questions = $exam->questions;
        if ($exam->shuffle_questions && $user->type === 'student') {
            $seed = (int) ($user->id . date('Ymd'));
            srand($seed);
            $questions = $questions->shuffle();
        }

        $attemptsUsed = $user->type === 'student' ? $exam->attemptsUsed($user->id) : 0;
        $canAttempt = $user->type !== 'student' || $exam->canAttempt($user->id);

        return view('dashboard.exams.show', compact('exam', 'myResult', 'questions', 'attemptsUsed', 'canAttempt'));
    }

    public function edit(Exam $exam)
    {
        $this->authorizeOwner($exam);
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.exams.edit', compact('exam', 'grades'));
    }

    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $this->authorizeOwner($exam);
        $this->examRepository->update($request->validated(), $exam);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تحديث الامتحان بنجاح'), 'url' => route('admin.exams.show', $exam->id)]);
        }

        return redirect()->route('admin.exams.show', $exam->id)->with('success', __('تم تحديث الامتحان بنجاح'));
    }

    public function destroy(Request $request, Exam $exam)
    {
        $this->authorizeOwner($exam);
        $this->examRepository->destroy($exam);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حذف الامتحان بنجاح'), 'url' => route('admin.exams.index')]);
        }

        return redirect()->route('admin.exams.index')->with('success', __('تم حذف الامتحان بنجاح'));
    }

    public function submit(Request $request, Exam $exam)
    {
        abort_unless(auth('admin')->user()->type === 'student', 403);
        $this->authorizeView($exam);

        // 1) نافذة الوقت
        if (! $exam->isOpen()) {
            return redirect()->back()->with('error_message', __('الامتحان خارج الوقت المسموح'));
        }

        // 2) عدد المحاولات
        $studentId = auth('admin')->id();
        if (! $exam->canAttempt($studentId)) {
            return redirect()->back()->with('error_message', __('استنفدت عدد المحاولات المتاحة'));
        }

        // 3) زمن المؤقت: لازم يكون بدأ محاولة ولم يتجاوز المدة
        $attempt = \App\Models\ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->whereNull('submitted_at')
            ->latest()->first();
        if ($exam->duration_minutes && $attempt?->started_at) {
            $deadline = $attempt->started_at->copy()->addMinutes($exam->duration_minutes + 2); // سماح دقيقتين
            if (now()->gt($deadline)) {
                $attempt->update(['submitted_at' => now()]);
                return redirect()->back()->with('error_message', __('انتهى وقت الامتحان'));
            }
        }

        $request->validate(['answers' => ['nullable', 'array']]);
        $exam->loadMissing('questions');

        $answers = $request->answers ?? [];
        $autoMarks = 0;
        $hasEssay = false;

        foreach ($exam->questions as $q) {
            $given = $answers[$q->id] ?? null;
            if ($q->type === 'essay') {
                $hasEssay = true;
                continue;
            }
            if ($q->isAutoGradable() && $given !== null && trim((string) $given) === trim((string) $q->correct_answer)) {
                $autoMarks += (float) $q->marks;
            }
        }

        $data = [
            'student_id' => auth('admin')->id(),
            'answers' => $answers,
            'marks_obtained' => $autoMarks,
            // تصحيح تلقائي كامل لو مفيش مقالي، وإلا بانتظار مراجعة المدرس
            'status_id' => Status::idFor($hasEssay ? Status::UNDER_REVIEW : Status::GRADED),
        ];

        $this->examRepository->submit($exam, $data);

        // قفل المحاولة الحالية
        $attempt?->update([
            'submitted_at' => now(),
            'tab_switches' => (int) $request->input('tab_switches', $attempt->tab_switches ?? 0),
        ]);
        // لو مفيش محاولة مفتوحة (امتحان قديم قبل الميزة) سجّل واحدة مقفولة
        if (! $attempt) {
            \App\Models\ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => $studentId,
                'attempt_no' => $exam->attempts()->where('student_id', $studentId)->count() + 1,
                'started_at' => now(),
                'submitted_at' => now(),
                'tab_switches' => (int) $request->input('tab_switches', 0),
            ]);
        }

        // نقاط تحفيزية: 10 نقاط لكل امتحان مُسلّم + 1 لكل درجة
        $points = 10 + (int) $autoMarks;
        \App\Models\StudentPoint::create([
            'student_id' => $studentId,
            'points' => $points,
            'reason' => 'exam_submit',
            'source_type' => \App\Models\Exam::class,
            'source_id' => $exam->id,
        ]);

        // شهادة تلقائية عند تجاوز درجة النجاح
        if (! $hasEssay && $exam->passing_marks !== null && $autoMarks >= (float) $exam->passing_marks) {
            $exists = \App\Models\Certificate::where('exam_id', $exam->id)->where('student_id', $studentId)->exists();
            if (! $exists) {
                \App\Models\Certificate::create([
                    'student_id' => $studentId,
                    'exam_id' => $exam->id,
                    'code' => 'CERT-' . strtoupper(uniqid()),
                    'score' => $autoMarks,
                ]);
            }
        }

        $student = auth('admin')->user();
        notifyAdmin($exam->teacher_id, __('تسليم امتحان جديد'), $student->name . ' - ' . $exam->title, 'warning', route('admin.exams.show', $exam->id));

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تسليم الامتحان بنجاح'), 'url' => route('admin.exams.show', $exam->id)]);
        }

        return redirect()->route('admin.exams.show', $exam->id)->with('success', __('تم تسليم الامتحان بنجاح'));
    }

    public function startAttempt(Request $request, Exam $exam)
    {
        abort_unless(auth('admin')->user()->type === 'student', 403);
        $this->authorizeView($exam);
        $studentId = auth('admin')->id();

        if (! $exam->canAttempt($studentId)) {
            return response()->json(['message' => __('غير متاح: خارج الوقت أو استنفدت المحاولات')], 422);
        }

        $open = \App\Models\ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $studentId)->whereNull('submitted_at')->latest()->first();
        if ($open) {
            return response()->json(['started_at' => $open->started_at, 'attempt_no' => $open->attempt_no]);
        }

        $maxNo = $exam->attempts()->where('student_id', $studentId)->max('attempt_no') ?: 0;
        $attempt = \App\Models\ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $studentId,
            'attempt_no' => $maxNo + 1,
            'started_at' => now(),
        ]);

        return response()->json(['started_at' => $attempt->started_at, 'attempt_no' => $attempt->attempt_no]);
    }

    public function addQuestion(StoreQuestionRequest $request, Exam $exam)
    {
        $this->authorizeOwner($exam);
        $data = $request->validated();
        $data['options'] = array_values(array_filter($data['options'] ?? []));
        $data['sort'] = $exam->questions()->max('sort') + 1;

        $this->questionRepository->store($exam, $data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تمت إضافة السؤال بنجاح'), 'url' => route('admin.exams.show', $exam->id)]);
        }

        return redirect()->route('admin.exams.show', $exam->id)->with('success', __('تمت إضافة السؤال بنجاح'));
    }

    public function deleteQuestion(Request $request, Question $question)
    {
        $examId = $question->exam_id;
        $this->authorizeOwner($question->exam);

        $this->questionRepository->destroy($question);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حذف السؤال بنجاح'), 'url' => route('admin.exams.show', $examId)]);
        }

        return redirect()->back()->with('success', __('تم حذف السؤال بنجاح'));
    }

    public function grade(Request $request, ExamResult $result)
    {
        $request->validate([
            'marks_obtained' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:submitted,under_review,graded,returned'],
        ]);
        $this->authorizeOwner($result->exam);
        $data = $request->only('marks_obtained');
        $data['status_id'] = \App\Models\Status::idFor($request->status ?? \App\Models\Status::GRADED);
        $this->examRepository->grade($result, $data);

        $link = route('admin.exams.show', $result->exam_id);
        notifyAdmin($result->student_id, __('نتيجة الامتحان'), __('درجتك') . ': ' . $request->marks_obtained, 'success', $link);
        foreach ($result->student->parents ?? [] as $parent) {
            notifyAdmin($parent->id, __('نتيجة امتحان'), $result->student->name . ': ' . $request->marks_obtained, 'success', $link);
            $this->whatsapp->send(
                $parent->phone ?? '',
                __('نتيجة امتحان') . ': ' . $result->student->name . ' - ' . $result->exam->title . ' - ' . __('درجته') . ': ' . $request->marks_obtained,
                $parent->whatsapp_key ?? '',
                $parent->id
            );
        }

        if ($request->ajax()) {
            return response()->json(['message' => __('تم رصد الدرجة بنجاح'), 'url' => $link]);
        }

        return redirect()->back()->with('success', __('تم رصد الدرجة بنجاح'));
    }

    protected function authorizeOwner(Exam $exam): void
    {
        $user = auth('admin')->user();
        if ($user->type === 'admin') {
            return;
        }
        abort_if($exam->teacher_id !== $user->id, 403, __('غير مصرح لك'));
    }

    protected function authorizeView(Exam $exam): void
    {
        $user = auth('admin')->user();
        if ($user->type === 'admin' || $user->type === 'parent') {
            return;
        }
        if ($user->type === 'teacher') {
            abort_if($exam->teacher_id !== $user->id, 403, __('غير مصرح لك'));
        }
        if ($user->type === 'student') {
            abort_if($exam->grade !== $user->grade, 403, __('غير مصرح لك'));
        }
    }
}
