@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('مرحباً،') }} {{ auth('admin')->user()->name }}</h2>
<p class="text-muted mb-0">{{ __('لوحة تحكم الطالب') }} • <span class="badge badge-light-warning">{{ __('نقاط التميز') }}: {{ $stats['points'] ?? 0 }}</span> <span class="badge badge-light-success">{{ __('أيام الالتزام') }}: {{ $stats['streak'] ?? 0 }}</span></p></div>
<div class="mt-4 mt-md-0 d-flex gap-3">
<a href="{{ route('admin.assignments.index') }}" class="btn btn-light-primary">{{ __('واجباتي') }}</a>
<a href="{{ route('admin.videos.index') }}" class="btn btn-primary">{{ __('أكمل الدروس') }}</a>
</div></div></div>

@if(!auth('admin')->user()->grade)
<div class="alert alert-warning d-flex align-items-center mb-7">
<i class="ki-outline ki-information fs-2 me-3"></i>
<span>{{ __('لم يتم تحديد صفك الدراسي بعد — تواصل مع الإدارة لتحديد صفك') }}</span>
</div>
@endif

@if(isset($overdueAssignments) && $overdueAssignments > 0)
<div class="alert alert-danger d-flex align-items-center mb-7">
<i class="ki-outline ki-information fs-2 me-3"></i>
<span>{{ __('لديك') }} <b>{{ $overdueAssignments }}</b> {{ __('واجبات متأخرة عن موعدها — سلمها الآن') }}</span>
<a href="{{ route('admin.assignments.index') }}" class="btn btn-sm btn-danger ms-auto">{{ __('عرضها') }}</a>
</div>
@endif

@if(isset($upcomingExams) && $upcomingExams->isNotEmpty())
<div class="card mb-7" style="border-top: 3px solid #f1416c">
<div class="card-header"><h3 class="card-title">{{ __('امتحانات قادمة') }}</h3></div>
<div class="card-body py-5">
@foreach($upcomingExams as $e)
<div class="d-flex align-items-center gap-4 border rounded p-4 mb-3">
<div class="flex-grow-1"><a href="{{ route('admin.exams.show',$e->id) }}" class="fw-bold text-gray-900">{{ $e->title }}</a>
<div class="text-muted fs-8">{{ $e->exam_date?->format('Y-m-d H:i') }}</div></div>
<span class="badge badge-light-danger fs-7 countdown" data-date="{{ $e->exam_date?->toIso8601String() }}">…</span>
</div>
@endforeach
</div>
</div>
@endif

<div class="row g-6 g-xl-9 mb-7">
@foreach([['label'=>__('الحصص'),'v'=>$stats['courses'],'c'=>'primary','i'=>'book-open'],['label'=>__('الواجبات'),'v'=>$stats['assignments'],'c'=>'success','i'=>'file'],['label'=>__('الامتحانات'),'v'=>$stats['exams'],'c'=>'warning','i'=>'clipboard'],['label'=>__('الفيديوهات'),'v'=>$stats['videos'],'c'=>'danger','i'=>'video']] as $s)
<div class="col-sm-6 col-xl-3"><div class="card card-flush h-100"><div class="card-body py-5 px-6 d-flex align-items-center gap-4">
<span class="symbol symbol-45px"><span class="symbol-label bg-light-{{ $s['c'] }}"><i class="ki-outline ki-{{ $s['i'] }} fs-2 text-{{ $s['c'] }}"></i></span></span>
<div><div class="fs-6 fw-bold text-gray-800">{{ $s['label'] }}</div><div class="fs-2 fw-bolder">{{ $s['v'] }}</div></div>
</div></div></div>
@endforeach
</div>

<div class="row g-6 g-xl-9">
<div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">{{ __('واجبات بانتظارك') }}</h3></div>
<div class="card-body py-5">@forelse($pendingAssignments as $a)<a href="{{ route('admin.assignments.show',$a->id) }}" class="d-block border rounded p-4 mb-3"><span class="fw-bold">{{ $a->title }}</span><span class="d-block text-muted fs-8 mt-1">{{ __('التسليم') }}: {{ $a->due_date?->format('Y-m-d') ?? '—' }}</span></a>@empty<p class="text-muted">{{ __('ممتاز! لا توجد واجبات معلقة') }}</p>@endforelse</div></div></div>
<div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">{{ __('حصص قادمة') }}</h3></div>
<div class="card-body py-5">@forelse($upcomingCourses as $c)<a href="{{ route('admin.courses.show',$c->id) }}" class="d-block border rounded p-4 mb-3"><span class="fw-bold">{{ $c->title }}</span><span class="d-block text-muted fs-8 mt-1">{{ $c->scheduled_at?->format('Y-m-d H:i') ?? '' }}</span></a>@empty<p class="text-muted">{{ __('لا توجد حصص مجدولة') }}</p>@endforelse</div></div></div>
<div class="col-xl-4"><div class="card card-flush h-100"><div class="card-header"><h3 class="card-title">{{ __('أكمل المشاهدة') }}</h3></div>
<div class="card-body py-5">@forelse($recentVideos as $v)<a href="{{ route('admin.videos.show',$v->id) }}" class="d-block border rounded p-4 mb-3"><span class="fw-bold">{{ \Illuminate\Support\Str::limit($v->title,45) }}</span><span class="d-block text-muted fs-8 mt-1">{{ $v->views_count }} {{ __('مشاهدة') }}</span></a>@empty<p class="text-muted">{{ __('لا توجد فيديوهات') }}</p>@endforelse</div></div></div>
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
