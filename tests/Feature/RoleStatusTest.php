<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Status;

class RoleStatusTest extends LmsTestCase
{
    public function test_admin_can_create_role_with_abilities(): void
    {
        $ability = \App\Models\Ability::firstOrCreate(
            ['name' => 'view_videos'],
            ['name' => 'view_videos', 'category' => 'videos', 'action' => 'view']
        );

        $response = $this->loginAs($this->admin)->postJson('/dashboard/roles', [
            'name_ar' => 'مشرف محتوى',
            'name_en' => 'content supervisor',
            'abilities' => [$ability->id],
        ], $this->ajaxHeaders());

        $response->assertOk();
        $role = Role::where('name_en', 'content supervisor')->first();
        $this->assertNotNull($role);
        $this->assertTrue($role->abilities->pluck('id')->contains($ability->id));
    }

    public function test_duplicate_role_name_rejected(): void
    {
        Role::create(['name_ar' => 'مكرر', 'name_en' => 'dup-role']);

        $this->loginAs($this->admin)->postJson('/dashboard/roles', [
            'name_ar' => 'مكرر', 'name_en' => 'dup-role',
        ], $this->ajaxHeaders())->assertStatus(422);
    }

    public function test_teacher_cannot_manage_roles(): void
    {
        $this->loginAs($this->teacher)->get('/dashboard/roles')->assertForbidden();
    }

    public function test_status_color_can_be_updated(): void
    {
        $status = Status::where('slug', 'graded')->first();

        $this->loginAs($this->admin)->put(
            "/dashboard/statuses/{$status->id}",
            ['name_ar' => 'تم التصحيح', 'color' => 'success'],
            $this->ajaxHeaders()
        )->assertOk();

        $this->assertDatabaseHas('statuses', ['slug' => 'graded', 'color' => 'success']);
    }

    public function test_branding_requires_admin(): void
    {
        $this->loginAs($this->teacher)->get('/dashboard/branding')->assertForbidden();
        $this->loginAs($this->admin)->get('/dashboard/branding')->assertOk();
    }
}
