<?php

namespace Tests\Feature;

use App\Models\Admin;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

abstract class LmsTestCase extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected Admin $teacher;
    protected Admin $teacher2;
    protected Admin $student;
    protected Admin $parent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\StatusSeeder::class);

        $this->admin = $this->makeUser('admin@lms.test', 'admin');
        $this->teacher = $this->makeUser('t1@lms.test', 'teacher', '1_secondary', 'فيزياء');
        $this->teacher2 = $this->makeUser('t2@lms.test', 'teacher', '2_secondary', 'كيمياء');
        $this->student = $this->makeUser('s1@lms.test', 'student', '1_secondary');
        $this->parent = $this->makeUser('p1@lms.test', 'parent');
        $this->parent->students()->attach($this->student->id);

        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    protected function makeUser(string $email, string $type, ?string $grade = null, ?string $subject = null): Admin
    {
        return Admin::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make('password'),
            'type' => $type,
            'grade' => $grade,
            'subject' => $subject,
            'phone' => '01' . random_int(100000000, 999999999),
        ]);
    }

    protected function loginAs(Admin $user)
    {
        return $this->actingAs($user, 'admin');
    }

    protected function ajaxHeaders(): array
    {
        return ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'];
    }
}
