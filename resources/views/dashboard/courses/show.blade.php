@extends('dashboard.partials.master')
@section('content')
<div class="d-flex align-items-center gap-3 mb-7"><a href="{{ route('admin.courses.index') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-arrow-right fs-2"></i> {{ __('الحصص') }}</a><h2 class="fw-bold mb-0">{{ $course->title }}</h2></div>
<div class="card"><div class="card-body p-7">
<p class="text-gray-700 fs-6">{{ $course->description }}</p>
<div class="d-flex flex-wrap gap-5 mt-5 pt-5 border-top text-muted fs-7">
<span>{{ __($course->grade) }}</span><span>{{ $course->scheduled_at?->format('Y-m-d H:i') }}</span><span>{{ $course->teacher->name ?? '' }}</span>
@if($course->price > 0)<span class="badge badge-light-success">{{ $course->price }} ج / {{ __('حصة') }}</span>@endif
</div>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<div class="d-flex gap-3 mt-6 flex-wrap"><a href="{{ route('admin.courses.edit',$course->id) }}" class="btn btn-light-primary">{{ __('تعديل') }}</a>
<a href="{{ route('admin.attendance.mark',$course->id) }}" class="btn btn-light-success"><i class="ki-outline ki-clipboard fs-4"></i> {{ __('تسجيل الحضور') }}</a>
@if($course->is_live)<a href="{{ route('admin.live.room',$course->id) }}" class="btn btn-danger"><i class="ki-outline ki-video fs-4"></i> {{ __('دخول البث') }}</a>
@else<form method="POST" action="{{ route('admin.live.start',$course->id) }}">@csrf<button class="btn btn-light-info"><i class="ki-outline ki-broadcast fs-4"></i> {{ __('بدء بث مباشر') }}</button></form>@endif
<form action="{{ route('admin.courses.destroy',$course->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" class="btn btn-light-danger">{{ __('حذف') }}</button></form></div>
@elseif(auth('admin')->user()->type==='student')
<div class="d-flex gap-3 mt-6 flex-wrap">
@if($course->is_live)<a href="{{ route('admin.live.room',$course->id) }}" class="btn btn-danger"><i class="ki-outline ki-video fs-4"></i> {{ __('انضم للبث المباشر') }}</a>@endif
@if($course->qr_token)<a href="{{ route('admin.attendance.scan',$course->qr_token) }}" class="btn btn-light-success"><i class="ki-outline ki-qr fs-4"></i> {{ __('تسجيل حضوري') }}</a>@endif
</div>
@endif
</div></div>

{{-- كل ما يخص الحصة --}}
<div class="row g-6 g-xl-9 mt-2">
<div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title fw-bold">{{ __('واجب الحصة') }}</h3>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))<span class="card-toolbar"><a href="{{ route('admin.assignments.create') }}?course_id={{ $course->id }}" class="btn btn-sm btn-light-primary"><i class="ki-outline ki-plus fs-4"></i></a></span>@endif</div>
<div class="card-body py-5">@forelse($course->assignments as $a)<a href="{{ route('admin.assignments.show',$a->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light"><span class="symbol symbol-35px"><span class="symbol-label bg-light-success"><i class="ki-outline ki-file fs-4 text-success"></i></span></span><span class="fw-bold text-gray-800 flex-grow-1">{{ $a->title }}</span><span class="text-muted fs-8">{{ $a->due_date?->format('m-d') ?? '' }}</span></a>@empty<p class="text-muted fs-7 mb-0">{{ __('لا واجب مرتبط') }}</p>@endforelse</div></div></div>
<div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title fw-bold">{{ __('امتحان الحصة') }}</h3>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))<span class="card-toolbar"><a href="{{ route('admin.exams.create') }}?course_id={{ $course->id }}" class="btn btn-sm btn-light-primary"><i class="ki-outline ki-plus fs-4"></i></a></span>@endif</div>
<div class="card-body py-5">@forelse($course->exams as $e)<a href="{{ route('admin.exams.show',$e->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light"><span class="symbol symbol-35px"><span class="symbol-label bg-light-danger"><i class="ki-outline ki-clipboard fs-4 text-danger"></i></span></span><span class="fw-bold text-gray-800 flex-grow-1">{{ $e->title }}</span><span class="text-muted fs-8">{{ $e->exam_date?->format('m-d') ?? '' }}</span></a>@empty<p class="text-muted fs-7 mb-0">{{ __('لا امتحان مرتبط') }}</p>@endforelse</div></div></div>
<div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title fw-bold">{{ __('الحضور') }}</h3>
<span class="card-toolbar"><a href="{{ route('admin.attendance.mark',$course->id) }}" class="btn btn-sm btn-light">{{ __('التفاصيل') }}</a></span></div>
<div class="card-body py-5"><div class="d-flex gap-6 text-center justify-content-center">
<div><div class="fs-2 fw-bolder text-success">{{ $attStats['present'] ?? 0 }}</div><div class="fs-8 text-muted">{{ __('حاضر') }}</div></div>
<div><div class="fs-2 fw-bolder text-danger">{{ $attStats['absent'] ?? 0 }}</div><div class="fs-8 text-muted">{{ __('غائب') }}</div></div>
<div><div class="fs-2 fw-bolder text-warning">{{ $attStats['late'] ?? 0 }}</div><div class="fs-8 text-muted">{{ __('متأخر') }}</div></div>
</div></div></div></div>
</div>

@if(in_array(auth('admin')->user()->type,['admin','teacher']) && ($invoices ?? collect())->isNotEmpty())
<div class="card mt-7">
    <div class="card-header"><h3 class="card-title fw-bold">{{ __('فواتير شهر الحصة') }} ({{ $month }})</h3><span class="card-toolbar"><span class="text-muted fs-8">{{ __('طلبة نفس الصف') }}</span></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>{{ __('الطالب') }}</th><th>{{ __('المطلوب') }}</th><th>{{ __('المدفوع') }}</th><th>{{ __('المتبقي') }}</th><th>{{ __('الدافع') }}</th></tr></thead>
<tbody>@foreach($invoices as $inv)<tr><td class="fs-7 fw-bold">{{ $inv->student->name ?? '' }}</td><td class="fs-7">{{ $inv->amount }}</td><td class="fs-7 text-success">{{ $inv->paid_amount }}</td><td class="fs-7 text-danger">{{ $inv->remaining }}</td><td class="fs-7">{{ $inv->payer_label }}</td></tr>@endforeach</tbody>
</table></div></div>
</div>
@endif
@endsection
