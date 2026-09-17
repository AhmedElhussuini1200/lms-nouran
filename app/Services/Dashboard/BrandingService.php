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
        $me = auth('admin')->user();
        // المدرس يدير هويته الخاصة — الأدمن يدير الهوية العامة
        if ($me->type === 'teacher') {
            return view('dashboard.branding.teacher', ['teacher' => $me]);
        }
        abort_unless($me->type === 'admin', 403);
        $branding = $this->brandingRepository->all();
        return view('dashboard.branding.index', compact('branding'));
    }

    public function update(Request $request)
    {
        $me = auth('admin')->user();

        // هوية المدرس الخاصة + باقته الشهرية (نفس الفورم يحفظ الكل — نفرق بالحقول الموجودة)
        if ($me->type === 'teacher') {
            $request->validate([
                'brand_name' => ['nullable', 'string', 'max:255'],
                'brand_primary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'brand_secondary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'brand_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
                'monthly_classes' => ['nullable', 'integer', 'min:1', 'max:31'],
                'price_per_class' => ['nullable', 'numeric', 'min:0'],
                'pay_methods' => ['nullable', 'array'],
                'pay_methods.*' => ['in:cash,vodafone,instapay,card'],
                'pay_details' => ['nullable', 'string', 'max:1000'],
            ]);
            $data = array_filter($request->only(['brand_name', 'brand_primary', 'brand_secondary', 'monthly_classes', 'price_per_class', 'pay_details']), fn ($v) => $v !== null);
            if ($request->has('pay_methods')) {
                $data['pay_methods'] = implode(',', $request->pay_methods);
            }
            if ($request->hasFile('brand_logo')) {
                $data['brand_logo'] = 'storage/' . $request->file('brand_logo')->store('branding', 'public');
            }
            $me->update($data);

            if ($request->ajax()) {
                return response()->json(['message' => __('تم الحفظ بنجاح')]);
            }

            return redirect()->back()->with('success', __('تم الحفظ بنجاح'));
        }

        abort_unless($me->type === 'admin', 403);
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
