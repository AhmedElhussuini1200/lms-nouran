<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\CourseService;
use App\Http\Requests\Dashboard\StoreCourseRequest;
use App\Http\Requests\Dashboard\UpdateCourseRequest;

class CourseController extends Controller
{
    protected $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_courses');
        return $this->service->index($request);
    }

    public function create()
    {
        $this->authorize('create_courses');
        return $this->service->create();
    }

    public function store(StoreCourseRequest $request)
    {
        $this->authorize('create_courses');
        return $this->service->store($request);
    }

    public function show(Course $course)
    {
        $this->authorize('view_courses');
        return $this->service->show($course);
    }

    public function edit(Course $course)
    {
        $this->authorize('update_courses');
        return $this->service->edit($course);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->authorize('update_courses');
        return $this->service->update($request, $course);
    }

    public function destroy(Request $request, Course $course)
    {
        $this->authorize('delete_courses');
        return $this->service->destroy($request, $course);
    }

    public function calendar()
    {
        return $this->service->calendar();
    }

    public function events(Request $request)
    {
        return $this->service->events($request);
    }
}
