@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">
<h3 class="card-title fw-bold"><i class="ki-outline ki-medal-star fs-2 me-2"></i>{{ __('لوحة الصدارة') }}</h3>
<form method="GET" class="d-flex gap-2"><select name="grade" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
@foreach($grades as $k=>$v)<option value="{{ $k }}" @selected($grade==$k)>{{ $v }}</option>@endforeach</select></form>
</div>
<div class="card-body p-0">
<div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>#</th><th>{{ __('الطالب') }}</th><th>{{ __('مجموع الدرجات') }}</th><th>{{ __('النقاط') }}</th><th>{{ __('الإجمالي') }}</th></tr></thead>
<tbody>
@foreach($board as $i=>$s)
<tr class="{{ $i<3?'bg-light-warning':'' }}"><td class="fw-bolder">{{ $i+1 }}</td>
<td class="fw-bold">{{ $s->name }}</td><td>{{ $s->total_marks ?? 0 }}</td><td>{{ $s->total_points }}</td><td class="fw-bolder">{{ $s->score }}</td></tr>
@endforeach
</tbody>
</table></div>
</div>
</div>
@endsection
