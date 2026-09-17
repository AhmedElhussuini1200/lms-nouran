<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\AssignmentRepositoryInterface;

class AssignmentRepository implements AssignmentRepositoryInterface
{
    public function index(Request $request)
    {
        $user = auth('admin')->user();
        $query = Assignment::with('teacher:id,name')->withCount('submissions')->orderBy('due_date', 'desc');

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
        return Assignment::create($data);
    }

    public function update(array $data, $assignment)
    {
        $assignment->update($data);
        return $assignment;
    }

    public function destroy($assignment)
    {
        return $assignment->delete();
    }

    public function show($assignment)
    {
        return $assignment->load(['teacher:id,name', 'submissions.student:id,name', 'submissions.status', 'submissions.student.parents:id,name']);
    }

    public function find($id)
    {
        return Assignment::find($id);
    }

    public function submit($assignment, array $data)
    {
        $data['status_id'] = \App\Models\Status::idFor(\App\Models\Status::SUBMITTED) ?? null;
        return AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $data['student_id']],
            $data + ['submitted_at' => now()]
        );
    }

    public function grade($submission, array $data)
    {
        $submission->update($data);
        return $submission;
    }

    public function mySubmission($assignment, $studentId)
    {
        return AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $studentId)->first();
    }
}
