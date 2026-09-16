@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body py-6 px-6">
<h2 class="fw-bold mb-2">{{ __('مرحباً،') }} {{ auth('admin')->user()->name }}</h2>
<p class="text-muted mb-0">{{ __('متابعة لحظية لمستوى أبنائك: الواجبات الناقصة + متوسط الدرجات') }}</p>
</div></div>

<div class="row g-6 g-xl-9 mb-7">
@foreach([['label'=>__('الأبناء'),'v'=>$stats['students'],'c'=>'primary'],['label'=>__('الواجبات'),'v'=>$stats['total_assignments'],'c'=>'success'],['label'=>__('الامتحانات'),'v'=>$stats['total_exams'],'c'=>'warning'],['label'=>__('متوسط الدرجات'),'v'=>$stats['average_marks'],'c'=>'danger']] as $s)
<div class="col-sm-6 col-xl-3"><div class="card card-flush h-100"><div class="card-body py-5 px-6">
<div class="fs-6 fw-bold text-gray-800">{{ $s['label'] }}</div><div class="fs-2 fw-bolder text-{{ $s['c'] }}">{{ $s['v'] }}</div>
</div></div></div>
@endforeach
</div>

<div class="row g-6 g-xl-9">@foreach($studentProgress as $p)
<div class="col-xl-6"><div class="card card-flush h-100">
<div class="card-header"><h3 class="card-title">{{ $p['student']->name }} <span class="badge badge-light-info ms-2">{{ __($p['student']->grade ?? '') }}</span></h3>
<span class="badge badge-light-{{ ($p['average'] ?? 0) >= 50 ? 'success' : 'warning' }}">{{ __('المتوسط') }}: {{ $p['average'] ?? '—' }}</span></div>
<div class="card-body py-5">
<div class="fw-bold mb-3">{{ __('واجبات ناقصة') }} ({{ $p['missing']->count() }})</div>
@forelse($p['missing'] as $m)<a href="{{ route('admin.assignments.show',$m->id) }}" class="d-block border rounded p-3 mb-2 fs-8"><span class="fw-bold">{{ $m->title }}</span><span class="text-muted"> • {{ $m->due_date?->format('Y-m-d') ?? '' }}</span></a>@empty<p class="text-success fs-8">{{ __('مسلّم كل واجباته') }}</p>@endforelse
</div></div></div>
@endforeach</div>
@endsection
