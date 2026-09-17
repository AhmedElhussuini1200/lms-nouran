<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\CourseRepositoryInterface;

class CourseRepository implements CourseRepositoryInterface
{
    public function index(Request $request)
    {
        $user = auth('admin')->user();

        $query = Course::with('teacher:id,name')->orderBy('scheduled_at', 'desc');

        // قفل الصف: الطالب صفه فقط وولي الأمر صفوف أبنائه — يتجاهل أي grade في الرابط
        $allowed = allowedGrades($user);
        if ($allowed !== null) {
            $query->whereIn('grade', $allowed ?: ['__none__']);
        } elseif ($request->filled('grade') && $request->grade !== 'all') {
            $query->where('grade', $request->grade);
        }

        if ($user && $user->type === 'teacher') {
            // المدرس يشوف حصصه فقط (الأدمن يشوف الكل)
            if ($user->type === 'teacher') {
                $query->where('teacher_id', $user->id);
            }
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
        return Course::create($data);
    }

    public function update(array $data, $course)
    {
        $course->update($data);
        return $course;
    }

    public function destroy($course)
    {
        return $course->delete();
    }

    public function show($course)
    {
        return $course->load('teacher:id,name');
    }

    public function find($id)
    {
        return Course::find($id);
    }
}
