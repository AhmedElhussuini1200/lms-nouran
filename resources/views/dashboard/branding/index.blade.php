@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header"><h3 class="card-title fw-bold">{{ __('الهوية البصرية') }}</h3></div>
<form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<div class="card-body"><div class="row g-6">
<div class="col-md-6"><label class="form-label required">{{ __('اسم المنصة') }}</label><input type="text" name="site_name" class="form-control" required value="{{ old('site_name', $branding['site_name']) }}" /></div>
<div class="col-md-3"><label class="form-label required">{{ __('اللون الأساسي') }}</label><input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ old('primary_color', $branding['primary_color']) }}" /></div>
<div class="col-md-3"><label class="form-label required">{{ __('اللون الثانوي') }}</label><input type="color" name="secondary_color" class="form-control form-control-color w-100" value="{{ old('secondary_color', $branding['secondary_color']) }}" /></div>
<div class="col-md-6"><label class="form-label">{{ __('اللوجو') }}</label><input type="file" name="logo" class="form-control" accept="image/*" />
@if(!empty($branding['logo']))<div class="mt-3"><img src="{{ asset($branding['logo']) }}" class="h-60px rounded border" /></div>@endif
</div>
<div class="col-md-6"><label class="form-label">{{ __('معاينة') }}</label>
<div class="border rounded p-5" style="border-top: 4px solid {{ $branding['primary_color'] }} !important">
<span class="badge" style="background: {{ $branding['primary_color'] }}">{{ $branding['site_name'] }}</span>
<span class="badge" style="background: {{ $branding['secondary_color'] }}">{{ __('تميز') }}</span>
</div></div>
</div></div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ الهوية') }}</button></div>
</form></div>
@endsection
