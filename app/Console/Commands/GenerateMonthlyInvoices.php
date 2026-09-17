<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Payment;
use Illuminate\Console\Command;

// توليد فواتير الشهر من باقات المدرسين: عدد الحصص × سعر الحصة
class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'lms:invoices {month? : YYYY-MM (default: current)} {--dry : عرض فقط}';
    protected $description = 'توليد فواتير شهرية من باقات المدرسين لكل طالب مسجل';

    public function handle(): int
    {
        $month = $this->argument('month') ?: date('Y-m');
        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->error('صيغة الشهر YYYY-MM');

            return self::FAILURE;
        }
        $dry = (bool) $this->option('dry');
        $created = 0;
        $skipped = 0;

        $teachers = Admin::where('type', 'teacher')->where('price_per_class', '>', 0)->get();
        foreach ($teachers as $t) {
            $fee = (int) ($t->monthly_classes ?? 8) * (float) $t->price_per_class;
            if ($fee <= 0) {
                continue;
            }
            // طلبة المدرس: المسجلون معه، وإلا طلبة صفوف حصصه
            $studentIds = $t->enrolledStudents()->pluck('admins.id');
            if ($studentIds->isEmpty()) {
                $grades = \App\Models\Course::where('teacher_id', $t->id)->distinct()->pluck('grade');
                $studentIds = Admin::where('type', 'student')->whereIn('grade', $grades)->pluck('id');
            }
            foreach ($studentIds as $sid) {
                $exists = Payment::where('student_id', $sid)->where('month', $month)->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }
                if (! $dry) {
                    Payment::create([
                        'student_id' => $sid, 'month' => $month, 'amount' => $fee,
                        'paid_amount' => 0, 'method' => explode(',', $t->pay_methods ?? 'cash')[0] ?? 'cash',
                        'notes' => __('باقة') . ' ' . ($t->brand_name ?? $t->name) . ': ' . $t->monthly_classes . ' × ' . $t->price_per_class,
                        'created_by' => $t->id,
                    ]);
                }
                $created++;
            }
        }

        $this->info("invoices month=$month created=$created skipped=$skipped" . ($dry ? ' (dry)' : ''));

        return self::SUCCESS;
    }
}
