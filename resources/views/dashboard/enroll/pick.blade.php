@extends('dashboard.partials.master')
@section('content')
<div class="text-center py-8">
<h2 class="fw-bold text-gray-900 mb-2">{{ __('هتحضر عند مين النهاردة؟') }}</h2>
<p class="text-muted fs-6">{{ __('اختار المادة — كل حاجة هتتظبط عليها: الحصص والواجبات والامتحانات والفيديوهات') }}</p>
</div>
<div class="row g-6 justify-content-center">
@foreach($teachers as $t)
<div class="col-md-6 col-xl-4">
<form method="POST" action="{{ route('admin.enroll.choose') }}">@csrf
<input type="hidden" name="teacher_id" value="{{ $t->id }}" />
<button type="submit" class="card card-flush w-100 text-start p-0 overflow-hidden border-hover-primary" style="cursor:pointer;border-top:4px solid {{ $t->brand_primary ?? '#1b84ff' }} !important">
<span class="card-body d-flex align-items-center gap-4 p-6">
<span class="symbol symbol-50px"><span class="symbol-label fs-3 fw-bold" style="background:{{ ($t->brand_primary ?? '#1b84ff') }}1a;color:{{ $t->brand_primary ?? '#1b84ff' }}">{{ mb_substr($t->brand_name ?? $t->name, 0, 1) }}</span></span>
<span><span class="d-block fw-bolder text-gray-900 fs-4">{{ $t->brand_name ?? $t->name }}</span>
<span class="d-block text-muted fs-7 mt-1">{{ $t->subject ?? '' }} • {{ $t->monthly_classes }} {{ __('حصة') }} / {{ __('شهر') }} • {{ $t->price_per_class }} {{ __('ج') }}</span></span>
<i class="ki-outline ki-arrow-left fs-2 text-muted ms-auto"></i>
</span>
</button>
</form>
</div>
@endforeach
</div>
@endsection
