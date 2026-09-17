<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LiveMessage;
use Illuminate\Http\Request;

class LiveController extends Controller
{
    // بدء بث (مدرس)
    public function start(Course $course)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $course->update(['is_live' => true, 'live_started_at' => now(), 'live_url' => route('admin.live.room', $course->id)]);

        return redirect()->route('admin.live.room', $course->id);
    }

    // غرفة البث (عرض + شات polling)
    public function room(Course $course)
    {
        $messages = $course->liveMessages()->with('author:id,name')->latest()->limit(30)->get()->reverse()->values();

        return view('dashboard.live.room', compact('course', 'messages'));
    }

    // إيقاف
    public function stop(Course $course)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $course->update(['is_live' => false]);

        return redirect()->route('admin.courses.show', $course->id)->with('success', __('تم إنهاء البث'));
    }

    // إرسال رسالة شات
    public function message(Request $request, Course $course)
    {
        $request->validate(['message' => ['required', 'string', 'max:500']]);
        $msg = LiveMessage::create(['course_id' => $course->id, 'admin_id' => auth('admin')->id(), 'message' => strip_tags($request->message)]);
        $msg->load('author:id,name');
        broadcast(new \App\Events\LiveMessageSent($msg))->toOthers();

        return response()->json(['message' => $msg]);
    }

    // تحديث الشات
    public function feed(Course $course, Request $request)
    {
        $after = (int) $request->get('after', 0);
        $messages = LiveMessage::where('course_id', $course->id)->where('id', '>', $after)->with('author:id,name')->orderBy('id')->limit(50)->get();

        return response()->json($messages);
    }
}
