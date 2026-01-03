<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CourseService;
use App\Services\AuthService;

class CourseController extends Controller
{
    protected $courseService;
    protected $authService;

    public function __construct(CourseService $courseService, AuthService $authService)
    {
        $this->middleware('auth');
        $this->courseService = $courseService;
        $this->authService = $authService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = $this->courseService->getAllCourses();
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade' => 'required|in:1_secondary,2_secondary,3_secondary',
            'scheduled_at' => 'required|date',
        ]);

        $this->courseService->createCourse($data);
        return redirect()->route('courses.index')->with('success', 'تم إضافة الحصة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $course = $this->courseService->getCourseById($id);
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        $course = $this->courseService->getCourseById($id);
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade' => 'required|in:1_secondary,2_secondary,3_secondary',
            'scheduled_at' => 'required|date',
        ]);

        $this->courseService->updateCourse($id, $data);
        return redirect()->route('courses.index')->with('success', 'تم تحديث الحصة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        $this->courseService->deleteCourse($id);
        return redirect()->route('courses.index')->with('success', 'تم حذف الحصة بنجاح');
    }
}
