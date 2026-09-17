@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('إضافة مستخدم') }}</h3><div class="card-toolbar"><a href="{{ route('admin.admins.index') }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.admins.store') }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf
<div class="card-body"><div class="row g-6">
<div class="col-md-6"><label class="form-label required">{{ __('الاسم') }}</label><input type="text" name="name" class="form-control" required value="{{ old('name') }}" /></div>
<div class="col-md-6"><label class="form-label required">{{ __('البريد') }}</label><input type="email" name="email" class="form-control" required dir="ltr" value="{{ old('email') }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('الهاتف') }}</label><input type="text" name="phone" class="form-control" dir="ltr" value="{{ old('phone') }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('مفتاح الواتساب (CallMeBot)') }}</label><input type="text" name="whatsapp_key" class="form-control" dir="ltr" value="{{ old('whatsapp_key') }}" placeholder="6 أرقام من CallMeBot" /></div>
<div class="col-md-4"><label class="form-label required">{{ __('النوع') }}</label><select data-placeholder="{{ __('النوع') }}" data-control="select2" name="type" class="form-select" required id="user-type">@foreach($types as $k=>$v)<option value="{{ $k }}" {{ old('type')==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('الصف (للطالب)') }}</label><select name="grade" data-control="select2" data-placeholder="الصف الدراسي" class="form-select"><option value="">{{ __('اختر') }}</option>@foreach($grades as $k=>$v)<option value="{{ $k }}" {{ old('grade')==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">{{ __('المادة (للمدرس)') }}</label><input type="text" name="subject" class="form-control" placeholder="فيزياء" value="{{ old('subject') }}" /></div>
<div class="col-md-6"><label class="form-label">{{ __('اسم العرض (هوية المدرس)') }}</label><input type="text" name="brand_name" class="form-control" placeholder="{{ __('مثال: مستر عصام سمكة — رياضيات') }}" value="{{ old('brand_name') }}" /></div>
<div class="col-md-3"><label class="form-label">{{ __('لون المدرس') }}</label><input type="color" name="brand_primary" class="form-control form-control-color w-100" value="{{ old('brand_primary', '#1b84ff') }}" /></div>
<div class="col-md-3"><label class="form-label">{{ __('لون ثانوي') }}</label><input type="color" name="brand_secondary" class="form-control form-control-color w-100" value="{{ old('brand_secondary', '#17c653') }}" /></div>
<div class="col-md-3"><label class="form-label required">{{ __('كلمة المرور') }}</label><input type="password" name="password" class="form-control" required /></div>
<div class="col-md-3"><label class="form-label required">{{ __('تأكيدها') }}</label><input type="password" name="password_confirmation" class="form-control" required /></div>
@if($roles->isNotEmpty())
<div class="col-12"><label class="form-label">{{ __('الأدوار') }}</label>
<div class="d-flex flex-wrap gap-3">
@foreach($roles as $r)
<label class="d-flex align-items-center gap-2 border rounded px-3 py-2">
<input type="checkbox" name="roles[]" value="{{ $r->id }}" class="form-check-input m-0" />
<span>{{ $r->name_ar }}</span>
</label>
@endforeach
</div></div>
@endif
</div></div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ') }}</button></div>
</form></div>
@endsection
