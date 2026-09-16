@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body py-6 px-6">
<h2 class="fw-bold mb-2">{{ __('جدول الحالات') }}</h2>
<p class="text-muted mb-0">{{ __('كل حالة بلونها — اللون هو اللي يميز الحالة في كل الشاشات') }}</p>
</div></div>

@foreach(['submission' => __('حالات التسليم والتصحيح'), 'payment' => __('حالات المدفوعات')] as $scope => $title)
<div class="card mb-7">
<div class="card-header"><h3 class="card-title fw-bold">{{ $title }}</h3></div>
<div class="card-body p-0"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted"><th>{{ __('الحالة') }}</th><th>{{ __('المعاينة') }}</th><th>{{ __('الاسم عربي') }}</th><th>{{ __('الاسم إنجليزي') }}</th><th>{{ __('اللون') }}</th><th></th></tr></thead>
<tbody>
@foreach($grouped[$scope] ?? [] as $status)
<tr>
<td><span class="bullet bullet-dot h-15px w-15px bg-{{ $status->color }} me-2"></span><code>{{ $status->slug }}</code></td>
<td><span class="badge badge-light-{{ $status->color }} fs-7">{{ $status->name_ar }}</span></td>
<td colspan="4">
@if(auth('admin')->user()->type === 'admin')
<form action="{{ route('admin.statuses.update', $status->id) }}" method="POST" class="ajax-form d-flex flex-wrap gap-2 align-items-center" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<input type="text" name="name_ar" class="form-control form-control-sm w-auto" value="{{ $status->name_ar }}" required />
<input type="text" name="name_en" class="form-control form-control-sm w-auto" value="{{ $status->name_en }}" placeholder="EN" />
<select data-placeholder="{{ __('اللون') }}" data-control="select2" name="color" class="form-select form-select-sm w-auto">
@foreach($colors as $c => $label)<option value="{{ $c }}" {{ $status->color == $c ? 'selected' : '' }}>{{ $label }}</option>@endforeach
</select>
<button class="btn btn-sm btn-primary">{{ __('حفظ') }}</button>
</form>
@else
<span class="fw-bold">{{ $status->name_ar }}</span>
@endif
</td>
</tr>
@endforeach
</tbody>
</table></div></div>
</div>
@endforeach
@endsection
