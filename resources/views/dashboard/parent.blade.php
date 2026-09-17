@extends('dashboard.partials.master')
@section('content')
@php $riskMap = ['high' => __('خطر'), 'medium' => __('يحتاج متابعة'), 'low' => __('ممتاز')]; @endphp

<!--begin::Hero-->
<div class="card mb-7 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center gap-5 p-7" style="background: linear-gradient(120deg, {{ brand('primary_color', '#1b84ff') }} 0%, {{ brand('primary_color', '#1b84ff') }}cc 55%, {{ brand('secondary_color', '#17c653') }}bb 100%);">
            <div class="flex-grow-1 text-white">
                <h2 class="fw-bolder text-white mb-2">{{ __('مرحباً،') }} {{ auth('admin')->user()->name }}</h2>
                <p class="text-white opacity-75 mb-0">{{ __('متابعة ذكية لأبنائك: المستوى + المخاطر + الفواتير + مين دفع') }}</p>
            </div>
        </div>
        <!--begin::KPI strip-->
        <div class="row g-0">
            @foreach([
                ['l' => __('الأبناء'), 'v' => $stats['students'], 'i' => 'profile-user', 'c' => 'primary'],
                ['l' => __('المستحق الكلي'), 'v' => number_format($stats['due']) . ' ' . __('ج'), 'i' => 'wallet', 'c' => 'warning'],
                ['l' => __('متوسط الأبناء'), 'v' => $stats['avg'] . '%', 'i' => 'chart', 'c' => 'info'],
                ['l' => __('تنبيهات خطر'), 'v' => $stats['alerts'], 'i' => 'shield-cross', 'c' => 'danger'],
            ] as $s)
            <div class="col-6 col-xl-3">
                <div class="d-flex align-items-center gap-3 p-5 border-end border-bottom">
                    <span class="symbol symbol-40px"><span class="symbol-label bg-light-{{ $s['c'] }}"><i class="ki-outline ki-{{ $s['i'] }} fs-3 text-{{ $s['c'] }}"></i></span></span>
                    <span><span class="d-block fs-8 text-muted">{{ $s['l'] }}</span><span class="d-block fs-4 fw-bolder text-gray-900">{{ $s['v'] }}</span></span>
                </div>
            </div>
            @endforeach
        </div>
        <!--end::KPI strip-->
    </div>
</div>
<!--end::Hero-->

