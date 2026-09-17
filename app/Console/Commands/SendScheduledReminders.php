<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Payment;
use App\Services\WhatsappService;
use Illuminate\Console\Command;

// تذكيرات مجدولة: حصص اليوم + أقساط متأخرة (يُشغَّل يومياً عبر scheduler)
class SendScheduledReminders extends Command
{
    protected $signature = 'lms:remind {--dry : عرض فقط بدون إرسال}';
    protected $description = 'تذكير حصص اليوم والأقساط المتأخرة (واتساب + إشعار داشبورد)';

    public function handle(WhatsappService $whatsapp): int
    {
        $dry = (bool) $this->option('dry');
        $sent = 0;

        // 1) حصص اليوم
        $today = Course::with('teacher:id,name')->whereDate('scheduled_at', today())->get();
        foreach ($today as $course) {
            $students = \App\Models\Admin::where('type', 'student')->where('grade', $course->grade)->with('parents')->get();
            foreach ($students as $s) {
                $msg = __('تذكير: حصة اليوم') . ' ' . $course->title . ' - ' . $course->scheduled_at->format('H:i');
                if (! $dry) {
                    notifyAdmin($s->id, __('حصة اليوم'), $msg, 'info', route('admin.courses.show', $course->id));
                    foreach ($s->parents ?? [] as $p) {
                        $whatsapp->send($p->phone ?? '', $msg . ' - ' . $s->name, $p->whatsapp_key ?? '', $p->id);
                    }
                }
                $sent++;
            }
        }

        // 2) أقساط متأخرة (متبقي > 0)
        $overdue = Payment::with(['student.parents'])->get()->filter(fn ($p) => $p->remaining > 0);
        foreach ($overdue as $p) {
            $msg = __('تذكير: مبلغ مستحق') . ' ' . $p->remaining . ' ج - ' . $p->month;
            if (! $dry) {
                notifyAdmin($p->student_id, __('قسط مستحق'), $msg, 'warning', route('admin.payments.index'));
                foreach ($p->student->parents ?? [] as $parent) {
                    $whatsapp->send($parent->phone ?? '', $msg . ' - ' . $p->student->name, $parent->whatsapp_key ?? '', $parent->id);
                }
            }
            $sent++;
        }

        $this->info("reminders: {$sent}" . ($dry ? ' (dry)' : ''));

        return self::SUCCESS;
    }
}
