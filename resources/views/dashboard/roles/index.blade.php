@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('الأدوار والصلاحيات') }}</h2><p class="text-muted mb-0">{{ __('تحكم في صلاحيات كل دور') }}</p></div>
<div class="mt-4 mt-md-0"><a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="ki-outline ki-plus fs-2"></i> {{ __('دور جديد') }}</a></div>
</div></div>

<div class="row g-6 g-xl-9">@forelse($roles as $role)
<div class="col-md-6 col-xl-4"><div class="card card-flush h-100">
<div class="card-body p-6">
<div class="d-flex justify-content-between align-items-center mb-3">
<h3 class="fw-bold mb-0">{{ $role->name_ar }} <span class="text-muted fs-8">({{ $role->name_en }})</span></h3>
<span class="badge badge-light-primary">{{ $role->abilities_count }} {{ __('صلاحية') }}</span>
</div>
<div class="text-muted fs-8 mb-4">{{ $role->admins_count }} {{ __('مستخدم') }}</div>
<div class="d-flex gap-2">
<a href="{{ route('admin.roles.edit',$role->id) }}" class="btn btn-sm btn-light-primary flex-fill">{{ __('الصلاحيات') }}</a>
<form action="{{ route('admin.roles.destroy',$role->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger" onclick="return confirm('{{ __('هل أنت متأكد؟') }}')">{{ __('حذف') }}</button></form>
</div>
</div></div></div>
@empty<div class="col-12"><div class="card"><div class="card-body text-center text-muted py-10">{{ __('لا توجد أدوار') }}</div></div></div>@endforelse</div>
<div class="mt-7 d-flex justify-content-center">{{ $roles->links() }}</div>
@endsection
