<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FamilyIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(): Admin
    {
        return Admin::create([
            'name' => 'admin', 'email' => 'a@t.t', 'password' => Hash::make('password'), 'type' => 'admin',
        ]);
    }

    public function test_parent_requires_children(): void
    {
        $this->actingAs($this->makeAdmin(), 'admin');
        $res = $this->post(route('admin.admins.store'), [
            'name' => 'p', 'email' => 'p@t.t', 'password' => '12345678', 'password_confirmation' => '12345678', 'type' => 'parent',
        ]);
        $res->assertSessionHasErrors('children');
        $this->assertDatabaseMissing('admins', ['email' => 'p@t.t']);
    }

    public function test_student_with_inline_parent_links(): void
    {
        $this->actingAs($this->makeAdmin(), 'admin');
        $res = $this->post(route('admin.admins.store'), [
            'name' => 's', 'email' => 's@t.t', 'password' => '12345678', 'password_confirmation' => '12345678',
            'type' => 'student', 'grade' => '1_secondary',
            'parent_name' => 'pp', 'parent_email' => 'pp@t.t', 'parent_phone' => '01000000001',
        ]);
        $res->assertRedirect();
        $st = Admin::where('email', 's@t.t')->firstOrFail();
        $this->assertEquals(1, $st->parents()->count());
    }

    public function test_delete_student_soft_deletes_orphan_parent_and_restore_brings_back(): void
    {
        $me = $this->makeAdmin();
        $this->actingAs($me, 'admin');
        $st = Admin::create(['name' => 's', 'email' => 's@t.t', 'password' => 'x', 'type' => 'student', 'grade' => '1_secondary']);
        $par = Admin::create(['name' => 'p', 'email' => 'p@t.t', 'password' => 'x', 'type' => 'parent']);
        $par->students()->attach($st->id);

        $this->delete(route('admin.admins.destroy', $st->id))->assertRedirect();
        $this->assertSoftDeleted('admins', ['id' => $st->id]);
        $this->assertSoftDeleted('admins', ['id' => $par->id]);

        $this->get(url("dashboard/trash/Admin/{$st->id}"))->assertOk();
        $this->assertNotSoftDeleted('admins', ['id' => $st->id]);
        $this->assertNotSoftDeleted('admins', ['id' => $par->id]);
    }
}
