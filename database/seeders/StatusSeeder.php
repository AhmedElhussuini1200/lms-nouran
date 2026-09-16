<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'submitted', 'name_ar' => 'تم التسليم', 'name_en' => 'Submitted', 'color' => 'info', 'scope' => 'submission'],
            ['slug' => 'under_review', 'name_ar' => 'قيد التصحيح', 'name_en' => 'Under review', 'color' => 'warning', 'scope' => 'submission'],
            ['slug' => 'graded', 'name_ar' => 'تم التصحيح', 'name_en' => 'Graded', 'color' => 'success', 'scope' => 'submission'],
            ['slug' => 'returned', 'name_ar' => 'راجع للتعديل', 'name_en' => 'Returned', 'color' => 'danger', 'scope' => 'submission'],
            ['slug' => 'pending', 'name_ar' => 'معلق', 'name_en' => 'Pending', 'color' => 'warning', 'scope' => 'payment'],
            ['slug' => 'partial', 'name_ar' => 'مدفوع جزئياً', 'name_en' => 'Partial', 'color' => 'info', 'scope' => 'payment'],
            ['slug' => 'paid', 'name_ar' => 'مدفوع', 'name_en' => 'Paid', 'color' => 'success', 'scope' => 'payment'],
            ['slug' => 'overdue', 'name_ar' => 'متأخر', 'name_en' => 'Overdue', 'color' => 'danger', 'scope' => 'payment'],
        ];

        foreach ($rows as $row) {
            Status::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
