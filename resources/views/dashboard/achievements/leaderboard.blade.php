@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header d-flex justify-content-between align-items-center">
<h3 class="card-title">🏆 {{ __('لوحة الصدارة') }}</h3>
<form method="GET" class="d-flex gap-2"><select name="grade" class="form-select form-select-sm" onchange="this.form.submit()">
@foreach($grades as $k=>$v)<option value="{{ $k }}" @selected($grade==$k)>{{ $v }}</option>@endforeach</select></form></div>
<div class="card-body"><table class="table"><thead><tr><th>#</th><th>{{ __('الطالب') }}</th><th>{{ __('مجموع الدرجات') }}</th><th>{{ __('النقاط') }}</th><th>{{ __('الإجمالي') }}</th></tr></thead>
<tbody>@foreach($board as $i=>$s)<tr class="{{ $i<3?'fw-bold table-warning':'' }}"><td>{{ $i+1 }} @if($i==0)🥇 @elseif($i==1)🥈 @elseif($i==2)🥉 @endif</td>
<td>{{ $s->name }}</td><td>{{ $s->total_marks ?? 0 }}</td><td>{{ $s->total_points }}</td><td>{{ $s->score }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
