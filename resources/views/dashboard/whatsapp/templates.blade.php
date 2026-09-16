@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('قوالب الواتساب') }}</h2><p class="text-muted mb-0">{{ __('إضافة وتعديل وحذف القوالب الجاهزة') }}</p></div>
<div class="mt-4 mt-md-0"><a href="{{ route('admin.whatsapp.index') }}" class="btn btn-light">{{ __('صفحة الإرسال') }}</a></div>
</div></div>

<div class="card mb-7"><div class="card-header"><h3 class="card-title">{{ __('قالب جديد') }}</h3></div>
<form action="{{ route('admin.whatsapp.templates.store') }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf
<div class="card-body"><div class="row g-4">
<div class="col-md-4"><label class="form-label required">{{ __('العنوان') }}</label><input type="text" name="title" class="form-control" required /></div>
<div class="col-md-8"><label class="form-label required">{{ __('النص') }}</label><input type="text" name="body" class="form-control" required maxlength="1000" /></div>
</div></div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('إضافة') }}</button></div>
</form></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted"><th>{{ __('العنوان') }}</th><th>{{ __('النص') }}</th><th></th></tr></thead>
<tbody>
@forelse($templates as $t)
<tr>
<td class="fw-bold">{{ $t->title }}</td>
<td class="fs-8">{{ $t->body }}</td>
<td class="text-end">
<div class="d-flex gap-2 justify-content-end">
<form action="{{ route('admin.whatsapp.templates.update',$t->id) }}" method="POST" class="ajax-form d-flex gap-2" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<input type="hidden" name="title" value="{{ $t->title }}" />
<input type="text" name="body" class="form-control form-control-sm" value="{{ $t->body }}" maxlength="1000" style="min-width:220px" />
<button class="btn btn-sm btn-light-primary">{{ __('حفظ') }}</button>
</form>
<form action="{{ route('admin.whatsapp.templates.destroy',$t->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger" onclick="return confirm('{{ __('هل أنت متأكد؟') }}')">{{ __('حذف') }}</button></form>
</div>
</td>
</tr>
@empty
<tr><td colspan="3" class="text-center text-muted py-10">{{ __('لا توجد قوالب') }}</td></tr>
@endforelse
</tbody>
</table></div></div></div>
@endsection
