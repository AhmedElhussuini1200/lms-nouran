<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Admin;
use App\Models\Attendance;
use App\Repositories\Dashboard\Contracts\AttendanceRepositoryInterface;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function studentsForCourse($course, string $date)
    {
        $students = Admin::where('type', 'student')
            ->where('grade', $course->grade)
            ->orderBy('name')
            ->get(['id', 'name']);

        $marked = Attendance::where('course_id', $course->id)
            ->where('date', $date)
            ->pluck('status', 'student_id');

        return [$students, $marked];
    }

    public function saveMany($course, string $date, array $statuses, $markedBy)
    {
        $absentIds = [];

        foreach ($statuses as $studentId => $status) {
            if (!in_array($status, [Attendance::PRESENT, Attendance::ABSENT, Attendance::LATE])) {
                continue;
            }
            $record = Attendance::updateOrCreate(
                ['course_id' => $course->id, 'student_id' => $studentId, 'date' => $date],
                ['status' => $status, 'marked_by' => $markedBy]
            );
            if ($status === Attendance::ABSENT) {
                $absentIds[] = (int) $studentId;
            }
        }

        return $absentIds;
    }

    public function absencesForStudent($studentId, int $limit = 10)
    {
        return Attendance::with('course:id,title')
            ->where('student_id', $studentId)
            ->where('status', Attendance::ABSENT)
            ->orderByDesc('date')
            ->limit($limit)
            ->get();
    }

    public function absenceCount($studentId): int
    {
        return Attendance::where('student_id', $studentId)
            ->where('status', Attendance::ABSENT)
            ->count();
    }
}
