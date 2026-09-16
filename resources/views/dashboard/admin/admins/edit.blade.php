@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('تعديل') }}: {{ $admin->name }}</h3><div class="card-toolbar"><a href="{{ route('admin.admins.show', $admin->id) }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.admins.update', $admin->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<div class="card-body"><div class="row g-6">
<div class="col-md-6"><label class="form-label required">{{ __('الاسم') }}</label><input type="text" name="name" class="form-control" required value="{{ old('name', $admin->name) }}" /></div>
<div class="col-md-6"><label class="form-label required">{{ __('البريد') }}</label><input type="email" name="email" class="form-control" required dir="ltr" value="{{ old('email', $admin->email) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('الهاتف') }}</label><input type="text" name="phone" class="form-control" dir="ltr" value="{{ old('phone', $admin->phone) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('مفتاح الواتساب (CallMeBot)') }}</label><input type="text" name="whatsapp_key" class="form-control" dir="ltr" value="{{ old('whatsapp_key', $admin->whatsapp_key ?? '') }}" /></div>
<div class="col-md-4"><label class="form-label required">{{ __('النوع') }}</label><select data-placeholder="{{ __('النوع') }}" data-control="select2" name="type" class="form-select" required>@foreach($types as $k=>$v)<option value="{{ $k }}" {{ old('type', $admin->type)==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('الصف') }}</label><select name="grade" data-control="select2" data-placeholder="الصف الدراسي" class="form-select"><option value="">{{ __('اختر') }}</option>@foreach($grades as $k=>$v)<option value="{{ $k }}" {{ old('grade', $admin->grade)==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">{{ __('المادة (للمدرس)') }}</label><input type="text" name="subject" class="form-control" value="{{ old('subject', $admin->subject) }}" /></div>
<div class="col-md-3"><label class="form-label">{{ __('كلمة المرور (اتركها فارغة للإبقاء)') }}</label><input type="password" name="password" class="form-control" /></div>
<div class="col-md-3"><label class="form-label">{{ __('تأكيدها') }}</label><input type="password" name="password_confirmation" class="form-control" /></div>
@if($roles->isNotEmpty())
@php $mine = $admin->roles->pluck('id')->toArray(); @endphp
<div class="col-12"><label class="form-label">{{ __('الأدوار') }}</label>
<div class="d-flex flex-wrap gap-3">
@foreach($roles as $r)
<label class="d-flex align-items-center gap-2 border rounded px-3 py-2">
<input type="checkbox" name="roles[]" value="{{ $r->id }}" class="form-check-input m-0" {{ in_array($r->id,$mine)?'checked':'' }} />
<span>{{ $r->name_ar }}</span>
</label>
@endforeach
</div></div>
@endif
@if($admin->type === 'parent')
<div class="col-12"><label class="form-label">{{ __('الأبناء') }}</label>
<select data-placeholder="{{ __('الأبناء') }}" data-control="select2" name="children[]" class="form-select" multiple size="5">
@foreach($students as $s)<option value="{{ $s->id }}" {{ $admin->students->contains($s->id) ? 'selected' : '' }}>{{ $s->name }} - {{ __($s->grade ?? '') }}</option>@endforeach
</select></div>
@endif
</div></div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ التعديلات') }}</button></div>
</form></div>
@endsection
