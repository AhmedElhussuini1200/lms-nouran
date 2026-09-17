<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    \Log::info('Auth channel check', ['user' => $user->id, 'channel' => $userId]);
    return (int) $user->id === (int) $userId;
});
Broadcast::channel('chat.user.{user1Id}.user.{user2Id}', function ($user, $user1Id, $user2Id) {
    \Log::info('Trying to auth user: ' . $user->id, ['allowed' => [$user1Id, $user2Id]]);
    return in_array((int)$user->id, [(int)$user1Id, (int)$user2Id]);
});

// قناة البث المباشر للحصة: أي مسجل دخول من نفس الصف أو المدرس
Broadcast::channel('live.course.{courseId}', function ($user, $courseId) {
    $course = \App\Models\Course::find($courseId);
    if (! $course) {
        return false;
    }
    if (in_array($user->type, ['admin', 'teacher'])) {
        return true;
    }

    return $user->type === 'student' && $user->grade === $course->grade;
});
