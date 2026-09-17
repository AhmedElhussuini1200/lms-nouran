<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Payment;
use App\Models\StudentPoint;
use App\Models\Video;
use App\Models\VideoProgress;

/**
 * طبقة الذكاء: ملف مهارة + توصيات مرتبة + مخاطر مفسّرة لكل طالب.
 */
class InsightService
{
    public function profile(int $studentId): array
    {
        $student = Admin::findOrFail($studentId);

        // أفضل درجة لكل امتحان
        $best = ExamResult::where('student_id', $studentId)
            ->selectRaw('exam_id, MAX(marks_obtained) as best, MAX(created_at) as last_at')
            ->groupBy('exam_id')->get()->keyBy('exam_id');

        $exams = Exam::whereIn('id', $best->keys())->get(['id', 'title', 'subject', 'total_marks', 'passing_marks', 'exam_date']);
        $perExam = $exams->map(function ($e) use ($best) {
            $b = (float) ($best[$e->id]->best ?? 0);
            $total = (float) ($e->total_marks ?: 100);

            return [
                'exam' => $e,
                'best' => $b,
                'pct' => $total > 0 ? round($b / $total * 100, 1) : 0,
                'passed' => $e->passing_marks !== null ? $b >= (float) $e->passing_marks : $b >= $total * 0.5,
            ];
        })->sortByDesc('pct')->values();

        // إتقان المواد (من أفضل الدرجات)
        $mastery = $perExam->groupBy(fn ($x) => $x['exam']->subject ?: __('عام'))->map(function ($rows, $subject) {
            return ['subject' => $subject, 'avg' => round($rows->avg('pct'), 1), 'count' => $rows->count()];
        })->sortBy('avg')->values();

        // الاتجاه: آخر 3 امتحانات زمنياً
        $chrono = $perExam->sortBy(fn ($x) => $x['exam']->exam_date)->values()->take(-3);
        $trend = 'flat';
        if ($chrono->count() >= 2) {
            $diff = $chrono->last()['pct'] - $chrono->first()['pct'];
            $trend = $diff >= 5 ? 'up' : ($diff <= -5 ? 'down' : 'flat');
        }

        // دقة الاختيارات (من كل المحاولات)
        [$mcqHits, $mcqTotal] = $this->mcqAccuracy($studentId);

        // التزام
        $att30 = Attendance::where('student_id', $studentId)->where('date', '>=', now()->subDays(30));
        $attTotal = (clone $att30)->count();
        $absences = (clone $att30)->where('status', 'absent')->count();
        $attRate = $attTotal ? round(((clone $att30)->where('status', '!=', 'absent')->count() / $attTotal) * 100) : null;

        $gradeVideos = Video::when($student->grade, fn ($q) => $q->where('grade', $student->grade))->count();
        $vidDone = VideoProgress::where('student_id', $studentId)->where('completed', true)->count();
        $vidRate = $gradeVideos ? round($vidDone / $gradeVideos * 100) : null;

        $avg = $perExam->count() ? round($perExam->avg('pct'), 1) : null;
        $points = (int) StudentPoint::where('student_id', $studentId)->sum('points');

        // مخاطر مفسّرة
        [$risk, $reasons] = $this->assess($avg, $absences, $trend, $perExam);

        return [
            'student' => $student,
            'perExam' => $perExam,
            'mastery' => $mastery,
            'trend' => $trend,
            'avg' => $avg,
            'mcqAccuracy' => $mcqTotal ? round($mcqHits / $mcqTotal * 100) : null,
            'attRate' => $attRate,
            'absences' => $absences,
            'vidRate' => $vidRate,
            'points' => $points,
            'risk' => $risk,
            'reasons' => $reasons,
            'recommendations' => $this->recommend($student, $perExam, $mastery, $absences),
        ];
    }

    protected function mcqAccuracy(int $studentId): array
    {
        $hits = 0;
        $total = 0;
        $results = ExamResult::with('exam.questions')->where('student_id', $studentId)->get();
        foreach ($results as $res) {
            $answers = $res->answers ?? [];
            foreach ($res->exam->questions ?? [] as $q) {
                if (! $q->isAutoGradable()) {
                    continue;
                }
                $given = $answers[$q->id] ?? null;
                if ($given === null) {
                    continue;
                }
                $total++;
                if (trim((string) $given) === trim((string) $q->correct_answer)) {
                    $hits++;
                }
            }
        }

        return [$hits, $total];
    }

