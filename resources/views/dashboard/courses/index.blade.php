@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('الحصص الدراسية') }}</h2><p class="text-muted mb-0">{{ __('جدول الحصص حسب الصف الدراسي') }}</p></div>
<div class="mt-4 mt-md-0 d-flex gap-3">
<form method="GET" action="{{ route('admin.courses.index') }}"><select data-placeholder="{{ __('الصف الدراسي') }}" data-control="select2" name="grade" class="form-select w-auto" onchange="this.form.submit()">@foreach($grades as $k=>$v)<option value="{{ $k }}" {{ request('grade')==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select>@if(auth('admin')->user()->type==='admin' && isset($teachers))<select data-placeholder="{{ __('مدرس') }}" data-control="select2" name="teacher" class="form-select w-auto" onchange="this.form.submit()"><option value="all">{{ __('كل المدرسين') }}</option>@foreach($teachers as $t)<option value="{{ $t->id }}" {{ request('teacher')==$t->id?'selected':'' }}>{{ $t->name }}{{ $t->subject ? ' - '.$t->subject : '' }}</option>@endforeach</select>@endif</form>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))<a href="{{ route('admin.courses.create') }}" class="btn btn-primary"><i class="ki-outline ki-plus fs-2"></i><span class="ms-2">{{ __('إضافة حصة') }}</span></a>@endif
<a href="{{ route('admin.courses.calendar') }}" class="btn btn-light-info"><i class="ki-outline ki-calendar fs-2"></i><span class="ms-2">{{ __('التقويم') }}</span></a>
</div></div></div>
<div class="row g-6 g-xl-9">@forelse($courses as $course)<div class="col-sm-6 col-xl-4"><div class="card card-flush h-100"><div class="card-body p-6">
<a href="{{ route('admin.courses.show',$course->id) }}" class="fs-5 fw-bold text-gray-900 text-hover-primary d-block mb-2">{{ $course->title }}</a>
<p class="text-muted fs-7 mb-4">{{ \Illuminate\Support\Str::limit($course->description,90) }}</p>
<div class="d-flex justify-content-between align-items-center"><span class="badge badge-light-primary">{{ __($course->grade) }}</span><span class="text-muted fs-8">{{ $course->scheduled_at?->format('Y-m-d H:i') ?? '' }}</span></div>
<div class="mt-4 pt-4 border-top text-muted fs-8">{{ $course->teacher->name ?? '' }}</div>
</div></div></div>@empty<div class="col-12"><div class="card"><div class="card-body text-center text-muted py-10">{{ __('لا توجد حصص') }}</div></div></div>@endforelse</div>
@if(method_exists($courses,'links'))<div class="mt-7 d-flex justify-content-center">{{ $courses->appends(request()->query())->links() }}</div>@endif
@endsection
