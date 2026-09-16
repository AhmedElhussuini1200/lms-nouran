<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\AttendanceService;

class AttendanceController extends Controller
{
    protected $service;

    public function __construct(AttendanceService $service)
    {
        $this->service = $service;
    }

    public function mark(Course $course, Request $request)
    {
        return $this->service->mark($course, $request);
    }

    public function store(Course $course, Request $request)
    {
        return $this->service->store($course, $request);
    }
}
