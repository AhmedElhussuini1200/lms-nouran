<?php

namespace App\Services\Dashboard;

use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\WhatsappTemplateRepositoryInterface;

class WhatsappTemplateService
{
    protected $templates;

    public function __construct(WhatsappTemplateRepositoryInterface $templates)
    {
        $this->templates = $templates;
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
    }

    public function index()
    {
        $this->authorizeAdmin();
        $templates = $this->templates->all();

        return view('dashboard.whatsapp.templates', compact('templates'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $this->templates->store($data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تمت إضافة القالب بنجاح'), 'url' => route('admin.whatsapp.templates')]);
        }

        return redirect()->route('admin.whatsapp.templates')->with('success', __('تمت إضافة القالب بنجاح'));
    }

    public function update(Request $request, WhatsappTemplate $template)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $this->templates->update($data, $template);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تحديث القالب بنجاح'), 'url' => route('admin.whatsapp.templates')]);
        }

        return redirect()->route('admin.whatsapp.templates')->with('success', __('تم تحديث القالب بنجاح'));
    }

    public function destroy(Request $request, WhatsappTemplate $template)
    {
        $this->authorizeAdmin();
        $this->templates->destroy($template);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حذف القالب بنجاح'), 'url' => route('admin.whatsapp.templates')]);
        }

        return redirect()->route('admin.whatsapp.templates')->with('success', __('تم حذف القالب بنجاح'));
    }
}
