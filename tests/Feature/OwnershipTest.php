<?php

namespace Tests\Feature;

use App\Models\Video;

class OwnershipTest extends LmsTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->t1Video = Video::create([
            'title' => 'T1 video', 'grade' => '1_secondary',
            'teacher_id' => $this->teacher->id, 'video_url' => 'https://example.com/t1',
        ]);
        $this->t2Video = Video::create([
            'title' => 'T2 video', 'grade' => '2_secondary',
            'teacher_id' => $this->teacher2->id, 'video_url' => 'https://example.com/t2',
        ]);
    }

    public function test_teacher_sees_only_own_videos_in_index(): void
    {
        $response = $this->loginAs($this->teacher)->get('/dashboard/videos');

        $response->assertOk()->assertSee('T1 video')->assertDontSee('T2 video');
    }

    public function test_teacher_cannot_open_other_teacher_video(): void
    {
        $this->loginAs($this->teacher)
            ->get("/dashboard/videos/{$this->t2Video->id}")
            ->assertForbidden();
    }

    public function test_teacher_can_open_own_video(): void
    {
        $this->loginAs($this->teacher)
            ->get("/dashboard/videos/{$this->t1Video->id}")
            ->assertOk();
    }

    public function test_student_cannot_open_other_grade_video(): void
    {
        $this->loginAs($this->student)
            ->get("/dashboard/videos/{$this->t2Video->id}")
            ->assertForbidden();
    }

    public function test_student_can_open_own_grade_video(): void
    {
        $this->loginAs($this->student)
            ->get("/dashboard/videos/{$this->t1Video->id}")
            ->assertOk();
    }

    public function test_parent_can_follow_up_any_video(): void
    {
        $this->loginAs($this->parent)
            ->get("/dashboard/videos/{$this->t2Video->id}")
            ->assertOk();
    }

    public function test_teacher_cannot_delete_other_teacher_video(): void
    {
        $this->loginAs($this->teacher)
            ->delete("/dashboard/videos/{$this->t2Video->id}", [], $this->ajaxHeaders())
            ->assertForbidden();

        $this->assertDatabaseHas('videos', ['id' => $this->t2Video->id]);
    }
}
