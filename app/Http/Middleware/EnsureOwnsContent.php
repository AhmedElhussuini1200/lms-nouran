<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Question;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Auth;

/**
 * عزل المدرسين: كل مدرس يشوف ويدير المحتوى بتاعه بس.
 * - admin: كل الصلاحيات
 * - teacher: المحتوى اللي teacher_id بتاعه فقط
 * - student: محتوى الصف (grade) بتاعه فقط
 * - parent: متابعة قراءة فقط (مفتوح)
 */
class EnsureOwnsContent
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        if (!$user || $user->type === 'admin' || $user->type === 'parent') {
            return $next($request);
        }

        $model = $request->route('video')
            ?? $request->route('course')
            ?? $request->route('assignment')
            ?? $request->route('exam')
            ?? $request->route('question')
            ?? $request->route('submission')
            ?? $request->route('result');

        // index/create/store: مفيش موديل — الفلترة في الـ Repository
        if (!$model) {
            return $next($request);
        }

        $content = match (true) {
            $model instanceof Question => $model->exam,
            $model instanceof AssignmentSubmission => $model->assignment,
            $model instanceof ExamResult => $model->exam,
            default => $model,
        };

        if ($user->type === 'teacher') {
            abort_if($content->teacher_id !== $user->id, 403, __('غير مصرح لك'));
        }

        if ($user->type === 'student') {
            abort_if(($content->grade ?? null) !== $user->grade, 403, __('غير مصرح لك'));
        }

        return $next($request);
    }
}
