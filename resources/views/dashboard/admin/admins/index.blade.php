@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('إدارة المستخدمين') }}</h2><p class="text-muted mb-0">{{ __('مدرسين • طلاب • أولياء أمور') }}</p></div>
<div class="mt-4 mt-md-0 d-flex gap-2">
<form method="GET" action="{{ route('admin.admins.index') }}" class="d-flex gap-2">
<input type="text" name="q" class="form-control w-auto" placeholder="{{ __('بحث') }}" value="{{ request('q') }}" />
<select data-placeholder="{{ __('النوع') }}" data-control="select2" name="type" class="form-select w-auto" onchange="this.form.submit()">
<option value="all">{{ __('كل الأنواع') }}</option>
@foreach($types as $k=>$v)<option value="{{ $k }}" {{ request('type')==$k?'selected':'' }}>{{ $v }}</option>@endforeach
</select>
<select data-placeholder="{{ __('الصف الدراسي') }}" data-control="select2" name="grade" class="form-select w-auto" onchange="this.form.submit()">
@foreach($grades as $k=>$v)<option value="{{ $k }}" {{ (string)request('grade')===(string)$k?'selected':'' }}>{{ $v }}</option>@endforeach
</select>
<button class="btn btn-light">{{ __('بحث') }}</button>
</form>
<a href="{{ route('admin.admins.create') }}" class="btn btn-primary"><i class="ki-outline ki-plus fs-2"></i> {{ __('إضافة') }}</a>
</div></div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted"><th>{{ __('الاسم') }}</th><th>{{ __('النوع') }}</th><th>{{ __('الصف / المادة') }}</th><th>{{ __('التواصل') }}</th><th>{{ __('الحالة') }}</th><th></th></tr></thead>
<tbody>
@forelse($admins as $a)
<tr>
<td class="fw-bold">{{ $a->name }}<div class="text-muted fw-normal fs-8">{{ $a->email }}</div></td>
<td><span class="badge badge-light-primary">{{ $types[$a->type] ?? $a->type }}</span></td>
<td class="fs-8">{{ $a->grade ? __($a->grade) : '—' }} @if($a->subject)<span class="badge badge-light-info ms-1">{{ $a->subject }}</span>@endif</td>
<td class="text-muted fs-8">{{ $a->phone ?? '—' }}</td>
<td>@if($a->is_blocked)<span class="badge badge-light-danger">{{ __('محظور') }}</span>@else<span class="badge badge-light-success">{{ __('نشط') }}</span>@endif</td>
<td class="text-end"><a href="{{ route('admin.admins.show',$a->id) }}" class="btn btn-sm btn-light-primary">{{ __('عرض') }}</a></td>
</tr>
@empty<tr><td colspan="6" class="text-center text-muted py-10">{{ __('لا يوجد مستخدمون') }}</td></tr>@endforelse
</tbody>
</table></div></div></div>
<div class="mt-7 d-flex justify-content-center">{{ $admins->appends(request()->query())->links() }}</div>
@endsection