@foreach($profiles as $p)
@php $st = $p['student']; @endphp
<div class="card mb-7">
    <div class="card-header">
        <h3 class="card-title fw-bold">{{ $st->name }} <span class="badge badge-light-info ms-2">{{ __($st->grade ?? '') }}</span></h3>
        <div class="card-toolbar d-flex gap-2">
            <span class="badge badge-light-{{ $p['risk'] === 'high' ? 'danger' : ($p['risk'] === 'medium' ? 'warning' : 'success') }}">{{ $riskMap[$p['risk']] }}</span>
            <a href="{{ route('admin.analytics.parent', $st->id) }}" class="btn btn-sm btn-light-primary"><i class="ki-outline ki-file-down fs-4"></i> {{ __('تقرير PDF') }}</a>
        </div>
    </div>
    <div class="card-body py-5">
        <div class="row g-6">
            <div class="col-xl-4">
                <h4 class="fs-7 fw-bold text-muted mb-4">{{ __('إتقان المواد') }} — {{ $p['avg'] ?? '—' }}%</h4>
                @forelse($p['mastery'] as $m)
                <div class="d-flex align-items-center mb-3">
                    <div class="min-w-100px me-3"><span class="fw-bold text-gray-800 fs-7">{{ $m['subject'] }}</span></div>
                    <div class="progress h-8px w-100 me-3"><div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $m['avg']) }}%"></div></div>
                    <span class="text-muted fs-8 fw-bold">{{ $m['avg'] }}%</span>
                </div>
                @empty<p class="text-muted fs-7">{{ __('لا درجات بعد') }}</p>@endforelse
                <div class="d-flex gap-4 mt-4 fs-8 text-muted">
                    <span><i class="ki-outline ki-check-circle fs-5 text-success me-1"></i>{{ __('الحضور') }} {{ $p['attRate'] ?? '—' }}%</span>
                    <span><i class="ki-outline ki-video fs-5 text-danger me-1"></i>{{ $p['vidRate'] ?? '—' }}%</span>
                    <span><i class="ki-outline ki-star fs-5 text-warning me-1"></i>{{ $p['points'] }}</span>
                </div>
                @if(!empty($p['reasons']))
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mt-4">
                    <i class="ki-outline ki-information-5 fs-2 text-warning me-3"></i>
                    <div class="fs-7 text-gray-700">{{ implode(' • ', $p['reasons']) }}</div>
                </div>
                @endif
            </div>
            <div class="col-xl-4">
                <h4 class="fs-7 fw-bold text-muted mb-4">{{ __('درجات الامتحانات') }}</h4>
                @forelse($p['perExam']->take(6) as $x)
                <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                    <span class="fw-semibold text-gray-800 fs-7 flex-grow-1">{{ \Illuminate\Support\Str::limit($x['exam']->title, 35) }}</span>
                    <span class="badge badge-light-{{ $x['passed'] ? 'success' : 'danger' }}">{{ $x['pct'] }}%</span>
                </div>
                @empty<p class="text-muted fs-7">{{ __('لا نتائج بعد') }}</p>@endforelse
            </div>
            <div class="col-xl-4">
                <h4 class="fs-7 fw-bold text-muted mb-4">{{ __('يعمل إيه') }}</h4>
                @forelse(array_slice($p['recommendations'], 0, 4) as $r)
                <div class="d-flex align-items-center gap-2 py-2 border-bottom">
                    <i class="ki-outline ki-arrow-left fs-4 text-primary"></i>
                    <span class="fs-7 fw-semibold text-gray-800">{{ $r['text'] }}</span>
                </div>
                @empty<p class="text-muted fs-7">{{ __('مستواه ممتاز') }}</p>@endforelse
            </div>
        </div>
    </div>
</div>
@endforeach

<!--begin::Invoices-->
<div class="card">
    <div class="card-header">
        <h3 class="card-title fw-bold">{{ __('الفواتير') }}</h3>
        <span class="card-toolbar"><span class="text-muted fs-8">{{ __('الفاتورة باسم الطالب — والدافع متسجل لوحده') }}</span></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-row-bordered align-middle gy-4 mb-0">
                <thead><tr class="fw-bold text-muted fs-8"><th>{{ __('الطالب') }}</th><th>{{ __('الشهر') }}</th><th>{{ __('المطلوب') }}</th><th>{{ __('المدفوع') }}</th><th>{{ __('المتبقي') }}</th><th>{{ __('الدافع') }}</th><th></th></tr></thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="fw-bold fs-7">{{ $inv->student->name ?? '' }}</td>
                        <td class="fs-7">{{ $inv->month }}</td>
                        <td class="fs-7">{{ $inv->amount }}</td>
                        <td class="fs-7 text-success">{{ $inv->paid_amount }}</td>
                        <td class="fs-7 text-danger">{{ $inv->remaining }}</td>
                        <td class="fs-7">{{ $inv->payer_label }}</td>
                        <td class="text-end">
                            @if($inv->remaining > 0)
                            <form method="POST" action="{{ route('admin.onlinepay.checkout',$inv->id) }}">@csrf<input type="hidden" name="provider" value="paymob" /><button class="btn btn-sm btn-success"><i class="ki-outline ki-wallet fs-4"></i> {{ __('ادفع لابنك') }}</button></form>
                            @else<span class="badge badge-light-success">{{ __('مدفوع') }}</span>@endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-6">{{ __('لا فواتير') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--end::Invoices-->
@endsection
