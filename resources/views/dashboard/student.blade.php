@extends('dashboard.partials.master')
@include('dashboard.partials.design-system')
@section('content')
@php
    $riskMap = ['high' => __('خطر'), 'medium' => __('يحتاج متابعة'), 'low' => __('ممتاز')];
    $riskClass = ['high' => 'danger', 'medium' => 'warning', 'low' => 'success'];
    $rk = $insight['risk'] ?? 'low';
@endphp

{{-- ============ الترويسة ============ --}}
<div class="hero-lms p-7 mb-7 d-flex flex-column flex-md-row align-items-md-center gap-4">
    <div class="flex-grow-1">
        <h2 class="fw-bold text-white mb-1">{{ __('مرحباً،') }} {{ auth('admin')->user()->name }} 👋</h2>
        <p class="text-white opacity-75 mb-0">
            {{ __('متوسطك') }}: <b>{{ $insight['avg'] ?? '—' }}%</b>
            • {{ __('الاتجاه') }}: {{ ($insight['trend'] ?? 'flat') === 'up' ? '📈 '. __('صاعد') : (($insight['trend'] ?? 'flat') === 'down' ? '📉 '. __('نازل') : '➖ '. __('ثابت')) }}
            • <span class="badge badge-light-{{ $riskClass[$rk] }}">{{ $riskMap[$rk] }}</span>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.assignments.index') }}" class="btn btn-light btn-sm">{{ __('واجباتي') }}</a>
        <a href="{{ route('admin.videos.index') }}" class="btn btn-light btn-sm">{{ __('أكمل الدروس') }}</a>
        <a href="{{ route('admin.leaderboard') }}" class="btn btn-warning btn-sm">🏆 {{ __('الصدارة') }}</a>
    </div>
</div>

{{-- ============ الأرقام ============ --}}
<section class="sec">
    <div class="sec-head"><span class="sec-head__bar"></span>
        <div><h2 class="sec-head__title">{{ __('نظرة عامة') }}</h2><p class="sec-head__desc">{{ __('أرقامك في مكان واحد') }}</p></div>
    </div>
    <div class="row g-5">
        @foreach([
            ['l' => __('نقاط التميز'), 'v' => $stats['points'] ?? 0, 'i' => 'star'],
            ['l' => __('متوسط الدرجات'), 'v' => ($insight['avg'] ?? '—') . '%', 'i' => 'chart-line'],
            ['l' => __('دقة الاختيارات'), 'v' => ($insight['mcqAccuracy'] ?? '—') . '%', 'i' => 'check-circle'],
            ['l' => __('الحضور 30 يوم'), 'v' => ($insight['attRate'] ?? '—') . '%', 'i' => 'calendar-tick'],
            ['l' => __('إتمام الفيديو'), 'v' => ($insight['vidRate'] ?? '—') . '%', 'i' => 'video'],
            ['l' => __('أيام الالتزام'), 'v' => $stats['streak'] ?? 0, 'i' => 'fire'],
        ] as $s)
        <div class="col-6 col-md-4 col-xl-2"><div class="stat">
            <span class="stat__icon"><i class="ki-outline ki-{{ $s['i'] }}"></i></span>
            <span><span class="stat__value">{{ $s['v'] }}</span><span class="stat__label">{{ $s['l'] }}</span></span>
        </div></div>
        @endforeach
    </div>
</section>

