<?php

namespace App\Services\Dashboard;

use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\BrandingRepositoryInterface;

class BrandingService
{
    protected $brandingRepository;

    public function __construct(BrandingRepositoryInterface $brandingRepository)
    {
        $this->brandingRepository = $brandingRepository;
    }

    public function index()
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
        $branding = $this->brandingRepository->all();
        return view('dashboard.branding.index', compact('branding'));
    }

    public function update(Request $request)
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        $data = $request->only(['site_name', 'primary_color', 'secondary_color']);

        if ($request->hasFile('logo')) {
            $data['logo'] = 'storage/' . $request->file('logo')->store('branding', 'public');
        }

        $branding = $this->brandingRepository->update($data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حفظ الهوية البصرية بنجاح')]);
        }

        return redirect()->back()->with('success', __('تم حفظ الهوية البصرية بنجاح'));
    }
}
