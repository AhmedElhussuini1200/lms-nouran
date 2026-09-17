@extends('dashboard.partials.master')
@include('dashboard.partials.design-system')
@section('content')
<div class="text-center py-8">
<h2 class="fw-bold mb-2">👨‍🏫 {{ __('هتحضر عند مين النهاردة؟') }}</h2>
<p class="text-muted">{{ __('اختار المادة — كل حاجة هتتظبط عليها: الحصص والواجبات والامتحانات والفيديوهات') }}</p>
</div>
<div class="row g-5 justify-content-center">
@foreach($teachers as $t)
<div class="col-md-6 col-xl-4">
<form method="POST" action="{{ route('admin.enroll.choose') }}">@csrf
<input type="hidden" name="teacher_id" value="{{ $t->id }}" />
<button type="submit" class="stat w-100 text-start" style="border-top: 4px solid {{ $t->brand_primary ?? '#1b84ff' }}; cursor: pointer;">
<span class="stat__icon"><i class="ki-outline ki-teacher"></i></span>
<span><span class="stat__value fs-4">{{ $t->brand_name ?? $t->name }}</span>
<span class="stat__label">{{ $t->subject ?? '' }} • {{ $t->monthly_classes }} {{ __('حصة') }}/{{ __('شهر') }} • {{ $t->price_per_class }} ج</span></span>
</button>
</form>
</div>
@endforeach
</div>
@endsection
