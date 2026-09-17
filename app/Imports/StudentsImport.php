<?php

namespace App\Imports;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

// شيت الطلبة: name | email | phone | grade | password? | teacher_emails?
class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected int $created = 0;
    protected int $updated = 0;

    public function model(array $row)
    {
        $email = strtolower(trim($row['email'] ?? ''));
        if (! $email) {
            return null;
        }

        $grade = trim($row['grade'] ?? '');
        $gradeMap = [
            '1' => '1_secondary', '2' => '2_secondary', '3' => '3_secondary',
            'الأول الثانوي' => '1_secondary', 'الثاني الثانوي' => '2_secondary', 'الثالث الثانوي' => '3_secondary',
            '1_secondary' => '1_secondary', '2_secondary' => '2_secondary', '3_secondary' => '3_secondary',
        ];

        $admin = Admin::firstOrNew(['email' => $email]);
        $isNew = ! $admin->exists;
        $admin->fill([
            'name' => trim($row['name'] ?? $email),
            'phone' => trim($row['phone'] ?? '') ?: null,
            'type' => 'student',
            'grade' => $gradeMap[$grade] ?? '1_secondary',
        ]);
        if ($isNew || ! empty($row['password'])) {
            $admin->password = Hash::make(trim($row['password'] ?? '12345678'));
        }
        $admin->save();
        $isNew ? $this->created++ : $this->updated++;

        // تسجيل مع مدرسين ببريددهم (مفصولة بفاصلة)
        if (! empty($row['teacher_emails'])) {
            $ids = Admin::where('type', 'teacher')
                ->whereIn('email', array_map('trim', explode(',', $row['teacher_emails'])))
                ->pluck('id');
            if ($ids->isNotEmpty()) {
                $admin->enrolledTeachers()->syncWithoutDetaching($ids);
            }
        }

        // ولي الأمر من الشيت: موجود؟ اربط — مش موجود؟ أنشئ واربط
        $pEmail = strtolower(trim($row['parent_email'] ?? ''));
        $pPhone = trim($row['parent_phone'] ?? '');
        $pName = trim($row['parent_name'] ?? '');
        if ($pEmail || $pPhone || $pName) {
            $parent = null;
            if ($pEmail) {
                $parent = Admin::where('email', $pEmail)->first();
            }
            if (! $parent && $pPhone) {
                $parent = Admin::where('phone', $pPhone)->first();
            }
            if (! $parent) {
                $parent = Admin::create([
                    'name' => $pName ?: __('ولي أمر') . ' ' . $admin->name,
                    'email' => $pEmail ?: 'parent-' . $admin->id . '-' . time() . '@lms.local',
                    'phone' => $pPhone ?: null,
                    'type' => 'parent',
                    'password' => Hash::make('12345678'),
                ]);
            }
            $parent->students()->syncWithoutDetaching([$admin->id]);
        }

        return null; // حفظنا يدوياً (تحديث الموجود بدل التكرار)
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'grade' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function stats(): array
    {
        return ['created' => $this->created, 'updated' => $this->updated, 'failed' => count($this->failures())];
    }
}
