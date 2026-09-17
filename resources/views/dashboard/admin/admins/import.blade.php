@extends('dashboard.partials.master')
@section('content')
<div class="card mx-auto" style="max-width:640px">
<div class="card-header"><h3 class="card-title fw-bold"><i class="ki-outline ki-file-up fs-2 me-2"></i>{{ __('رفع شيت الطلبة') }}</h3>
<span class="card-toolbar"><a href="{{ route('admin.admins.template') }}" class="btn btn-sm btn-light-primary"><i class="ki-outline ki-file-down fs-4"></i> {{ __('تحميل القالب') }}</a></span></div>
<div class="card-body">
<div class="notice d-flex bg-light rounded p-4 mb-5">
<i class="ki-outline ki-information-5 fs-2 text-primary me-3"></i>
<div class="fs-7 text-gray-700">{{ __('الأعمدة') }}: <code dir="ltr">name | email | phone | grade | password | teacher_emails | parent_name | parent_email | parent_phone</code><br />{{ __('الصف') }}: 1 / 2 / 3 — {{ __('الإيميل الموجود بيتحدث بدل ما يتكرر') }} — {{ __('teacher_emails ببريد المدرسين مفصولة بفاصلة') }}</div>
</div>
<form method="POST" action="{{ route('admin.admins.import-store') }}" enctype="multipart/form-data">@csrf
<input type="file" name="file" class="form-control mb-4" accept=".xlsx,.xls,.csv" required />
<button class="btn btn-primary w-100"><i class="ki-outline ki-file-up fs-4"></i> {{ __('رفع واستيراد') }}</button>
</form>
</div>
</div>
@endsection
