@extends('dashboard.partials.master')

@section('content')
@php
    $gradeNames = ['1_secondary' => 'الأول الثانوي', '2_secondary' => 'الثاني الثانوي', '3_secondary' => 'الثالث الثانوي'];
    $maxMonth = max(1, collect($months ?? [])->max(fn($m) => max($m['total'], $m['paid'])));
    $stTotal = array_sum($submissionStates ?? []);
    $stColors = ['submitted' => '#1b84ff', 'under_review' => '#f6c343', 'graded' => '#17c653', 'returned' => '#f1416c'];
    $donut = '';
    if ($stTotal > 0) {
        $acc = 0; $parts = [];
        foreach ($stColors as $k => $c) {
            $v = $submissionStates[$k] ?? 0;
            $from = round($acc / $stTotal * 360, 1); $acc += $v; $to = round($acc / $stTotal * 360, 1);
            if ($v > 0) $parts[] = "$c {$from}deg {$to}deg";
        }
        $donut = 'background: conic-gradient(' . implode(',', $parts) . ');';
    }
@endphp

<!--begin::Hero-->
<div class="card mb-7 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center gap-5 p-7" style="background: linear-gradient(120deg, {{ brand('primary_color', '#1b84ff') }} 0%, {{ brand('primary_color', '#1b84ff') }}cc 55%, {{ brand('secondary_color', '#17c653') }}bb 100%);">
            <div class="flex-grow-1 text-white">
                <h2 class="fw-bolder text-white mb-2">{{ __('مرحباً،') }} {{ auth('admin')->user()->name }}</h2>
                <p class="text-white opacity-75 mb-0">{{ brand('site_name') }} • {{ __('لديك') }} <b>{{ $stats['pending_reviews'] ?? 0 }}</b> {{ __('تسليمات بانتظار التصحيح') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.videos.create') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-video fs-4"></i> {{ __('فيديو') }}</a>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-book-open fs-4"></i> {{ __('حصة') }}</a>
                <a href="{{ route('admin.assignments.create') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-file fs-4"></i> {{ __('واجب') }}</a>
                <a href="{{ route('admin.exams.create') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-clipboard fs-4"></i> {{ __('امتحان') }}</a>
                <a href="{{ route('admin.payments.create') }}" class="btn btn-warning btn-sm"><i class="ki-outline ki-wallet fs-4"></i> {{ __('فاتورة') }}</a>
            </div>
        </div>
        <!--begin::KPI strip-->
        <div class="row g-0">
            @foreach([
                ['l' => __('الطلاب'), 'v' => $stats['students'] ?? 0, 'i' => 'profile-user', 'c' => 'primary', 'u' => route('admin.admins.index')],
                ['l' => __('المدرسين'), 'v' => $stats['teachers'] ?? 0, 'i' => 'teacher', 'c' => 'info', 'u' => route('admin.admins.index')],
                ['l' => __('فيديو + حصة'), 'v' => ($stats['videos'] ?? 0) + ($stats['courses'] ?? 0), 'i' => 'video', 'c' => 'danger', 'u' => route('admin.videos.index')],
                ['l' => __('واجب + امتحان'), 'v' => ($stats['assignments'] ?? 0) + ($stats['exams'] ?? 0), 'i' => 'clipboard', 'c' => 'warning', 'u' => route('admin.assignments.index')],
                ['l' => __('محصل'), 'v' => number_format($stats['collected'] ?? 0) . ' ج', 'i' => 'wallet', 'c' => 'success', 'u' => route('admin.payments.index')],
                ['l' => __('متبقي'), 'v' => number_format($stats['receivable'] ?? 0) . ' ج', 'i' => 'time', 'c' => 'dark', 'u' => route('admin.payments.index')],
            ] as $k)
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ $k['u'] }}" class="d-flex align-items-center gap-3 p-5 border-end border-bottom text-hover-primary">
                    <span class="symbol symbol-40px"><span class="symbol-label bg-light-{{ $k['c'] }}"><i class="ki-outline ki-{{ $k['i'] }} fs-3 text-{{ $k['c'] }}"></i></span></span>
                    <span><span class="d-block fs-8 text-muted">{{ $k['l'] }}</span><span class="d-block fs-4 fw-bolder text-gray-900">{{ $k['v'] }}</span></span>
                </a>
            </div>
            @endforeach
        </div>
        <!--end::KPI strip-->
    </div>
