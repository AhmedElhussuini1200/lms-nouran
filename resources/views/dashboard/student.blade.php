@extends('dashboard.partials.master')
@section('content')
@php
    $riskMap = ['high' => __('خطر'), 'medium' => __('يحتاج متابعة'), 'low' => __('ممتاز')];
    $riskClass = ['high' => 'danger', 'medium' => 'warning', 'low' => 'success'];
    $rk = $insight['risk'] ?? 'low';
@endphp

<!--begin::Hero-->
<div class="card mb-7 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center gap-5 p-7" style="background: linear-gradient(120deg, {{ brand('primary_color', '#1b84ff') }} 0%, {{ brand('primary_color', '#1b84ff') }}cc 55%, {{ brand('secondary_color', '#17c653') }}bb 100%);">
            <div class="flex-grow-1 text-white">
                <h2 class="fw-bolder text-white mb-2">{{ __('مرحباً،') }} {{ auth('admin')->user()->name }}</h2>
                <p class="text-white opacity-75 mb-0">{{ __('متوسطك') }}: <b>{{ $insight['avg'] ?? '—' }}%</b> • {{ __('الاتجاه') }}: {{ ($insight['trend'] ?? 'flat') === 'up' ? __('صاعد') : (($insight['trend'] ?? 'flat') === 'down' ? __('نازل') : __('ثابت')) }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(!empty($teacher))
                <a href="{{ route('admin.enroll.switch') }}" class="btn btn-light btn-sm" title="{{ __('تغيير المادة') }}"><i class="ki-outline ki-teacher fs-4"></i> {{ $teacher->brand_name ?? $teacher->name }}</a>
                @endif
                <a href="{{ route('admin.assignments.index') }}" class="btn btn-light btn-sm">{{ __('واجباتي') }}</a>
                <a href="{{ route('admin.videos.index') }}" class="btn btn-light btn-sm">{{ __('أكمل الدروس') }}</a>
                <a href="{{ route('admin.leaderboard') }}" class="btn btn-warning btn-sm"><i class="ki-outline ki-medal-star fs-4"></i> {{ __('الصدارة') }}</a>
            </div>
        </div>
        <!--begin::KPI strip-->
        <div class="row g-0">
            @foreach([
                ['l' => __('نقاط التميز'), 'v' => $stats['points'] ?? 0, 'i' => 'star', 'c' => 'warning'],
                ['l' => __('متوسط الدرجات'), 'v' => ($insight['avg'] ?? '—') . '%', 'i' => 'chart-line', 'c' => 'primary'],
                ['l' => __('دقة الاختيارات'), 'v' => ($insight['mcqAccuracy'] ?? '—') . '%', 'i' => 'check-circle', 'c' => 'success'],
                ['l' => __('الحضور 30 يوم'), 'v' => ($insight['attRate'] ?? '—') . '%', 'i' => 'calendar-tick', 'c' => 'info'],
                ['l' => __('إتمام الفيديو'), 'v' => ($insight['vidRate'] ?? '—') . '%', 'i' => 'video', 'c' => 'danger'],
                ['l' => __('الحالة'), 'v' => $riskMap[$rk], 'i' => 'shield-tick', 'c' => $riskClass[$rk]],
            ] as $k)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="d-flex align-items-center gap-3 p-5 border-end border-bottom">
                    <span class="symbol symbol-40px"><span class="symbol-label bg-light-{{ $k['c'] }}"><i class="ki-outline ki-{{ $k['i'] }} fs-3 text-{{ $k['c'] }}"></i></span></span>
                    <span><span class="d-block fs-8 text-muted">{{ $k['l'] }}</span><span class="d-block fs-4 fw-bolder text-gray-900">{{ $k['v'] }}</span></span>
                </div>
            </div>
            @endforeach
        </div>
        <!--end::KPI strip-->
    </div>
</div>
<!--end::Hero-->

<div class="row g-6 g-xl-9 mb-7">
    <!--begin::Recommendations-->
    <div class="col-xl-6">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('اعمل إيه دلوقتي') }}</h3><span class="card-toolbar"><span class="badge badge-light-primary">{{ __('مرتبة حسب الأهمية ليك') }}</span></span></div>
            <div class="card-body py-5">
                @forelse($insight['recommendations'] ?? [] as $r)
                <a href="{{ $r['url'] }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-light-{{ $r['level'] === 'high' ? 'danger' : ($r['level'] === 'medium' ? 'warning' : 'success') }} fw-bold">{{ $loop->iteration }}</span></span>
                    <span class="fw-semibold text-gray-800">{{ $r['text'] }}</span>
                    <i class="ki-outline ki-arrow-left fs-3 text-muted ms-auto"></i>
                </a>
                @empty<p class="text-muted mb-0">{{ __('لا توصيات حالياً') }}</p>@endforelse
            </div>
        </div>
    </div>
    <!--end::Recommendations-->
    <!--begin::Mastery-->
    <div class="col-xl-6">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('إتقان المواد') }}</h3></div>
            <div class="card-body py-5">
                @forelse($insight['mastery'] ?? [] as $m)
                <div class="d-flex align-items-center mb-4">
                    <div class="min-w-125px me-3"><span class="fw-bold text-gray-800 fs-7">{{ $m['subject'] }}</span></div>
                    <div class="progress h-8px w-100 me-3"><div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $m['avg']) }}%"></div></div>
                    <span class="text-muted fs-8 fw-bold">{{ $m['avg'] }}%</span>
                </div>
                @empty<p class="text-muted mb-0">{{ __('امتحن أول امتحان عشان نرسم مستواك') }}</p>@endforelse
                @if(!empty($insight['reasons']))
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mt-4">
                    <i class="ki-outline ki-information-5 fs-2 text-warning me-3"></i>
                    <div class="fs-7 text-gray-700">{{ implode(' • ', $insight['reasons']) }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!--end::Mastery-->
</div>

<div class="row g-6 g-xl-9">
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('درجاتي') }}</h3></div>
            <div class="card-body py-5">
                @forelse($insight['perExam']->take(5) ?? [] as $x)
                <a href="{{ route('admin.exams.show', $x['exam']->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light">
                    <span class="fw-bold text-gray-800 flex-grow-1">{{ \Illuminate\Support\Str::limit($x['exam']->title, 40) }}</span>
                    <span class="badge badge-light-{{ $x['passed'] ? 'success' : 'danger' }}">{{ $x['pct'] }}%</span>
                </a>
                @empty<p class="text-muted mb-0">{{ __('لا نتائج بعد') }}</p>@endforelse
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('القادم') }}</h3></div>
            <div class="card-body py-5">
                @foreach($upcomingExams as $e)
                <a href="{{ route('admin.exams.show',$e->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-light-danger"><i class="ki-outline ki-clipboard fs-4 text-danger"></i></span></span>
                    <span class="flex-grow-1"><span class="d-block fw-bold text-gray-800">{{ $e->title }}</span></span>
                    <span class="badge badge-light-danger countdown" data-date="{{ $e->exam_date?->toIso8601String() }}">…</span>
                </a>
                @endforeach
                @foreach($upcomingCourses as $c)
                <a href="{{ route('admin.courses.show',$c->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-light-primary"><i class="ki-outline ki-book-open fs-4 text-primary"></i></span></span>
                    <span class="flex-grow-1"><span class="d-block fw-bold text-gray-800">{{ $c->title }}</span><span class="d-block text-muted fs-8">{{ $c->scheduled_at?->format('Y-m-d H:i') }}</span></span>
                </a>
                @endforeach
                @foreach($pendingAssignments as $a)
                <a href="{{ route('admin.assignments.show',$a->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-light-success"><i class="ki-outline ki-file fs-4 text-success"></i></span></span>
                    <span class="flex-grow-1"><span class="d-block fw-bold text-gray-800">{{ $a->title }}</span><span class="d-block text-muted fs-8">{{ $a->due_date?->format('Y-m-d') ?? '' }}</span></span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('فواتيري') }}</h3><span class="card-toolbar"><a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-light">{{ __('الكل') }}</a></span></div>
            <div class="card-body py-5">
                @forelse($myPayments as $p)
                <div class="d-flex align-items-center gap-3 p-3 rounded border border-dashed mb-3">
                    <span class="flex-grow-1"><span class="d-block fw-bold text-gray-800">{{ $p->month }}</span><span class="d-block text-muted fs-8">{{ __('المتبقي') }}: {{ $p->remaining }} {{ __('ج') }}</span></span>
                    @if($p->remaining > 0)
                    <form method="POST" action="{{ route('admin.onlinepay.checkout',$p->id) }}">@csrf<input type="hidden" name="provider" value="paymob" /><button class="btn btn-sm btn-success"><i class="ki-outline ki-wallet fs-4"></i> {{ __('ادفع') }}</button></form>
                    @else<span class="badge badge-light-success">{{ __('مدفوع') }}</span>@endif
                </div>
                @empty<p class="text-muted mb-0">{{ __('لا فواتير') }}</p>@endforelse
                @if($myCertificates->isNotEmpty())
                <div class="separator my-4"></div>
                @foreach($myCertificates as $c)
                <a href="{{ route('admin.certificates.pdf',$c->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-light-primary"><i class="ki-outline ki-award fs-4 text-primary"></i></span></span>
                    <span class="fw-bold text-gray-800 flex-grow-1">{{ $c->exam->title ?? '' }}</span>
                    <span class="badge badge-light-primary">PDF</span>
                </a>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function tick() {
        document.querySelectorAll('.countdown[data-date]').forEach(function(el) {
            const diff = new Date(el.dataset.date).getTime() - Date.now();
            if (isNaN(diff) || diff <= 0) { el.textContent = "{{ __('بدأ الآن') }}"; return; }
            const d = Math.floor(diff / 86400000), h = Math.floor(diff % 86400000 / 3600000), m = Math.floor(diff % 3600000 / 60000);
            el.textContent = (d > 0 ? d + "{{ __('يوم') }} " : "") + pad(h) + ":" + pad(m);
        });
    }
    tick(); setInterval(tick, 60000);
});
</script>
@endpush
