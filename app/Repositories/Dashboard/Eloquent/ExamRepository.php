<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\ExamRepositoryInterface;

class ExamRepository implements ExamRepositoryInterface
{
    public function index(Request $request)
    {
        $user = auth('admin')->user();
        $query = Exam::with('teacher:id,name')->withCount('results')->orderBy('exam_date', 'desc');

        // قفل الصف: الطالب صفه فقط وولي الأمر صفوف أبنائه — يتجاهل أي grade في الرابط
        $allowed = allowedGrades($user);
        if ($allowed !== null) {
            $query->whereIn('grade', $allowed ?: ['__none__']);
        } elseif ($request->filled('grade') && $request->grade !== 'all') {
            $query->where('grade', $request->grade);
        }

        if ($user && $user->type === 'teacher') {
            $query->where('teacher_id', $user->id);
        }


        // فلتر المدرس (للأدمن) والمادة — ديناميكية تعدد المدرسين
        if ($request->filled('teacher') && $request->teacher !== 'all') {
            $query->where('teacher_id', $request->teacher);
        }

        if ($request->filled('subject') && $request->subject !== 'all') {
            $query->where('subject', $request->subject);
        }

        return $query->paginate(12);
    }

    public function store(array $data)
    {
        return Exam::create($data);
    }

    public function update(array $data, $exam)
    {
        $exam->update($data);
        return $exam;
    }

    public function destroy($exam)
    {
        return $exam->delete();
    }

    public function show($exam)
    {
        return $exam->load(['teacher:id,name', 'questions', 'results.student:id,name', 'results.status']);
    }

    public function find($id)
    {
        return Exam::find($id);
    }

    public function submit($exam, array $data)
    {
        $data['status_id'] = $data['status_id'] ?? \App\Models\Status::idFor(\App\Models\Status::SUBMITTED) ?? null;
        // صف مستقل لكل محاولة — لا مسح للمحاولات السابقة
        $data['attempt_no'] = $data['attempt_no'] ?? ((int) ExamResult::where('exam_id', $exam->id)->where('student_id', $data['student_id'])->max('attempt_no') + 1);

        return ExamResult::create($data + ['exam_id' => $exam->id, 'submitted_at' => now()]);
    }

    public function grade($result, array $data)
    {
        $result->update($data);
        return $result;
    }

    public function myResult($exam, $studentId)
    {
        // الأعلى درجة (ثم الأحدث عند التعادل)
        return ExamResult::where('exam_id', $exam->id)->where('student_id', $studentId)
            ->orderByDesc('marks_obtained')->latest()->first();
    }

    public function myAttempts($exam, $studentId)
    {
        return ExamResult::where('exam_id', $exam->id)->where('student_id', $studentId)
            ->with('status')->orderBy('attempt_no')->get();
    }
}