</div>
<!--end::Hero-->

<div class="row g-6 g-xl-9 mb-7">
    <!--begin::Content by grade-->
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('المحتوى حسب الصف') }}</h3></div>
            <div class="card-body py-5">
                @foreach($contentByGrade ?? [] as $g => $c)
                @php $tot = $c['courses'] + $c['videos'] + $c['assignments'] + $c['exams']; @endphp
                <div class="mb-5">
                    <div class="d-flex justify-content-between fs-8 mb-1"><span class="fw-bold">{{ $gradeNames[$g] }}</span><span class="text-muted">{{ $tot }} {{ __('عنصر') }} • {{ $c['students'] }} {{ __('طالب') }}</span></div>
                    <div class="d-flex h-8px rounded overflow-hidden bg-light">
                        @php $mx = max(1, $tot); @endphp
                        <span style="width:{{ $c['courses'] / $mx * 100 }}%;background:#1b84ff" title="{{ __('حصص') }}"></span>
                        <span style="width:{{ $c['videos'] / $mx * 100 }}%;background:#f1416c" title="{{ __('فيديو') }}"></span>
                        <span style="width:{{ $c['assignments'] / $mx * 100 }}%;background:#f6c343" title="{{ __('واجب') }}"></span>
                        <span style="width:{{ $c['exams'] / $mx * 100 }}%;background:#17c653" title="{{ __('امتحان') }}"></span>
                    </div>
                </div>
                @endforeach
                <div class="d-flex flex-wrap gap-3 fs-8 text-muted mt-2">
                    <span><span class="bullet bullet-dot bg-primary me-1"></span>{{ __('حصص') }}</span>
                    <span><span class="bullet bullet-dot bg-danger me-1"></span>{{ __('فيديو') }}</span>
                    <span><span class="bullet bullet-dot bg-warning me-1"></span>{{ __('واجب') }}</span>
                    <span><span class="bullet bullet-dot bg-success me-1"></span>{{ __('امتحان') }}</span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content by grade-->
    <!--begin::Submission states-->
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('حالات التسليمات') }}</h3><a href="{{ route('admin.assignments.index') }}" class="btn btn-sm btn-light">{{ __('التفاصيل') }}</a></div>
            <div class="card-body py-5 d-flex align-items-center gap-6">
                <div class="rounded-circle flex-shrink-0" style="width:130px;height:130px;{{ $donut ?: 'background:#eef1f6;' }}"></div>
                <div class="flex-grow-1">
                    @foreach(['submitted' => __('تم التسليم'), 'under_review' => __('قيد التصحيح'), 'graded' => __('تم التصحيح'), 'returned' => __('راجع للتعديل')] as $k => $v)
                    <div class="d-flex justify-content-between fs-7 py-1 border-bottom"><span><span class="bullet bullet-dot me-2" style="background:{{ $stColors[$k] }}"></span>{{ $v }}</span><b>{{ $submissionStates[$k] ?? 0 }}</b></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!--end::Submission states-->
    <!--begin::Monthly collection-->
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('التحصيل الشهري') }}</h3><a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-light">{{ __('الحسابات') }}</a></div>
            <div class="card-body py-5">
                @foreach($months ?? [] as $m => $v)
                <div class="mb-3">
                    <div class="d-flex justify-content-between fs-8 mb-1"><span class="fw-bold">{{ $m }}</span><span class="text-muted">{{ number_format($v['paid']) }} / {{ number_format($v['total']) }}</span></div>
                    <div class="h-8px rounded bg-light position-relative overflow-hidden">
                        <div class="h-100 rounded bg-light-success position-absolute" style="width:{{ $v['total'] > 0 ? round($v['total'] / $maxMonth * 100) : 0 }}%;background:#e8f7ee !important"></div>
                        <div class="h-100 rounded position-absolute" style="width:{{ $v['total'] > 0 ? round($v['paid'] / $maxMonth * 100) : 0 }}%;background:#17c653"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!--end::Monthly collection-->
</div>

