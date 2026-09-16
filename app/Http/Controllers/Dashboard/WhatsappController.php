<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\WhatsappService;

class WhatsappController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsappService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index(Request $request)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);

        $query = Admin::whereNotNull('phone')->orderBy('name');
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        $users = $query->get(['id', 'name', 'phone', 'type']);

        $templates = \App\Models\WhatsappTemplate::orderBy('id')->get()->pluck('body', 'title')->toArray();

        $prefillUser = $request->get('user');
        $prefillMsg = $request->get('message', '');

        $gateway = null;
        if (config('services.whatsapp.provider') === 'gateway') {
            $gateway = app(WhatsappService::class)->gatewayStatus();
        }

        return view('dashboard.whatsapp.index', compact('users', 'templates', 'prefillUser', 'prefillMsg', 'gateway'));
    }

    public function logs(Request $request)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);

        $query = \App\Models\WhatsappLog::with(['recipient:id,name', 'sender:id,name'])
            ->orderByDesc('id');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20);

        return view('dashboard.whatsapp.logs', compact('logs'));
    }

    public function send(Request $request)
    {        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);

        $data = $request->validate([
            'admin_ids' => ['required', 'array', 'min:1'],
            'admin_ids.*' => ['exists:admins,id'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $sent = 0;
        $failed = 0;
        foreach (Admin::whereIn('id', $data['admin_ids'])->get() as $user) {
            // رسالة محددة لو السبب عدم وجود مفتاح CallMeBot
            if (config('services.whatsapp.provider') === 'callmebot'
                && empty($user->whatsapp_key)
                && empty(config('services.whatsapp.callmebot_apikey'))) {
                $failed++;
                continue;
            }

            $this->whatsapp->send($user->phone ?? '', $data['message'], $user->whatsapp_key ?? '', $user->id)
                ? $sent++ : $failed++;
        }

        $message = __('تم الإرسال إلى') . " {$sent}" . ($failed ? ' — ' . __('فشل') . ": {$failed}" : '');

        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->back()->with($failed && !$sent ? 'error_message' : 'success', $message);
    }
}
