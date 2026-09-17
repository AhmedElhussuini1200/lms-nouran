@extends('dashboard.partials.master')
@include('dashboard.partials.design-system')
@section('content')
@php $riskMap = ['high' => __('خطر'), 'medium' => __('يحتاج متابعة'), 'low' => __('ممتاز')]; @endphp

<div class="hero-lms p-7 mb-7">
    <h2 class="fw-bold text-white mb-1">{{ __('مرحباً،') }} {{ auth('admin')->user()->name }} 👋</h2>
    <p class="text-white opacity-75 mb-0">{{ __('متابعة ذكية لأبنائك: المستوى + المخاطر + الفواتير + مين دفع') }}</p>
</div>

{{-- ============ الأرقام ============ --}}
<section class="sec">
    <div class="sec-head"><span class="sec-head__bar"></span>
        <div><h2 class="sec-head__title">{{ __('نظرة عامة') }}</h2><p class="sec-head__desc">{{ __('ملخص أبنائك') }}</p></div>
    </div>
    <div class="row g-5">
        @foreach([
            ['l' => __('الأبناء'), 'v' => $stats['students'], 'i' => 'profile-user'],
            ['l' => __('المستحق الكلي'), 'v' => number_format($stats['due']) . ' ج', 'i' => 'wallet'],
            ['l' => __('متوسط الأبناء'), 'v' => $stats['avg'] . '%', 'i' => 'chart-line'],
            ['l' => __('تنبيهات خطر'), 'v' => $stats['alerts'], 'i' => 'shield-cross'],
        ] as $s)
        <div class="col-6 col-xl-3"><div class="stat">
            <span class="stat__icon"><i class="ki-outline ki-{{ $s['i'] }}"></i></span>
            <span><span class="stat__value">{{ $s['v'] }}</span><span class="stat__label">{{ $s['l'] }}</span></span>
        </div></div>
        @endforeach
    </div>
</section>

{{-- ============ كل ابن ============ --}}
@foreach($profiles as $p)
@php $st = $p['student']; @endphp
<section class="sec">
    <div class="sec-head"><span class="sec-head__bar"></span>
        <div><h2 class="sec-head__title">{{ $st->name }} <span class="badge badge-light-info ms-2">{{ __($st->grade ?? '') }}</span></h2>
        <p class="sec-head__desc">{{ __('المتوسط') }} {{ $p['avg'] ?? '—' }}% • {{ $p['trend'] === 'up' ? '📈' : ($p['trend'] === 'down' ? '📉' : '➖') }} • <span class="risk-dot risk-{{ $p['risk'] }}"></span> {{ $riskMap[$p['risk']] }}</p></div>
        <a href="{{ route('admin.analytics.parent', $st->id) }}" class="sec-head__aside btn btn-sm btn-light-primary">📄 {{ __('تقرير PDF') }}</a>
    </div>
    <div class="row g-5">
        <div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">📊 {{ __('إتقان المواد') }}</h3></div>
            <div class="card-body py-4">
            @forelse($p['mastery'] as $m)<div class="mastery"><div class="mastery__top"><span class="fw-bold">{{ $m['subject'] }}</span><span class="text-muted">{{ $m['avg'] }}%</span></div>
            <div class="mastery__bar"><span style="width:{{ min(100, $m['avg']) }}%"></span></div></div>
            @empty<p class="text-muted">{{ __('لا درجات بعد') }}</p>@endforelse
            @if(!empty($p['reasons']))<div class="alert alert-light-warning mt-3 mb-0 fs-8">⚠️ {{ implode(' • ', $p['reasons']) }}</div>@endif
            <div class="d-flex gap-4 mt-3 fs-8 text-muted"><span>✅ {{ __('الحضور') }} {{ $p['attRate'] ?? '—' }}%</span><span>🎬 {{ __('الفيديو') }} {{ $p['vidRate'] ?? '—' }}%</span><span>⭐ {{ $p['points'] }}</span></div>
            </div></div></div>
        <div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">📝 {{ __('درجات الامتحانات') }}</h3></div>
            <div class="card-body py-4">@forelse($p['perExam']->take(6) as $x)
            <div class="rec"><span class="rec__txt">{{ $x['exam']->title }}</span>
            <span class="rec__lvl badge badge-light-{{ $x['passed'] ? 'success' : 'danger' }}">{{ $x['pct'] }}%</span></div>
            @empty<p class="text-muted">{{ __('لا نتائج بعد') }}</p>@endforelse</div></div></div>
        <div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">🎯 {{ __('يعمل إيه') }}</h3></div>
            <div class="card-body py-4">@forelse(array_slice($p['recommendations'], 0, 4) as $r)
            <div class="rec"><span class="rec__icon">{{ $r['icon'] }}</span><span class="rec__txt">{{ $r['text'] }}</span></div>
            @empty<p class="text-muted">{{ __('مستواه ممتاز') }}</p>@endforelse</div></div></div>
    </div>
</section>
@endforeach

{{-- ============ الفواتير والدافع ============ --}}
<section class="sec">
    <div class="sec-head"><span class="sec-head__bar"></span>
        <div><h2 class="sec-head__title">💰 {{ __('الفواتير') }}</h2><p class="sec-head__desc">{{ __('الفاتورة باسم الطالب — والدافع متسجل لوحده') }}</p></div>
        <a href="{{ route('admin.payments.index') }}" class="sec-head__aside btn btn-sm btn-light">{{ __('الكل') }}</a>
    </div>
    <div class="card card-flush"><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle gy-3 mb-0">
        <thead><tr class="fw-bold text-muted fs-8"><th>{{ __('الطالب') }}</th><th>{{ __('الشهر') }}</th><th>{{ __('المطلوب') }}</th><th>{{ __('المدفوع') }}</th><th>{{ __('المتبقي') }}</th><th>{{ __('الدافع') }}</th><th></th></tr></thead>
        <tbody>@forelse($invoices as $inv)<tr>
            <td class="fw-bold fs-8">{{ $inv->student->name ?? '' }}</td><td class="fs-8">{{ $inv->month }}</td>
            <td class="fs-8">{{ $inv->amount }}</td><td class="fs-8 text-success">{{ $inv->paid_amount }}</td>
            <td class="fs-8 text-danger">{{ $inv->remaining }}</td>
            <td class="fs-8">{{ $inv->payer_label }}</td>
            <td class="text-end">@if($inv->remaining > 0)
                <form method="POST" action="{{ route('admin.onlinepay.checkout',$inv->id) }}">@csrf<input type="hidden" name="provider" value="paymob" /><button class="btn btn-sm btn-success">💳 {{ __('ادفع لابنك') }}</button></form>
                @else<span class="badge badge-light-success">{{ __('مدفوع') }}</span>@endif</td>
        </tr>@empty<tr><td colspan="7" class="text-center text-muted py-6">{{ __('لا فواتير') }}</td></tr>@endforelse</tbody>
    </table></div></div></div>
</section>
@endsection
