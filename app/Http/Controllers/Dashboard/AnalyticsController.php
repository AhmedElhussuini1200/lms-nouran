<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\Payment;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

// التحليلات: الطلاب المتعثرون + تقرير ولي الأمر الشهري
class AnalyticsController extends Controller
{
    public function atRisk(Request $request)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $students = $this->computeRisk();

        return view('dashboard.analytics.at-risk', compact('students'));
    }

    // تنبيه أولياء أمور المتعثرين واتساب
    public function alertParents(Request $request, WhatsappService $whatsapp)
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
        $sent = 0;
        foreach ($this->computeRisk()->where('risk', 'high') as $s) {
            foreach ($s->parents ?? [] as $parent) {
                $whatsapp->send(
                    $parent->phone ?? '',
                    __('تنبيه: مستوى') . ' ' . $s->name . ' ' . __('يحتاج متابعة — متوسط الدرجات') . ': ' . round($s->avg, 1) . ' — ' . __('الغياب') . ': ' . $s->absences,
                    $parent->whatsapp_key ?? '', $parent->id
                );
                $sent++;
            }
        }

        return back()->with('success', __('تم إرسال تنبيهات لعدد') . ': ' . $sent);
    }

    // تقرير ولي الأمر PDF (درجات + حضور + مدفوعات)
    public function parentReport(Admin $student)
    {
        $user = auth('admin')->user();
        abort_unless(in_array($user->type, ['admin', 'teacher']) || ($user->type === 'parent' && $user->students()->where('admins.id', $student->id)->exists()) || ($user->type === 'student' && $user->id === $student->id), 403);

        $results = ExamResult::with('exam:id,title')->where('student_id', $student->id)->latest()->limit(20)->get();
        $absences = Attendance::where('student_id', $student->id)->where('status', 'absent')->count();
        $payments = Payment::where('student_id', $student->id)->latest()->limit(12)->get();

        $html = view('dashboard.analytics.parent-report-pdf', compact('student', 'results', 'absences', 'payments'))->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'dejavusans']);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=parent-report-{$student->id}.pdf",
        ]);
    }

    protected function computeRisk()
    {
        return Admin::where('type', 'student')->with('parents')->get()->map(function ($s) {
            $s->avg = (float) (ExamResult::where('student_id', $s->id)->avg('marks_obtained') ?? 0);
            $s->absences = Attendance::where('student_id', $s->id)->where('status', 'absent')->where('date', '>=', now()->subDays(30))->count();
            $s->risk = ($s->avg < 50 || $s->absences >= 3) ? 'high' : (($s->avg < 65 || $s->absences >= 2) ? 'medium' : 'low');
            return $s;
        })->sortBy(fn ($s) => ['high' => 0, 'medium' => 1, 'low' => 2][$s->risk])->values();
    }
}
