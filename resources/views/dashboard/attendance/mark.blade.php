@extends('dashboard.partials.master')
@section('content')
<div class="card mb-6"><div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
<div><h3 class="card-title">{{ __('تسجيل الحضور') }} — {{ $course->title }}</h3>
<p class="text-muted mb-0">{{ $date }} • {{ count($students) }} {{ __('طالب') }}</p></div>
<div class="d-flex gap-2 align-items-center">
@if($course->qr_token)
<a href="{{ route('admin.attendance.scan', $course->qr_token) }}" class="btn btn-sm btn-light-success"><i class="ki-outline ki-qr fs-4"></i> {{ __('رابط الحضور الذاتي QR') }}</a>
<code class="fs-8">{{ route('admin.attendance.scan', $course->qr_token) }}</code>
@endif
@if($course->is_live)<a href="{{ route('admin.live.room', $course->id) }}" class="btn btn-sm btn-danger"><i class="ki-outline ki-video fs-4"></i> {{ __('غرفة البث') }}</a>
@else<form method="POST" action="{{ route('admin.live.start', $course->id) }}">@csrf<button class="btn btn-sm btn-primary"><i class="ki-outline ki-broadcast fs-4"></i> {{ __('بدء بث') }}</button></form>@endif
</div></div>
<form method="POST" action="{{ route('admin.attendance.store', $course->id) }}">@csrf
<div class="card-body">
<input type="hidden" name="date" value="{{ $date }}" />
<table class="table"><thead><tr><th>{{ __('الطالب') }}</th><th>{{ __('حاضر') }}</th><th>{{ __('غائب') }}</th><th>{{ __('متأخر') }}</th></tr></thead>
<tbody>@foreach($students as $s)<tr><td>{{ $s->name }}</td>
@php $cur = $marked[$s->id] ?? 'present'; @endphp
@foreach(['present'=>__('حاضر'),'absent'=>__('غائب'),'late'=>__('متأخر')] as $k=>$v)
<td><input type="radio" name="status[{{ $s->id }}]" value="{{ $k }}" @checked($cur==$k) /> {{ $v }}</td>@endforeach
</tr>@endforeach</tbody></table>
</div><div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ الحضور') }}</button></div></form></div>
@endsection