{{-- ============ اعمل إيه دلوقتي + إتقان المواد ============ --}}
<div class="row g-5 mb-7">
    <div class="col-xl-6">
        <section class="sec mb-0"><div class="sec-head"><span class="sec-head__bar"></span>
            <div><h2 class="sec-head__title">🎯 {{ __('اعمل إيه دلوقتي') }}</h2><p class="sec-head__desc">{{ __('مرتبة حسب الأهمية ليك') }}</p></div>
        </div>
        <div class="card card-flush"><div class="card-body py-4">
            @forelse($insight['recommendations'] ?? [] as $r)
            <a href="{{ $r['url'] }}" class="rec"><span class="rec__icon">{{ $r['icon'] }}</span>
                <span class="rec__txt">{{ $r['text'] }}</span>
                <span class="rec__lvl badge badge-light-{{ $r['level'] === 'high' ? 'danger' : ($r['level'] === 'medium' ? 'warning' : 'success') }}">{{ __('مهم') }}</span></a>
            @empty<p class="text-muted mb-0">{{ __('لا توصيات حالياً') }}</p>@endforelse
        </div></div></section>
    </div>
    <div class="col-xl-6">
        <section class="sec mb-0"><div class="sec-head"><span class="sec-head__bar"></span>
            <div><h2 class="sec-head__title">📊 {{ __('إتقان المواد') }}</h2><p class="sec-head__desc">{{ __('من أفضل درجاتك في كل مادة') }}</p></div>
        </div>
        <div class="card card-flush"><div class="card-body py-5">
            @forelse($insight['mastery'] ?? [] as $m)
            <div class="mastery"><div class="mastery__top"><span class="fw-bold">{{ $m['subject'] }}</span><span class="text-muted">{{ $m['avg'] }}%</span></div>
            <div class="mastery__bar"><span style="width:{{ min(100, $m['avg']) }}%"></span></div></div>
            @empty<p class="text-muted mb-0">{{ __('امتحن أول امتحان عشان نرسم مستواك') }}</p>@endforelse
            @if(!empty($insight['reasons']))<div class="alert alert-light-warning mt-3 mb-0 fs-8">⚠️ {{ implode(' • ', $insight['reasons']) }}</div>@endif
        </div></div></section>
    </div>
</div>

{{-- ============ درجاتي + القادم ============ --}}
<div class="row g-5">
    <div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">📝 {{ __('درجاتي') }}</h3></div>
        <div class="card-body py-4">@forelse($insight['perExam']->take(5) ?? [] as $x)
        <a href="{{ route('admin.exams.show', $x['exam']->id) }}" class="rec"><span class="rec__txt">{{ $x['exam']->title }}</span>
        <span class="rec__lvl badge badge-light-{{ $x['passed'] ? 'success' : 'danger' }}">{{ $x['pct'] }}%</span></a>
        @empty<p class="text-muted">{{ __('لا نتائج بعد') }}</p>@endforelse</div></div></div>
    <div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">📌 {{ __('القادم') }}</h3></div>
        <div class="card-body py-4">
        @foreach($upcomingExams as $e)<a href="{{ route('admin.exams.show',$e->id) }}" class="rec"><span class="rec__icon">📝</span><span class="rec__txt">{{ $e->title }}</span><span class="rec__lvl badge badge-light-danger countdown" data-date="{{ $e->exam_date?->toIso8601String() }}">…</span></a>@endforeach
        @foreach($upcomingCourses as $c)<a href="{{ route('admin.courses.show',$c->id) }}" class="rec"><span class="rec__icon">📚</span><span class="rec__txt">{{ $c->title }}</span><span class="rec__lvl text-muted fs-8">{{ $c->scheduled_at?->format('m-d H:i') }}</span></a>@endforeach
        @foreach($pendingAssignments as $a)<a href="{{ route('admin.assignments.show',$a->id) }}" class="rec"><span class="rec__icon">📄</span><span class="rec__txt">{{ $a->title }}</span><span class="rec__lvl text-muted fs-8">{{ $a->due_date?->format('m-d') ?? '' }}</span></a>@endforeach
        </div></div></div>
    <div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">💰 {{ __('فواتيري') }}</h3>
        <div class="card-toolbar"><a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-light">{{ __('الكل') }}</a></div></div>
        <div class="card-body py-4">@forelse($myPayments as $p)
        <div class="rec"><span class="rec__txt">{{ $p->month }} — {{ __('المتبقي') }} {{ $p->remaining }} ج</span>
        @if($p->remaining > 0)<span class="rec__lvl"><form method="POST" action="{{ route('admin.onlinepay.checkout',$p->id) }}">@csrf<input type="hidden" name="provider" value="paymob" /><button class="btn btn-sm btn-success">💳 {{ __('ادفع') }}</button></form></span>
        @else<span class="rec__lvl badge badge-light-success">{{ __('مدفوع') }}</span>@endif</div>
        @empty<p class="text-muted">{{ __('لا فواتير') }}</p>@endforelse
        @if($myCertificates->isNotEmpty())<div class="separator my-3"></div>
        @foreach($myCertificates as $c)<a href="{{ route('admin.certificates.pdf',$c->id) }}" class="rec"><span class="rec__icon">🎓</span><span class="rec__txt">{{ $c->exam->title ?? '' }}</span><span class="rec__lvl badge badge-light-primary">PDF</span></a>@endforeach
        @endif
        </div></div></div>
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
