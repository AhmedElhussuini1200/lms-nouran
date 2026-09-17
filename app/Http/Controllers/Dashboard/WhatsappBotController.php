<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


// استقبال الواجبات عبر واتساب: يطابق رقم الهاتف مع طالب ثم يسجّل تسليمة
class WhatsappBotController extends Controller
{
    // webhook من البوابة (بدون auth — يتحقق بمفتاح البوابة)
    public function inbound(Request $request, WhatsappService $whatsapp)
    {
        if ($request->header('x-api-key') !== config('services.whatsapp.gateway_key') && app()->isProduction()) {
            abort(401);
        }

        $request->validate([
            'phone' => ['required', 'string'],
            'text' => ['nullable', 'string'],
            'media_url' => ['nullable', 'string'],
            'assignment_id' => ['nullable', 'integer'],
        ]);

        $phone = ltrim(preg_replace('/\D/', '', $request->phone), '0');
        $student = Admin::where('type', 'student')
            ->where(function ($q) use ($phone, $request) {
                $q->where('phone', 'like', "%{$phone}%")->orWhere('phone', $request->phone);
            })->first();

        if (! $student) {
            return response()->json(['ok' => false, 'error' => 'student_not_found'], 404);
        }

        $assignment = $request->assignment_id
            ? Assignment::find($request->assignment_id)
            : Assignment::where('grade', $student->grade)->latest()->first();

        if (! $assignment) {
            return response()->json(['ok' => false, 'error' => 'no_assignment'], 422);
        }

        $filePath = null;
        if ($request->media_url) {
            try {
                $contents = file_get_contents($request->media_url);
                $filePath = 'whatsapp_hw/' . $student->id . '_' . time() . '.jpg';
                Storage::disk('public')->put($filePath, $contents);
            } catch (\Throwable) {
            }
        }

        $submission = AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $student->id],
            ['submission_text' => $request->text ?? '', 'file_path' => $filePath ?? '', 'submitted_at' => now(), 'source' => 'whatsapp']
        );

        notifyAdmin($assignment->teacher_id, __('تسليمة واتساب جديدة'), $student->name . ' - ' . $assignment->title, 'success', route('admin.assignments.show', $assignment->id));
        $whatsapp->send($request->phone, __('تم استلام واجبك بنجاح') . ' ✅ - ' . $assignment->title, '', $student->id);

        return response()->json(['ok' => true, 'submission_id' => $submission->id]);
    }
}
