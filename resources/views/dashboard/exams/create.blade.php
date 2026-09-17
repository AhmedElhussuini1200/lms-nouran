@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('إضافة امتحان') }}</h3><div class="card-toolbar"><a href="{{ route('admin.exams.index') }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.exams.store') }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf
<div class="card-body"><div class="row g-6">
<div class="col-md-8"><label class="form-label required">{{ __('العنوان') }}</label><input type="text" name="title" class="form-control" required /></div>
<div class="col-md-4"><label class="form-label required">{{ __('الصف') }}</label><select name="grade" data-control="select2" data-placeholder="الصف الدراسي" class="form-select" required>@foreach($grades as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('المادة') }}</label><input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="فيزياء" /></div>
<div class="col-md-4"><label class="form-label">{{ __('موعد الامتحان') }}</label><input type="datetime-local" name="exam_date" class="form-control" /></div>
<div class="col-md-4"><label class="form-label">{{ __('المدة (دقيقة)') }}</label><input type="number" name="duration_minutes" class="form-control" min="1" /></div>
<div class="col-md-4"><label class="form-label">{{ __('الدرجة العظمى') }}</label><input type="number" name="total_marks" class="form-control" min="0" value="100" /></div>
<div class="col-md-4"><label class="form-label">{{ __('درجة النجاح (للشهادة التلقائية)') }}</label><input type="number" name="passing_marks" class="form-control" min="0" step="0.5" /></div>
<div class="col-md-4"><label class="form-label">{{ __('عدد المحاولات') }}</label><input type="number" name="max_attempts" class="form-control" min="1" max="10" value="1" /></div>
<div class="col-md-4"><label class="form-label">{{ __('بداية النافذة') }}</label><input type="datetime-local" name="starts_at" class="form-control" /></div>
<div class="col-md-4"><label class="form-label">{{ __('نهاية النافذة') }}</label><input type="datetime-local" name="ends_at" class="form-control" /></div>
<div class="col-md-4 d-flex gap-5 align-items-end pb-3">
<label class="form-check form-switch"><input type="checkbox" name="shuffle_questions" value="1" class="form-check-input" /><span class="form-check-label">{{ __('خلط الأسئلة') }}</span></label>
<label class="form-check form-switch"><input type="checkbox" name="anti_cheat" value="1" class="form-check-input" /><span class="form-check-label">{{ __('منع الغش') }}</span></label>
</div>
<div class="col-12"><label class="form-label">{{ __('الوصف / التعليمات') }}</label><textarea name="description" class="form-control" rows="4"></textarea></div>
</div></div>
<div class="card-footer d-flex justify-content-end gap-3"><button class="btn btn-primary">{{ __('حفظ') }}</button></div>
</form></div>
@endsection
