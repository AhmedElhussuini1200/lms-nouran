<?php

namespace App\Services\Dashboard;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\StoreCourseRequest;
use App\Http\Requests\Dashboard\UpdateCourseRequest;
use App\Repositories\Dashboard\Contracts\CourseRepositoryInterface;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function index(Request $request)
    {
        $courses = $this->courseRepository->index($request);
        $grades = ['' => __('الكل'), '1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        $teachers = auth('admin')->user()->type === 'admin'
            ? \App\Models\Admin::where('type', 'teacher')->orderBy('name')->get(['id', 'name', 'subject'])
            : collect();

        return view('dashboard.courses.index', compact('courses', 'grades', 'teachers'));
    }

    public function create()
    {
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.courses.create', compact('grades'));
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();
        $data['teacher_id'] = auth('admin')->id();

        $course = $this->courseRepository->store($data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم إضافة الحصة بنجاح'), 'url' => route('admin.courses.show', $course->id)]);
        }

        return redirect()->route('admin.courses.show', $course->id)->with('success', __('تم إضافة الحصة بنجاح'));
    }

    public function show(Course $course)
    {
        $this->authorizeView($course);
        $course = $this->courseRepository->show($course);

        return view('dashboard.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorizeOwner($course);
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.courses.edit', compact('course', 'grades'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->authorizeOwner($course);
        $this->courseRepository->update($request->validated(), $course);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تحديث الحصة بنجاح'), 'url' => route('admin.courses.show', $course->id)]);
        }

        return redirect()->route('admin.courses.show', $course->id)->with('success', __('تم تحديث الحصة بنجاح'));
    }

    public function destroy(Request $request, Course $course)
    {
        $this->authorizeOwner($course);
        $this->courseRepository->destroy($course);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حذف الحصة بنجاح'), 'url' => route('admin.courses.index')]);
        }

        return redirect()->route('admin.courses.index')->with('success', __('تم حذف الحصة بنجاح'));
    }

    public function calendar()
    {
        return view('dashboard.courses.calendar');
    }

    public function events(Request $request)
    {
        $user = auth('admin')->user();
        $query = Course::with('teacher:id,name')->whereNotNull('scheduled_at');

        if ($user->type === 'student' && $user->grade) {
            $query->where('grade', $user->grade);
        }
        if ($user->type === 'teacher') {
            $query->where('teacher_id', $user->id);
        }

        return response()->json($query->get()->map(fn ($c) => [
            'id' => $c->id,
            'title' => $c->title . ($c->subject ? " ({$c->subject})" : ''),
            'start' => $c->scheduled_at->toIso8601String(),
            'url' => route('admin.courses.show', $c->id),
            'backgroundColor' => '#1b84ff',
            'extendedProps' => ['teacher' => $c->teacher->name ?? '', 'grade' => $c->grade],
        ]));
    }

    protected function authorizeOwner(Course $course): void
    {
        $user = auth('admin')->user();
        if ($user->type === 'admin') {
            return;
        }
        abort_if($course->teacher_id !== $user->id, 403, __('غير مصرح لك'));
    }

    protected function authorizeView(Course $course): void
    {
        $user = auth('admin')->user();
        if ($user->type === 'admin' || $user->type === 'parent') {
            return;
        }
        if ($user->type === 'teacher') {
            abort_if($course->teacher_id !== $user->id, 403, __('غير مصرح لك'));
        }
        if ($user->type === 'student') {
            abort_if($course->grade !== $user->grade, 403, __('غير مصرح لك'));
        }
    }
}
