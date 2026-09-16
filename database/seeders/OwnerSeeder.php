<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $owner = Admin::firstOrCreate(
            ['email' => 'ahmedsuperadmin@lms.com'],
            [
                'name' => 'Ahmed Elhussini',
                'password' => Hash::make('password'),
                'type' => 'admin',
                'phone' => '01000000000',
            ]
        );

        // لو الحساب موجود بكلمة مرور قديمة، حدثها للافتراضية
        if (!$owner->wasRecentlyCreated) {
            $owner->update(['password' => Hash::make('password'), 'type' => 'admin', 'is_blocked' => 0]);
        }

        $role = Role::where('name_en', 'super admin')->first();
        if ($role) {
            $owner->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
