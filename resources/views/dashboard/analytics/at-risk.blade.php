@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header d-flex justify-content-between align-items-center">
<h3 class="card-title">⚠️ {{ __('الطلاب المتعثرون') }}</h3>
<form method="POST" action="{{ route('admin.analytics.alert') }}">@csrf<button class="btn btn-sm btn-warning">{{ __('تنبيه أولياء الأمور واتساب') }}</button></form></div>
<div class="card-body"><table class="table"><thead><tr><th>{{ __('الطالب') }}</th><th>{{ __('المتوسط') }}</th><th>{{ __('غياب 30 يوم') }}</th><th>{{ __('الخطر') }}</th><th></th></tr></thead>
<tbody>@foreach($students as $s)<tr><td>{{ $s->name }}</td><td>{{ round($s->avg,1) }}</td><td>{{ $s->absences }}</td>
<td>@if($s->risk=='high')<span class="badge badge-light-danger">🔴 {{ __('عالي') }}</span>@elseif($s->risk=='medium')<span class="badge badge-light-warning">🟡 {{ __('متوسط') }}</span>@else<span class="badge badge-light-success">🟢 {{ __('مستقر') }}</span>@endif</td>
<td><a class="btn btn-sm btn-light" href="{{ route('admin.analytics.parent',$s->id) }}">{{ __('تقرير ولي الأمر') }}</a></td></tr>@endforeach</tbody></table></div></div>
@endsection
