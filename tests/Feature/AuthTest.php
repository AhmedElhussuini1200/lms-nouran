<?php

namespace Tests\Feature;

class AuthTest extends LmsTestCase
{
    public function test_guest_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_login_page_loads(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_can_login(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@lms.test',
            'password' => 'password',
        ]);

        $response->assertOk()->assertJsonPath('url', route('admin.index'));
        $this->assertAuthenticatedAs($this->admin, 'admin');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@lms.test',
            'password' => 'wrong',
        ]);

        $response->assertRedirect();
        $this->assertGuest('admin');
    }

    public function test_blocked_user_cannot_login(): void
    {
        $this->student->update(['is_blocked' => 1]);

        $response = $this->post('/admin/login', [
            'email' => 's1@lms.test',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertGuest('admin');
    }

    public function test_each_role_sees_own_dashboard(): void
    {
        foreach (['admin' => $this->admin, 'teacher' => $this->teacher, 'student' => $this->student, 'parent' => $this->parent] as $type => $user) {
            $this->loginAs($user)->get('/dashboard')->assertOk();
        }
    }
}