    protected function assess(?float $avg, int $absences, string $trend, $perExam): array
    {
        $reasons = [];
        $score = 0; // كلما زاد = خطر أعلى
        if ($avg !== null && $avg < 50) {
            $score += 2;
            $reasons[] = __('متوسط الدرجات منخفض') . " ($avg%)";
        } elseif ($avg !== null && $avg < 65) {
            $score += 1;
            $reasons[] = __('المتوسط يحتاج تحسين') . " ($avg%)";
        }
        if ($absences >= 3) {
            $score += 2;
            $reasons[] = __('غياب متكرر آخر 30 يوم') . " ($absences)";
        } elseif ($absences >= 2) {
            $score += 1;
            $reasons[] = __('غيابان آخر 30 يوم');
        }
        if ($trend === 'down') {
            $score += 1;
            $reasons[] = __('الدرجات في تراجع');
        }
        $failed = $perExam->where('passed', false)->count();
        if ($failed > 0) {
            $score += 1;
            $reasons[] = __('راسب في') . " $failed " . __('امتحان');
        }

        $risk = $score >= 3 ? 'high' : ($score >= 1 ? 'medium' : 'low');

        return [$risk, $reasons];
    }

    protected function recommend(Admin $student, $perExam, $mastery, int $absences): array
    {
        $recs = [];

        // 1) واجبات متأخرة
        $doneIds = AssignmentSubmission::where('student_id', $student->id)->pluck('assignment_id');
        $overdue = Assignment::where('grade', $student->grade)->whereNotIn('id', $doneIds)
            ->where('due_date', '<', now())->orderBy('due_date')->limit(3)->get();
        foreach ($overdue as $a) {
            $recs[] = ['level' => 'high', 'icon' => '⏰', 'text' => __('سلّم الواجب المتأخر') . ': ' . $a->title, 'url' => route('admin.assignments.show', $a->id)];
        }

        // 2) أضعف مادة
        $weak = $mastery->first();
        if ($weak && $weak['avg'] < 60) {
            $exam = $perExam->firstWhere(fn ($x) => ($x['exam']->subject ?: __('عام')) === $weak['subject']);
            $recs[] = ['level' => 'high', 'icon' => '📉', 'text' => __('نقطة ضعفك') . ': ' . $weak['subject'] . " ({$weak['avg']}%) — " . __('راجعها أولاً'), 'url' => $exam ? route('admin.exams.show', $exam['exam']->id) : route('admin.videos.index')];
        }

        // 3) امتحان راسب → إعادة
        foreach ($perExam->where('passed', false)->take(2) as $x) {
            $recs[] = ['level' => 'high', 'icon' => '🎯', 'text' => __('عيد امتحان') . ' ' . $x['exam']->title . " ({$x['pct']}%)", 'url' => route('admin.exams.show', $x['exam']->id)];
        }

        // 4) حصة اليوم
        $today = Course::where('grade', $student->grade)->whereDate('scheduled_at', today())->orderBy('scheduled_at')->first();
        if ($today) {
            $recs[] = ['level' => 'medium', 'icon' => '📚', 'text' => __('حصة اليوم') . ': ' . $today->title . ' — ' . $today->scheduled_at->format('H:i'), 'url' => route('admin.courses.show', $today->id)];
        }

        // 5) فيديو غير مكتمل
        $progress = VideoProgress::with('video:id,title')->where('student_id', $student->id)
            ->where('completed', false)->orderByDesc('percent')->first();
        if ($progress?->video) {
            $recs[] = ['level' => 'medium', 'icon' => '🎬', 'text' => __('كمّل فيديو') . ' ' . $progress->video->title . " ({$progress->percent}%)", 'url' => route('admin.videos.show', $progress->video_id)];
        }

        // 6) انتظام
        if ($absences >= 2) {
            $recs[] = ['level' => 'medium', 'icon' => '📅', 'text' => __('انتظامك نازل — الغياب يضيع درجات'), 'url' => route('admin.courses.index')];
        }

        // 7) تشجيع
        if (empty($recs)) {
            $recs[] = ['level' => 'low', 'icon' => '🔥', 'text' => __('مستواك ممتاز — حافظ على الاستمرارية'), 'url' => route('admin.leaderboard')];
        }

        return array_slice($recs, 0, 5);
    }
}