<div class="row g-6 g-xl-9">
    <!--begin::Pending reviews-->
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('بانتظار التصحيح') }}</h3><span class="badge badge-light-warning">{{ $stats['pending_reviews'] ?? 0 }}</span></div>
            <div class="card-body py-5">
                @forelse($pendingReviews ?? [] as $s)
                <a href="{{ route('admin.assignments.show', $s->assignment_id) }}" class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                    <span><span class="d-block fw-bold fs-8">{{ $s->student->name ?? '' }}</span><span class="d-block text-muted fs-9">{{ $s->assignment->title ?? '' }}</span></span>
                    <span class="badge badge-light-{{ $s->status->color ?? 'info' }} fs-9">{{ $s->status->name_ar ?? '' }}</span>
                </a>
                @empty<p class="text-muted fs-8">{{ __('لا يوجد ما ينتظر التصحيح') }}</p>@endforelse
            </div>
        </div>
    </div>
    <!--end::Pending reviews-->
    <!--begin::Top teachers-->
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('أنشط المدرسين') }}</h3></div>
            <div class="card-body py-5">
                @forelse($topTeachers ?? [] as $t)
                <div class="d-flex align-items-center gap-3 border rounded p-3 mb-2">
                    <span class="symbol symbol-35px"><span class="symbol-label bg-light-primary fw-bold">{{ mb_substr($t->name, 0, 1) }}</span></span>
                    <span class="flex-grow-1"><span class="d-block fw-bold fs-8">{{ $t->name }}</span>
                    <span class="d-block text-muted fs-9">{{ $t->courses_count }} {{ __('حصة') }} • {{ $t->videos_count }} {{ __('فيديو') }} • {{ $t->assignments_count }} {{ __('واجب') }} • {{ $t->exams_count }} {{ __('امتحان') }}</span></span>
                </div>
                @empty<p class="text-muted fs-8">{{ __('لا يوجد مدرسون بعد') }}</p>@endforelse
            </div>
        </div>
    </div>
    <!--end::Top teachers-->
    <!--begin::Overdue payments-->
    <div class="col-xl-4">
        <div class="card card-flush h-100">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('متأخرات التحصيل') }}</h3><a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-light">{{ __('الكل') }}</a></div>
            <div class="card-body py-5">
                @forelse($overduePayments ?? [] as $p)
                <a href="{{ route('admin.payments.edit', $p->id) }}" class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                    <span><span class="d-block fw-bold fs-8">{{ $p->student->name ?? '' }}</span><span class="d-block text-muted fs-9">{{ $p->month }}</span></span>
                    <span class="text-end"><span class="d-block fw-bolder text-danger fs-8">{{ number_format($p->amount - $p->paid_amount) }} ج</span><span class="badge badge-light-{{ $p->status->color ?? 'warning' }} fs-9">{{ $p->status->name_ar ?? '' }}</span></span>
                </a>
                @empty<p class="text-muted fs-8">{{ __('لا توجد متأخرات') }}</p>@endforelse
            </div>
        </div>
    </div>
    <!--end::Overdue payments-->
</div>

<div class="row g-6 g-xl-9 mt-0">
    <!--begin::Leaderboard-->
    <div class="col-12">
        <div class="card card-flush">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('لوحة الصدارة') }} 🏆</h3></div>
            <div class="card-body py-5">
                <div class="d-flex flex-wrap gap-4">
                @forelse($leaderboard ?? [] as $i => $row)
                <div class="d-flex align-items-center gap-3 border rounded p-4 flex-grow-1" style="min-width:220px;{{ $i === 0 ? 'border-color:gold !important;border-width:2px;' : '' }}">
                    <span class="symbol symbol-45px"><span class="symbol-label {{ $i === 0 ? 'bg-light-warning' : 'bg-light-primary' }} fw-bolder fs-4">{{ $i + 1 }}</span></span>
                    <span><span class="d-block fw-bold">{{ $row['student']->name }}</span>
                    <span class="d-block text-muted fs-8">{{ __($row['student']->grade ?? '') }} • <b class="text-warning">{{ $row['points'] }}</b> {{ __('نقطة') }}</span></span>
                </div>
                @empty<p class="text-muted fs-8">{{ __('لا يوجد نشاط بعد') }}</p>@endforelse
                </div>
            </div>
        </div>
    </div>
    <!--end::Leaderboard-->
</div>
@endsection
