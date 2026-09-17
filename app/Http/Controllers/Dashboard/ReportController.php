<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\Payment;
use App\Models\StudentPoint;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportController extends Controller
{
    public function index()
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);

        return view('dashboard.reports.index');
    }

    // تقرير الدرجات Excel
    public function gradesExcel(Request $request)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $grade = $request->get('grade');
        $results = ExamResult::with(['student:id,name,grade', 'exam:id,title'])
            ->when($grade, fn ($q) => $q->whereHas('student', fn ($s) => $s->where('grade', $grade)))
            ->latest()->limit(2000)->get()
            ->map(fn ($r) => [
                'student' => $r->student->name ?? '-', 'grade' => $r->student->grade ?? '-',
                'exam' => $r->exam->title ?? '-', 'marks' => $r->marks_obtained,
                'date' => $r->created_at->format('Y-m-d'),
            ]);

        return (new FastExcel($results))->download('grades.xlsx');
    }

    // تقرير الحضور Excel
    public function attendanceExcel(Request $request)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $rows = Attendance::with(['student:id,name', 'course:id,title'])->latest()->limit(2000)->get()
            ->map(fn ($a) => [
                'student' => $a->student->name ?? '-', 'course' => $a->course->title ?? '-',
                'date' => $a->date?->format('Y-m-d'), 'status' => $a->status,
            ]);

        return (new FastExcel($rows))->download('attendance.xlsx');
    }

    // تقرير المدفوعات Excel
    public function paymentsExcel(Request $request)
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
        $month = $request->get('month', date('Y-m'));
        $rows = Payment::with('student:id,name')->where('month', $month)->get()
            ->map(fn ($p) => [
                'student' => $p->student->name ?? '-', 'month' => $p->month,
                'amount' => $p->amount, 'paid' => $p->paid_amount, 'remaining' => $p->remaining,
            ]);

        return (new FastExcel($rows))->download("payments-{$month}.xlsx");
    }

    // تقرير التفاعل PDF (تقدم فيديو + نقاط)
    public function engagementPdf(Request $request)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $grade = $request->get('grade', '3_secondary');
        $students = Admin::where('type', 'student')->where('grade', $grade)->get()->map(function ($s) {
            $s->videos_completed = VideoProgress::where('student_id', $s->id)->where('completed', true)->count();
            $s->points = (int) StudentPoint::where('student_id', $s->id)->sum('points');
            $s->avg = (float) ExamResult::where('student_id', $s->id)->avg('marks_obtained') ?? 0;
            return $s;
        });

        $html = view('dashboard.reports.engagement-pdf', compact('students', 'grade'))->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'dejavusans']);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=engagement.pdf',
        ]);
    }
}
