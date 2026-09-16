<?php

namespace App\Services\Dashboard;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\StatusRepositoryInterface;

class StatusService
{
    protected $statusRepository;

    public function __construct(StatusRepositoryInterface $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public function index()
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $grouped = $this->statusRepository->allGrouped();
        $colors = ['primary' => __('أزرق'), 'info' => __('سماوي'), 'success' => __('أخضر'), 'warning' => __('برتقالي'), 'danger' => __('أحمر'), 'dark' => __('داكن')];

        return view('dashboard.statuses.index', compact('grouped', 'colors'));
    }

    public function update(Request $request, Status $status)
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'in:primary,info,success,warning,danger,dark'],
        ]);

        $this->statusRepository->update($status, $data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تحديث الحالة'), 'url' => route('admin.statuses.index')]);
        }

        return redirect()->back()->with('success', __('تم تحديث الحالة'));
    }
}
