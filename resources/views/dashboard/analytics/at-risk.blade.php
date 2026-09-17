@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
<h3 class="card-title fw-bold"><i class="ki-outline ki-shield-cross fs-2 me-2"></i>{{ __('الطلاب المتعثرون') }}</h3>
<form method="POST" action="{{ route('admin.analytics.alert') }}">@csrf<button class="btn btn-sm btn-warning"><i class="ki-outline ki-message-text fs-4"></i> {{ __('تنبيه أولياء الأمور واتساب') }}</button></form>
</div>
<div class="card-body p-0">
<div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>{{ __('الطالب') }}</th><th>{{ __('المتوسط') }}</th><th>{{ __('غياب 30 يوم') }}</th><th>{{ __('الخطر') }}</th><th></th></tr></thead>
<tbody>
@foreach($students as $s)
<tr><td class="fw-bold">{{ $s->name }}</td><td>{{ round($s->avg,1) }}</td><td>{{ $s->absences }}</td>
<td>@if($s->risk=='high')<span class="badge badge-light-danger">{{ __('عالي') }}</span>@elseif($s->risk=='medium')<span class="badge badge-light-warning">{{ __('متوسط') }}</span>@else<span class="badge badge-light-success">{{ __('مستقر') }}</span>@endif</td>
<td class="text-end"><a class="btn btn-sm btn-light" href="{{ route('admin.analytics.parent',$s->id) }}">{{ __('تقرير ولي الأمر') }}</a></td></tr>
@endforeach
</tbody>
</table></div>
</div>
</div>
@endsection
