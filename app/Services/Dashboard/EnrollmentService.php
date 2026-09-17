<?php

namespace App\Services\Dashboard;

use App\Models\Admin;

/**
 * منطق اختيار المدرس (هتحضر عند مين؟) — الكنترولر رفيع.
 */
class EnrollmentService
{
    public function teachersFor(Admin $student)
    {
        return $student->enrolledTeachers()->orderBy('name')->get();
    }

    public function needsPicker(Admin $student): bool
    {
        return $student->type === 'student'
            && $student->enrolledTeachers()->count() > 1
            && ! currentTeacher($student);
    }

    public function choose(Admin $student, int $teacherId): void
    {
        abort_unless(
            $student->enrolledTeachers()->where('admins.id', $teacherId)->exists(),
            403
        );
        session(['current_teacher_id' => $teacherId]);
    }

    public function reset(): void
    {
        session()->forget('current_teacher_id');
    }
}
