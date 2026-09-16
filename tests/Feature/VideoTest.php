<?php

namespace Tests\Feature;

use App\Models\Video;

class VideoTest extends LmsTestCase
{
    public function test_teacher_can_create_video_and_url_normalized_to_nocookie(): void
    {
        $response = $this->loginAs($this->teacher)->postJson('/dashboard/videos', [
            'title' => 'شرح جديد',
            'grade' => '1_secondary',
            'video_url' => 'https://www.youtube.com/watch?v=abc123XYZ_-',
        ], $this->ajaxHeaders());

        $response->assertOk()->assertJsonStructure(['message', 'url']);
        $this->assertDatabaseHas('videos', [
            'video_url' => 'https://www.youtube-nocookie.com/embed/abc123XYZ_-?rel=0',
        ]);
    }

    public function test_duplicate_video_url_rejected(): void
    {
        Video::create([
            'title' => 'A', 'grade' => '1_secondary',
            'teacher_id' => $this->teacher->id, 'video_url' => 'https://example.com/dup',
        ]);

        $this->loginAs($this->teacher)->postJson('/dashboard/videos', [
            'title' => 'B', 'grade' => '1_secondary', 'video_url' => 'https://example.com/dup',
        ], $this->ajaxHeaders())->assertStatus(422)->assertJsonValidationErrors('video_url');
    }

    public function test_watch_and_embed_same_video_counts_as_duplicate(): void
    {
        Video::create([
            'title' => 'A', 'grade' => '1_secondary',
            'teacher_id' => $this->teacher->id,
            'video_url' => 'https://www.youtube-nocookie.com/embed/abc123XYZ_-?rel=0',
        ]);

        $this->loginAs($this->teacher)->postJson('/dashboard/videos', [
            'title' => 'B', 'grade' => '1_secondary',
            'video_url' => 'https://www.youtube.com/watch?v=abc123XYZ_-',
        ], $this->ajaxHeaders())->assertStatus(422);
    }

    public function test_student_cannot_create_video(): void
    {
        $this->loginAs($this->student)->postJson('/dashboard/videos', [
            'title' => 'X', 'grade' => '1_secondary', 'video_url' => 'https://example.com/x',
        ], $this->ajaxHeaders())->assertStatus(403);
    }

    public function test_show_increments_views(): void
    {
        $video = Video::create([
            'title' => 'V', 'grade' => '1_secondary',
            'teacher_id' => $this->teacher->id, 'video_url' => 'https://example.com/v',
        ]);

        $this->loginAs($this->student)->get("/dashboard/videos/{$video->id}")->assertOk();
        $this->assertEquals(1, $video->fresh()->views_count);
    }
}
