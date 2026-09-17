<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Certificate;
use App\Models\StudentPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    // شهاداتي (طالب) + كل الشهادات (مدرس/أدمن)
    public function certificates(Request $request)
    {
        $user = auth('admin')->user();
        $q = Certificate::with(['student:id,name,grade', 'exam:id,title']);
        if ($user->type === 'student') {
            $q->where('student_id', $user->id);
        } elseif ($user->type === 'parent') {
            $ids = $user->students()->pluck('admins.id');
            $q->whereIn('student_id', $ids);
        }
        $certificates = $q->latest()->paginate(20);

        return view('dashboard.achievements.certificates', compact('certificates'));
    }

    // تحقق من شهادة بكود
    public function verify(string $code)
    {
        $cert = Certificate::with(['student:id,name', 'exam:id,title'])->where('code', $code)->firstOrFail();

        return view('dashboard.achievements.verify', compact('cert'));
    }

    // PDF الشهادة
    public function pdf(Certificate $certificate)
    {
        $this->authorizeView($certificate);
        $certificate->loadMissing(['student', 'exam']);
        $html = view('dashboard.achievements.certificate-pdf', compact('certificate'))->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'default_font' => 'dejavusans']);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=certificate-{$certificate->code}.pdf",
        ]);
    }

    // لوحة الصدارة لكل صف
    public function leaderboard(Request $request)
    {
        $grade = $request->get('grade', '3_secondary');
        $board = Admin::where('type', 'student')->where('grade', $grade)
            ->withSum(['examResults as total_marks' => fn ($q) => $q->whereHas('status', fn ($s) => $s->where('slug', 'graded'))], 'marks_obtained')
            ->withCount(['examResults as exams_count'])
            ->get()
            ->map(function ($s) {
                $s->total_points = (int) StudentPoint::where('student_id', $s->id)->sum('points');
                $s->score = (float) ($s->total_marks ?? 0) + $s->total_points;
                return $s;
            })
            ->sortByDesc('score')->values()->take(20);

        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.achievements.leaderboard', compact('board', 'grade', 'grades'));
    }

    protected function authorizeView(Certificate $certificate): void
    {
        $user = auth('admin')->user();
        if (in_array($user->type, ['admin', 'teacher'])) {
            return;
        }
        if ($user->type === 'student') {
            abort_if($certificate->student_id !== $user->id, 403);
            return;
        }
        if ($user->type === 'parent') {
            abort_unless($user->students()->where('admins.id', $certificate->student_id)->exists(), 403);
        }
    }
}
