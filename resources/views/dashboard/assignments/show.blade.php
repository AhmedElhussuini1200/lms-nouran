@extends('dashboard.partials.master')
@section('content')
<div class="d-flex align-items-center gap-3 mb-7"><a href="{{ route('admin.assignments.index') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-arrow-right fs-2"></i> {{ __('الواجبات') }}</a><h2 class="fw-bold mb-0">{{ $assignment->title }}</h2></div>
<div class="row g-6">
<div class="col-xl-8">
<div class="card mb-6"><div class="card-body p-7">
<p class="text-gray-700">{{ $assignment->description }}</p>
<div class="d-flex flex-wrap gap-5 mt-5 pt-5 border-top text-muted fs-7"><span>{{ __($assignment->grade) }}</span><span>{{ __('التسليم') }}: {{ $assignment->due_date?->format('Y-m-d') ?? '—' }}</span><span>{{ __('الدرجة') }}: {{ $assignment->total_marks }}</span><span>{{ $assignment->teacher->name ?? '' }}</span></div>
@if($assignment->file_path)<div class="mt-4"><a href="{{ asset($assignment->file_path) }}" target="_blank" class="btn btn-light-info btn-sm"><i class="ki-outline ki-file fs-2"></i> {{ __('تحميل المرفق') }}</a></div>@endif
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<div class="d-flex gap-3 mt-6"><a href="{{ route('admin.assignments.edit',$assignment->id) }}" class="btn btn-light-primary">{{ __('تعديل') }}</a>
<form action="{{ route('admin.assignments.destroy',$assignment->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" class="btn btn-light-danger">{{ __('حذف') }}</button></form></div>
@endif
</div></div>
@if(auth('admin')->user()->type==='student')
<div class="card"><div class="card-header"><h3 class="card-title">{{ __('تسليم الواجب') }} (PDF)</h3>
@if($mySubmission?->status)<span class="badge badge-light-{{ $mySubmission->status->color ?? 'info' }}">{{ $mySubmission->status->name_ar }}</span>@endif</div>
<form action="{{ route('admin.assignments.submit',$assignment->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess" enctype="multipart/form-data">@csrf
<div class="card-body"><div class="mb-4"><label class="form-label">{{ __('إجابتك') }}</label><textarea name="submission_text" class="form-control" rows="4">{{ old('submission_text',$mySubmission->submission_text ?? '') }}</textarea></div>
<div><label class="form-label">{{ __('ملف PDF') }}</label><input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png,.webp" /></div>
@if($mySubmission?->file_path && str_ends_with(strtolower($mySubmission->file_path), '.pdf'))
<div class="mt-4"><label class="form-label">{{ __('معاينة تسليمك') }}</label><iframe src="{{ asset($mySubmission->file_path) }}" class="w-100 rounded border" style="height:400px"></iframe></div>
@endif
@if($mySubmission)<div class="alert alert-{{ ($mySubmission->status?->slug ?? '') === 'graded' ? 'success' : 'info' }} mt-4">{{ __('الحالة') }}: <b>{{ $mySubmission->status->name_ar ?? __('تم التسليم') }}</b> • {{ $mySubmission->submitted_at?->diffForHumans() }} @if(!is_null($mySubmission->marks)) • {{ __('درجتك') }}: <b>{{ $mySubmission->marks }}</b> @endif @if($mySubmission->teacher_feedback)<div class="mt-1">{{ __('ملاحظة المعلم') }}: {{ $mySubmission->teacher_feedback }}</div>@endif</div>@endif
</div><div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('تسليم PDF') }}</button></div></form></div>
@endif
</div>
<div class="col-xl-4">
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<div class="card"><div class="card-header"><h3 class="card-title">{{ __('التسليمات') }} ({{ $assignment->submissions->count() }})</h3></div>
<div class="card-body py-5">@forelse($assignment->submissions as $s)
<div class="border rounded p-4 mb-4">
<div class="d-flex justify-content-between align-items-center"><div class="fw-bold">{{ $s->student->name ?? '' }}</div>
@if($s->status)<span class="badge badge-light-{{ $s->status->color ?? 'info' }}">{{ $s->status->name_ar }}</span>@endif</div>
<div class="text-muted fs-8">{{ $s->submitted_at?->diffForHumans() }}</div>
@if($s->submission_text)<p class="fs-8 mt-2 mb-2">{{ \Illuminate\Support\Str::limit($s->submission_text,120) }}</p>@endif
@if($s->file_path)
<div class="d-flex gap-2 mt-2"><a href="{{ asset($s->file_path) }}" target="_blank" class="btn btn-sm btn-light-info">{{ __('فتح الملف') }}</a>
@if(!($s->status?->slug === 'under_review' || $s->status?->slug === 'graded'))
<form action="{{ route('admin.submissions.start-review',$s->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf<button class="btn btn-sm btn-light-warning">{{ __('بدء التصحيح') }}</button></form>
@endif</div>
@if(str_ends_with(strtolower($s->file_path), '.pdf'))<iframe src="{{ asset($s->file_path) }}" class="w-100 rounded border mt-2" style="height:350px"></iframe>@endif
@endif
<form action="{{ route('admin.submissions.grade',$s->id) }}" method="POST" class="ajax-form mt-3" data-success-callback="onAjaxSuccess">@csrf
<div class="d-flex gap-2"><input type="number" name="marks" class="form-control form-control-sm" placeholder="{{ __('الدرجة') }}" value="{{ $s->marks }}" min="0" max="{{ $assignment->total_marks }}" />
<select data-placeholder="{{ __('الحالة') }}" data-control="select2" name="status" class="form-select form-select-sm w-auto">
<option value="under_review" {{ $s->status?->slug==='under_review'?'selected':'' }}>{{ __('قيد التصحيح') }}</option>
<option value="graded" {{ $s->status?->slug==='graded'?'selected':'' }}>{{ __('تم التصحيح') }}</option>
<option value="returned" {{ $s->status?->slug==='returned'?'selected':'' }}>{{ __('راجع للتعديل') }}</option>
</select>
<button class="btn btn-sm btn-primary">{{ __('رصد') }}</button></div>
<input type="text" name="teacher_feedback" class="form-control form-control-sm mt-2" placeholder="{{ __('ملاحظة المعلم (تظهر للطالب وولي الأمر)') }}" value="{{ $s->teacher_feedback }}" />
</form>
</div>@empty<p class="text-muted">{{ __('لا توجد تسليمات') }}</p>@endforelse</div></div>
@endif
</div>
</div>
@endsection
