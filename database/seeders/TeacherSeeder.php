<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'teacher@lms.com'],
            [
                'name' => 'نوران شرف الدين',
                'password' => Hash::make('nour123456'),
                'type' => 'admin',
                'phone' => '01000000000',
            ]
        );
    }
}
