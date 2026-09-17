<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Course;
use App\Models\StudentPoint;
use App\Models\Video;
use App\Models\VideoProgress;
use Illuminate\Http\Request;

class EngagementController extends Controller
{
    // حفظ تقدم المشاهدة (يُنادى كل 15 ثانية من الواجهة)
    public function progress(Request $request, Video $video)
    {
        $request->validate([
            'watched_seconds' => ['required', 'integer', 'min:0'],
            'duration' => ['nullable', 'integer', 'min:1'],
        ]);

        $user = auth('admin')->user();
        abort_unless($user->type === 'student', 403);

        $duration = $request->input('duration', $video->duration_seconds ?: 0);
        $percent = $duration > 0 ? (int) min(100, round($request->watched_seconds / $duration * 100)) : 0;
        $completed = $percent >= 90;

        $progress = VideoProgress::updateOrCreate(
            ['video_id' => $video->id, 'student_id' => $user->id],
            ['watched_seconds' => $request->watched_seconds, 'percent' => $percent, 'completed' => $completed]
        );

        // نقاط أول إكمال فقط
        if ($completed && ! StudentPoint::where('student_id', $user->id)->where('reason', 'video_complete')->where('source_id', $video->id)->where('source_type', Video::class)->exists()) {
            StudentPoint::create([
                'student_id' => $user->id, 'points' => 5, 'reason' => 'video_complete',
                'source_type' => Video::class, 'source_id' => $video->id,
            ]);
        }

        return response()->json(['percent' => $percent, 'completed' => $completed]);
    }

    // قائمة التعليقات
    public function comments(Request $request, string $type, int $id)
    {
        $model = $this->resolve($type, $id);
        $comments = $model->comments()->with('author:id,name')->latest()->limit(50)->get();

        return response()->json($comments);
    }

    // إضافة تعليق
    public function storeComment(Request $request, string $type, int $id)
    {
        $request->validate(['body' => ['required', 'string', 'max:1000']]);
        $model = $this->resolve($type, $id);

        $comment = $model->comments()->create([
            'admin_id' => auth('admin')->id(),
            'body' => strip_tags($request->body),
        ]);

        // نقطة تفاعل (مرة واحدة يومياً كحد أقصى لمنع السبام)
        $user = auth('admin')->user();
        if ($user->type === 'student' && ! StudentPoint::where('student_id', $user->id)->where('reason', 'comment')->whereDate('created_at', today())->exists()) {
            StudentPoint::create(['student_id' => $user->id, 'points' => 2, 'reason' => 'comment']);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => __('تمت إضافة التعليق'), 'comment' => $comment->load('author:id,name')]);
        }

        return back()->with('success', __('تمت إضافة التعليق'));
    }

    protected function resolve(string $type, int $id)
    {
        return match ($type) {
            'video' => Video::findOrFail($id),
            'course' => Course::findOrFail($id),
            default => abort(404),
        };
    }
}
