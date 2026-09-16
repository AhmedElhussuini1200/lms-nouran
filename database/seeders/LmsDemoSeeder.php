<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LmsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // الهوية الافتراضية
        foreach ([
            'site_name' => 'منصة نوران التعليمية',
            'primary_color' => '#1b84ff',
            'secondary_color' => '#17c653',
        ] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // مدرس تجريبي (type=teacher حتى يجرب صلاحيات المدرس الحقيقية)
        $teacher = Admin::firstOrCreate(
            ['email' => 'ostaz@lms.com'],
            ['name' => 'مستر عصام سمكة', 'password' => Hash::make('password'), 'type' => 'teacher', 'phone' => '01000000001']
        );

        // 3 طلاب (واحد لكل صف)
        $students = [];
        foreach (['1_secondary' => 'طالب أول ثانوي', '2_secondary' => 'طالب ثاني ثانوي', '3_secondary' => 'طالب ثالث ثانوي'] as $grade => $name) {
            $students[$grade] = Admin::firstOrCreate(
                ['email' => "student_{$grade}@lms.com"],
                ['name' => $name, 'password' => Hash::make('password'), 'type' => 'student', 'grade' => $grade, 'phone' => '01000000002']
            );
        }

        // ولي أمر مربوط بالثلاثة
        $parent = Admin::firstOrCreate(
            ['email' => 'parent@lms.com'],
            ['name' => 'ولي الأمر', 'password' => Hash::make('password'), 'type' => 'parent', 'phone' => '01000000003']
        );
        $parent->students()->syncWithoutDetaching(array_map(fn ($s) => $s->id, array_values($students)));

        // محتوى تجريبي للمدرس
        if (\App\Models\Video::where('teacher_id', $teacher->id)->count() === 0) {
            \App\Models\Video::create([
                'title' => 'شرح الدرس الأول - فيزياء', 'description' => 'فيديو تجريبي', 'grade' => '1_secondary',
                'teacher_id' => $teacher->id, 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ]);
            \App\Models\Course::create([
                'title' => 'حصة تجريبية - فيزياء', 'description' => 'حصة تجريبية', 'grade' => '1_secondary',
                'teacher_id' => $teacher->id, 'scheduled_at' => now()->addDay(),
            ]);
            \App\Models\Assignment::create([
                'title' => 'واجب تجريبي', 'description' => 'حل الأسئلة', 'grade' => '1_secondary',
                'teacher_id' => $teacher->id, 'due_date' => now()->addDays(3), 'total_marks' => 100,
            ]);
            \App\Models\Exam::create([
                'title' => 'امتحان تجريبي', 'description' => 'اختبار قصير', 'grade' => '1_secondary',
                'teacher_id' => $teacher->id, 'exam_date' => now()->addDays(7), 'duration_minutes' => 60, 'total_marks' => 100,
            ]);
        }
    }
}
