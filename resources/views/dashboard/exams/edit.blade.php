@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('تعديل الامتحان') }}</h3><div class="card-toolbar"><a href="{{ route('admin.exams.show',$exam->id) }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.exams.update',$exam->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<div class="card-body"><div class="row g-6">
<div class="col-md-8"><label class="form-label required">{{ __('العنوان') }}</label><input type="text" name="title" class="form-control" required value="{{ old('title',$exam->title) }}" /></div>
<div class="col-md-4"><label class="form-label required">{{ __('الصف') }}</label><select name="grade" data-control="select2" data-placeholder="الصف الدراسي" class="form-select" required>@foreach($grades as $k=>$v)<option value="{{ $k }}" {{ old('grade',$exam->grade)==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('الحصة (اختياري)') }}</label><select name="course_id" data-control="select2" data-placeholder="{{ __('بدون حصة') }}" class="form-select"><option value="">{{ __('بدون حصة') }}</option>@foreach($courses ?? [] as $c)<option value="{{ $c->id }}" {{ old('course_id',$exam->course_id)==$c->id?'selected':'' }}>{{ $c->title }} ({{ __($c->grade) }})</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('المادة') }}</label><input type="text" name="subject" class="form-control" value="{{ old('subject',$exam->subject ?? '') }}" placeholder="فيزياء" /></div>
<div class="col-md-4"><label class="form-label">{{ __('موعد الامتحان') }}</label><input type="datetime-local" name="exam_date" class="form-control" value="{{ old('exam_date',$exam->exam_date?->format('Y-m-d\TH:i')) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('المدة (دقيقة)') }}</label><input type="number" name="duration_minutes" class="form-control" min="1" value="{{ old('duration_minutes',$exam->duration_minutes) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('الدرجة العظمى') }}</label><input type="number" name="total_marks" class="form-control" min="0" value="{{ old('total_marks',$exam->total_marks) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('درجة النجاح') }}</label><input type="number" name="passing_marks" class="form-control" min="0" step="0.5" value="{{ old('passing_marks',$exam->passing_marks) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('عدد المحاولات') }}</label><input type="number" name="max_attempts" class="form-control" min="1" max="10" value="{{ old('max_attempts',$exam->max_attempts ?? 1) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('بداية النافذة') }}</label><input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at',$exam->starts_at?->format('Y-m-d\TH:i')) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('نهاية النافذة') }}</label><input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at',$exam->ends_at?->format('Y-m-d\TH:i')) }}" /></div>
<div class="col-md-4 d-flex gap-5 align-items-end pb-3">
<label class="form-check form-switch"><input type="checkbox" name="shuffle_questions" value="1" class="form-check-input" @checked(old('shuffle_questions',$exam->shuffle_questions)) /><span class="form-check-label">{{ __('خلط الأسئلة') }}</span></label>
<label class="form-check form-switch"><input type="checkbox" name="anti_cheat" value="1" class="form-check-input" @checked(old('anti_cheat',$exam->anti_cheat)) /><span class="form-check-label">{{ __('منع الغش') }}</span></label>
</div>
<div class="col-12"><label class="form-label">{{ __('الوصف') }}</label><textarea name="description" class="form-control" rows="4">{{ old('description',$exam->description) }}</textarea></div>
</div></div>
<div class="card-footer d-flex justify-content-end gap-3"><button class="btn btn-primary">{{ __('حفظ التعديلات') }}</button></div>
</form></div>
@endsection
