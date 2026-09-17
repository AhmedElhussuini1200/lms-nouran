@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('تعديل الواجب') }}</h3><div class="card-toolbar"><a href="{{ route('admin.assignments.show',$assignment->id) }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.assignments.update',$assignment->id) }}" method="POST" enctype="multipart/form-data" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<div class="card-body"><div class="row g-6">
<div class="col-md-8"><label class="form-label required">{{ __('العنوان') }}</label><input type="text" name="title" class="form-control" required value="{{ old('title',$assignment->title) }}" /></div>
<div class="col-md-4"><label class="form-label required">{{ __('الصف') }}</label><select name="grade" data-control="select2" data-placeholder="الصف الدراسي" class="form-select" required>@foreach($grades as $k=>$v)<option value="{{ $k }}" {{ old('grade',$assignment->grade)==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('الحصة (اختياري)') }}</label><select name="course_id" data-control="select2" data-placeholder="{{ __('بدون حصة') }}" class="form-select"><option value="">{{ __('بدون حصة') }}</option>@foreach($courses ?? [] as $c)<option value="{{ $c->id }}" {{ old('course_id',$assignment->course_id)==$c->id?'selected':'' }}>{{ $c->title }} ({{ __($c->grade) }})</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('المادة') }}</label><input type="text" name="subject" class="form-control" value="{{ old('subject',$assignment->subject ?? '') }}" placeholder="فيزياء" /></div>
<div class="col-md-4"><label class="form-label">{{ __('تاريخ التسليم') }}</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date',$assignment->due_date?->format('Y-m-d')) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('الدرجة العظمى') }}</label><input type="number" name="total_marks" class="form-control" min="0" value="{{ old('total_marks',$assignment->total_marks) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('ملف جديد') }}</label><input type="file" name="file" class="form-control" /></div>
<div class="col-12"><label class="form-label">{{ __('الوصف') }}</label><textarea name="description" class="form-control" rows="4">{{ old('description',$assignment->description) }}</textarea></div>
</div></div>
<div class="card-footer d-flex justify-content-end gap-3"><button class="btn btn-primary">{{ __('حفظ التعديلات') }}</button></div>
</form></div>
@endsection
