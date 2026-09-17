<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(protected EnrollmentService $enrollments) {}

    // كارت اختيار المدرس (هتحضر عند مين؟)
    public function picker()
    {
        $user = auth('admin')->user();
        abort_unless($user->type === 'student', 403);
        $teachers = $this->enrollments->teachersFor($user);
        if ($teachers->count() <= 1) {
            if ($teachers->count() === 1) {
                session(['current_teacher_id' => $teachers->first()->id]);
            }

            return redirect()->route('admin.index');
        }

        return view('dashboard.enroll.pick', compact('teachers'));
    }

    // تأكيد الاختيار
    public function choose(Request $request)
    {
        $user = auth('admin')->user();
        abort_unless($user->type === 'student', 403);
        $request->validate(['teacher_id' => ['required', 'exists:admins,id']]);
        $this->enrollments->choose($user, (int) $request->teacher_id);

        if ($request->expectsJson()) {
            return response()->json(['message' => __('تم الاختيار'), 'url' => route('admin.index')]);
        }

        return redirect()->route('admin.index');
    }

    // تغيير المادة (يرجع للكارت)
    public function switch()
    {
        $this->enrollments->reset();

        return redirect()->route('admin.enroll.pick');
    }
}
