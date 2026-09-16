<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $actions = ['view', 'show', 'create', 'update', 'delete'];

        // توليد صلاحيات كل موديولات المنصة
        foreach (Role::$modules as $category) {
            foreach ($actions as $action) {
                Ability::firstOrCreate(
                    ['name' => $action . '_' . $category],
                    ['name' => $action . '_' . $category, 'category' => $category, 'action' => $action]
                );
            }
        }

        $all = Ability::pluck('id');
        $viewOnly = Ability::whereIn('action', ['view', 'show'])->pluck('id');

        $roles = [
            'super' => ['name_ar' => 'مدير عام', 'name_en' => 'super admin', 'abilities' => $all],
            'teacher' => ['name_ar' => 'مدرس', 'name_en' => 'teacher', 'abilities' => $all],
            'accountant' => ['name_ar' => 'محاسب', 'name_en' => 'accountant', 'abilities' => Ability::where('category', 'payments')->pluck('id')->merge(Ability::whereIn('action', ['view', 'show'])->pluck('id'))],
            'student' => ['name_ar' => 'طالب', 'name_en' => 'student', 'abilities' => $viewOnly],
            'parent' => ['name_ar' => 'ولي أمر', 'name_en' => 'parent', 'abilities' => $viewOnly],
        ];

        foreach ($roles as $key => $row) {
            $role = Role::firstOrCreate(['name_en' => $row['name_en']], ['name_ar' => $row['name_ar'], 'name_en' => $row['name_en']]);
            $role->abilities()->sync($row['abilities']);
        }

        // ربط الحسابات التجريبية بالأدوار المناسبة
        $map = [
            'teacher@lms.com' => 'super admin',
        ];
        foreach ($map as $email => $roleName) {
            $admin = Admin::where('email', $email)->first();
            $role = Role::where('name_en', $roleName)->first();
            if ($admin && $role) {
                $admin->roles()->syncWithoutDetaching([$role->id]);
            }
        }

        // كل مدرس يأخذ دور المدرس، وكل طالب دور الطالب، وكل ولي أمر دوره
        $byType = ['teacher' => 'teacher', 'student' => 'student', 'parent' => 'parent'];
        foreach ($byType as $type => $roleName) {
            $role = Role::where('name_en', $roleName)->first();
            if (!$role) {
                continue;
            }
            $ids = Admin::where('type', $type)->pluck('id');
            foreach ($ids as $id) {
                $admin = Admin::find($id);
                $admin?->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }
}
