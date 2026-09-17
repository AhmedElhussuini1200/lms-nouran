@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header"><h3 class="card-title fw-bold">🎨 {{ __('هويتك كمدرس') }}</h3></div>
<div class="card-body"><p class="text-muted">{{ __('المنصة كلها هتظهر باسمك وألوانك وانت داخل — وكل مدرس ليه هويته') }}</p></div>
<form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<div class="card-body"><div class="row g-6">
<div class="col-md-6"><label class="form-label required">{{ __('اسمك المعروض') }}</label><input type="text" name="brand_name" class="form-control" required placeholder="{{ __('مثال: مستر عصام سمكة — رياضيات') }}" value="{{ old('brand_name', $teacher->brand_name ?? $teacher->name) }}" /></div>
<div class="col-md-3"><label class="form-label required">{{ __('اللون الأساسي') }}</label><input type="color" name="brand_primary" class="form-control form-control-color w-100" value="{{ old('brand_primary', $teacher->brand_primary ?? '#1b84ff') }}" /></div>
<div class="col-md-3"><label class="form-label required">{{ __('اللون الثانوي') }}</label><input type="color" name="brand_secondary" class="form-control form-control-color w-100" value="{{ old('brand_secondary', $teacher->brand_secondary ?? '#17c653') }}" /></div>
<div class="col-md-6"><label class="form-label">{{ __('اللوجو') }}</label><input type="file" name="brand_logo" class="form-control" accept="image/*" />
@if(!empty($teacher->brand_logo))<div class="mt-3"><img src="{{ asset($teacher->brand_logo) }}" class="h-60px rounded border" /></div>@endif
</div>
<div class="col-md-6"><label class="form-label">{{ __('معاينة') }}</label>
<div class="border rounded p-5" style="border-top: 4px solid {{ $teacher->brand_primary ?? '#1b84ff' }} !important">
<span class="badge" style="background: {{ $teacher->brand_primary ?? '#1b84ff' }}">{{ $teacher->brand_name ?? $teacher->name }}</span>
<span class="badge" style="background: {{ $teacher->brand_secondary ?? '#17c653' }}">{{ __('تميز') }}</span>
</div></div>
</div></div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ هويتي') }}</button></div>
</form>

{{-- باقة الدفع الشهرية --}}
<div class="card mt-6">
<div class="card-header"><h3 class="card-title fw-bold">💰 {{ __('باقة الدفع الشهرية') }}</h3></div>
<div class="card-body"><p class="text-muted">{{ __('الطلبة وأولياء الأمور هيشوفوا: المبلغ + يدفعوا لمين + على أي طريقة') }} — {{ __('الشهري') }} = {{ __('عدد الحصص') }} × {{ __('سعر الحصة') }}</p></div>
<form action="{{ route('admin.branding.update') }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<div class="card-body"><div class="row g-6">
<div class="col-md-4"><label class="form-label">{{ __('عدد حصص الشهر') }}</label><input type="number" name="monthly_classes" class="form-control" min="1" max="31" value="{{ old('monthly_classes', $teacher->monthly_classes ?? 8) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('سعر الحصة (ج)') }}</label><input type="number" name="price_per_class" class="form-control" min="0" step="0.5" value="{{ old('price_per_class', $teacher->price_per_class ?? 0) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('الإجمالي الشهري') }}</label><input class="form-control" disabled value="{{ ($teacher->monthly_classes ?? 8) * ($teacher->price_per_class ?? 0) }} ج" /></div>
<div class="col-12"><label class="form-label">{{ __('طرق الدفع المتاحة') }}</label>
<div class="d-flex gap-4 flex-wrap">@foreach(['cash'=>__('كاش'),'vodafone'=>__('فودافون كاش'),'instapay'=>__('انستاباي'),'card'=>__('كارت')] as $k=>$v)
<label class="form-check"><input type="checkbox" name="pay_methods[]" value="{{ $k }}" class="form-check-input" @checked(in_array($k, explode(',', $teacher->pay_methods ?? 'cash'))) /> <span class="form-check-label">{{ $v }}</span></label>@endforeach</div></div>
<div class="col-12"><label class="form-label">{{ __('بيانات الدفع (أرقام المحافظ اللي يحولوا عليها)') }}</label><textarea name="pay_details" class="form-control" rows="2" dir="ltr" placeholder="Vodafone: 010xxxxxxxx">{{ old('pay_details', $teacher->pay_details) }}</textarea></div>
</div></div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ الباقة') }}</button></div>
</form>
</div>
</div>
@endsection
