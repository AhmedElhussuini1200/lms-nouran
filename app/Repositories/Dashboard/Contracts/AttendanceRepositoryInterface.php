<?php

namespace App\Repositories\Dashboard\Contracts;

use Illuminate\Http\Request;

interface AttendanceRepositoryInterface
{
    public function studentsForCourse($course, string $date);
    public function saveMany($course, string $date, array $statuses, $markedBy);
    public function absencesForStudent($studentId, int $limit = 10);
    public function absenceCount($studentId): int;
}
