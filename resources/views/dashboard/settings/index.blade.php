@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body py-6 px-6">
<h2 class="fw-bold mb-2">{{ __('Settings') }}</h2>
<p class="text-muted mb-0">{{ __('إدارة إعدادات المنصة من مكان واحد') }}</p>
</div></div>

<div class="row g-6 g-xl-9">
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('admin.branding.index') }}" class="card card-flush h-100 text-hover-primary">
            <div class="card-body p-6 d-flex align-items-center gap-4">
                <span class="symbol symbol-50px"><span class="symbol-label bg-light-primary"><i class="ki-outline ki-paintbucket fs-2x text-primary"></i></span></span>
                <span><span class="d-block fw-bold fs-5 text-gray-900">{{ __('الهوية البصرية') }}</span>
                <span class="d-block text-muted fs-7">{{ __('اسم المنصة • اللوجو • الألوان') }}</span></span>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('admin.statuses.index') }}" class="card card-flush h-100 text-hover-primary">
            <div class="card-body p-6 d-flex align-items-center gap-4">
                <span class="symbol symbol-50px"><span class="symbol-label bg-light-warning"><i class="ki-outline ki-flag fs-2x text-warning"></i></span></span>
                <span><span class="d-block fw-bold fs-5 text-gray-900">{{ __('جدول الحالات') }}</span>
                <span class="d-block text-muted fs-7">{{ __('ألوان وأسماء الحالات') }}</span></span>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('admin.admins.profile-info') }}" class="card card-flush h-100 text-hover-primary">
            <div class="card-body p-6 d-flex align-items-center gap-4">
                <span class="symbol symbol-50px"><span class="symbol-label bg-light-success"><i class="ki-outline ki-profile-circle fs-2x text-success"></i></span></span>
                <span><span class="d-block fw-bold fs-5 text-gray-900">{{ __('Profile') }}</span>
                <span class="d-block text-muted fs-7">{{ __('بيانات حسابك وكلمة المرور') }}</span></span>
            </div>
        </a>
    </div>
</div>
@endsection
