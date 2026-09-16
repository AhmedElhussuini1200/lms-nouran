<?php

namespace App\Services\Dashboard;

use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use App\Repositories\Dashboard\Contracts\AttendanceRepositoryInterface;

class AttendanceService
{
    protected $attendanceRepository;
    protected $whatsapp;

    public function __construct(AttendanceRepositoryInterface $attendanceRepository, WhatsappService $whatsapp)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->whatsapp = $whatsapp;
    }

    protected function authorizeManage(Course $course): void
    {
        $user = auth('admin')->user();
        if ($user->type === 'admin') {
            return;
        }
        abort_unless($user->type === 'teacher' && $course->teacher_id === $user->id, 403);
    }

    public function mark(Course $course, Request $request)
    {
        $this->authorizeManage($course);
        $date = $request->get('date', date('Y-m-d'));
        [$students, $marked] = $this->attendanceRepository->studentsForCourse($course, $date);

        return view('dashboard.attendance.mark', compact('course', 'students', 'marked', 'date'));
    }

    public function store(Course $course, Request $request)
    {
        $this->authorizeManage($course);
        $data = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['in:present,absent,late'],
        ]);

        $absentIds = $this->attendanceRepository->saveMany(
            $course, $data['date'], $data['status'], auth('admin')->id()
        );

        // إشعار أولياء أمور الغائبين (داشبورد + واتساب)
        foreach ($absentIds as $studentId) {
            $student = \App\Models\Admin::with('parents')->find($studentId);
            if (!$student) {
                continue;
            }
            $link = route('admin.courses.show', $course->id);
            foreach ($student->parents ?? [] as $parent) {
                notifyAdmin(
                    $parent->id,
                    __('غياب') . ': ' . $student->name,
                    __('تغيب عن حصة') . ' ' . $course->title . ' - ' . $data['date'],
                    'warning',
                    $link
                );
                $this->whatsapp->send(
                    $parent->phone ?? '',
                    __('غياب') . ': ' . $student->name . ' - ' . __('تغيب عن حصة') . ' ' . $course->title . ' - ' . $data['date'],
                    $parent->whatsapp_key ?? '',
                    $parent->id
                );
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => __('تم حفظ الحضور وإشعار أولياء أمور الغائبين'),
                'url' => route('admin.attendance.mark', ['course' => $course->id, 'date' => $data['date']]),
            ]);
        }

        return redirect()->route('admin.attendance.mark', ['course' => $course->id, 'date' => $data['date']])
            ->with('success', __('تم حفظ الحضور وإشعار أولياء أمور الغائبين'));
    }
}
