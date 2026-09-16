@extends('dashboard.partials.master')
@section('content')
<div class="d-flex align-items-center gap-3 mb-7"><a href="{{ route('admin.courses.index') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-arrow-right fs-2"></i> {{ __('الحصص') }}</a><h2 class="fw-bold mb-0">{{ $course->title }}</h2></div>
<div class="card"><div class="card-body p-7">
<p class="text-gray-700 fs-6">{{ $course->description }}</p>
<div class="d-flex flex-wrap gap-5 mt-5 pt-5 border-top text-muted fs-7">
<span>{{ __($course->grade) }}</span><span>{{ $course->scheduled_at?->format('Y-m-d H:i') }}</span><span>{{ $course->teacher->name ?? '' }}</span>
</div>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<div class="d-flex gap-3 mt-6"><a href="{{ route('admin.courses.edit',$course->id) }}" class="btn btn-light-primary">{{ __('تعديل') }}</a>
<form action="{{ route('admin.courses.destroy',$course->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" class="btn btn-light-danger">{{ __('حذف') }}</button></form></div>
@endif
</div></div>
@endsection
