@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('إضافة حصة جديدة') }}</h3><div class="card-toolbar"><a href="{{ route('admin.courses.index') }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.courses.store') }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf
<div class="card-body"><div class="row g-6">
<div class="col-md-8"><label class="form-label required">{{ __('العنوان') }}</label><input type="text" name="title" class="form-control" required value="{{ old('title') }}" /></div>
<div class="col-md-4"><label class="form-label required">{{ __('الصف') }}</label><select name="grade" data-control="select2" data-placeholder="الصف الدراسي" class="form-select" required>@foreach($grades as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('المادة') }}</label><input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="فيزياء" /></div>
<div class="col-md-6"><label class="form-label">{{ __('موعد الحصة') }}</label><input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}" /></div>
<div class="col-md-6"><label class="form-label">{{ __('سعر الحصة (جنيه)') }}</label><input type="number" name="price" class="form-control" min="0" step="0.01" value="{{ old('price', 0) }}" /></div>
<div class="col-12"><label class="form-label">{{ __('الوصف') }}</label><textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea></div>
</div></div>
<div class="card-footer d-flex justify-content-end gap-3"><a href="{{ route('admin.courses.index') }}" class="btn btn-light">{{ __('إلغاء') }}</a><button class="btn btn-primary">{{ __('حفظ') }}</button></div>
</form></div>
@endsection
