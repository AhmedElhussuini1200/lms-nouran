<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Services\AuthService;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $authService;

    public function __construct(DashboardService $dashboardService, AuthService $authService)
    {
        $this->dashboardService = $dashboardService;
        $this->authService = $authService;
    }

    public function index()
    {
        $user = $this->authService->getUser();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isTeacher()) {
            $data = $this->dashboardService->getTeacherDashboard();
            return view('dashboard.teacher', $data);
        } elseif ($user->isStudent()) {
            $data = $this->dashboardService->getStudentDashboard();
            return view('dashboard.student', $data);
        } elseif ($user->isParent()) {
            $data = $this->dashboardService->getParentDashboard();
            return view('dashboard.parent', $data);
        }

        return redirect()->route('login');
    }
}
