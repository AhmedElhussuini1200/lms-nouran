@extends('dashboard.partials.master')
@section('content')
<div class="d-flex align-items-center gap-3 mb-7"><a href="{{ route('admin.admins.index') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-arrow-right fs-2"></i> {{ __('إدارة المستخدمين') }}</a><h2 class="fw-bold mb-0">{{ $admin->name }}</h2></div>
<div class="row g-6">
<div class="col-xl-4"><div class="card card-flush h-100"><div class="card-body p-7 text-center">
<span class="symbol symbol-80px mb-4"><span class="symbol-label bg-light-primary fs-2 fw-bold">{{ mb_substr($admin->name, 0, 1) }}</span></span>
<h3 class="fw-bold">{{ $admin->name }}</h3>
<p class="text-muted">{{ $admin->email }}<br>{{ $admin->phone ?? '' }}</p>
<div class="d-flex justify-content-center gap-2 mt-3">
<span class="badge badge-light-primary">{{ $admin->type }}</span>
@if($admin->grade)<span class="badge badge-light-info">{{ __($admin->grade) }}</span>@endif
@if($admin->subject)<span class="badge badge-light-warning">{{ $admin->subject }}</span>@endif
@if($admin->is_blocked)<span class="badge badge-light-danger">{{ __('محظور') }}</span>@endif
</div>
<div class="d-flex gap-2 justify-content-center mt-6 flex-wrap">
<a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-light-primary btn-sm">{{ __('تعديل') }}</a>
@if($admin->phone)
<a href="{{ route('admin.whatsapp.index', ['user' => $admin->id]) }}" class="btn btn-light-success btn-sm"><i class="ki-outline ki-whatsapp fs-4"></i> {{ __('واتساب') }}</a>
@endif
<form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" class="btn btn-light-danger btn-sm">{{ __('حذف') }}</button></form>
</div>
</div></div></div>
<div class="col-xl-8">
@if($admin->type === 'teacher')
<div class="card mb-6"><div class="card-header"><h3 class="card-title">{{ __('إحصائيات المدرس') }}</h3></div>
<div class="card-body"><div class="row text-center">
@foreach([__('الحصص') => $admin->courses->count(), __('الفيديوهات') => $admin->videos->count(), __('الواجبات') => $admin->assignments->count(), __('الامتحانات') => $admin->exams->count()] as $l => $v)
<div class="col-3"><div class="fs-2 fw-bolder">{{ $v }}</div><div class="text-muted fs-8">{{ $l }}</div></div>
@endforeach
</div></div></div>
@endif
@if($admin->type === 'parent')
<div class="card"><div class="card-header"><h3 class="card-title">{{ __('الأبناء') }}</h3></div>
<div class="card-body">@forelse($admin->students as $s)<a href="{{ route('admin.admins.show',$s->id) }}" class="d-flex justify-content-between border rounded p-3 mb-2"><span class="fw-bold">{{ $s->name }}</span><span class="badge badge-light-info">{{ __($s->grade ?? '') }}</span></a>@empty<p class="text-muted">{{ __('لا يوجد أبناء مربوطون') }}</p>@endforelse</div></div>
@endif
@if($admin->type === 'student' && isset($profile))
<div class="row g-6 mb-6">
@foreach([
 ['l'=>__('متوسط الدرجات'),'v'=>$profile['avg'] ?? '—','c'=>'primary'],
 ['l'=>__('نقاط التميز'),'v'=>$profile['points'],'c'=>'warning'],
 ['l'=>__('أيام الحضور'),'v'=>$profile['presentCount'],'c'=>'success'],
 ['l'=>__('أيام الغياب'),'v'=>$profile['absenceCount'],'c'=>'danger'],
 ['l'=>__('المتبقي مالياً'),'v'=>number_format($profile['dueTotal']).' ج','c'=>'info'],
] as $s)
<div class="col-6 col-md-4 col-xl"><div class="card"><div class="card-body py-4 px-5 text-center">
<div class="fs-7 text-muted">{{ $s['l'] }}</div><div class="fs-3 fw-bolder text-{{ $s['c'] }}">{{ $s['v'] }}</div>
</div></div></div>
@endforeach
</div>

@if($profile['missing']->isNotEmpty())
<div class="card mb-6 border-danger"><div class="card-header"><h3 class="card-title text-danger">{{ __('تقصير: واجبات لم تسلم') }} ({{ $profile['missing']->count() }})</h3></div>
<div class="card-body py-5">@foreach($profile['missing'] as $m)
<a href="{{ route('admin.assignments.show',$m->id) }}" class="d-flex justify-content-between border rounded p-3 mb-2"><span class="fw-bold">{{ $m->title }}</span><span class="text-muted fs-8">{{ $m->due_date?->format('Y-m-d') ?? '' }}</span></a>
@endforeach</div></div>
@endif

<div class="card mb-6"><div class="card-header"><h3 class="card-title">{{ __('الغياب الأخير') }}</h3></div>
<div class="card-body py-5">@forelse($profile['absences'] as $a)
<div class="d-flex justify-content-between border rounded p-3 mb-2"><span>{{ $a->course->title ?? '' }}</span><span class="badge badge-light-danger">{{ $a->date?->format('Y-m-d') }}</span></div>
@empty<p class="text-success fs-8 mb-0">{{ __('سجل حضور نظيف — لا غياب') }}</p>@endforelse</div></div>

<div class="card mb-6"><div class="card-header"><h3 class="card-title">{{ __('نتائج الامتحانات') }}</h3></div>
<div class="card-body p-0"><div class="table-responsive"><table class="table align-middle gy-2 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>{{ __('الامتحان') }}</th><th>{{ __('الدرجة') }}</th><th>{{ __('الحالة') }}</th></tr></thead>
<tbody>@forelse($profile['results'] as $r)<tr>
<td>{{ $r->exam->title ?? '' }}</td>
<td class="fw-bolder">{{ $r->marks_obtained ?? '—' }} / {{ $r->exam->total_marks ?? '' }}</td>
<td>@if($r->status)<span class="badge badge-light-{{ $r->status->color }}">{{ $r->status->name_ar }}</span>@endif</td>
</tr>@empty<tr><td colspan="3" class="text-center text-muted py-5">{{ __('لا توجد نتائج') }}</td></tr>@endforelse</tbody>
</table></div></div></div>

<div class="card mb-6"><div class="card-header"><h3 class="card-title">{{ __('تسليمات الواجبات') }}</h3></div>
<div class="card-body p-0"><div class="table-responsive"><table class="table align-middle gy-2 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>{{ __('الواجب') }}</th><th>{{ __('الدرجة') }}</th><th>{{ __('الحالة') }}</th></tr></thead>
<tbody>@forelse($profile['submissions'] as $s)<tr>
<td>{{ $s->assignment->title ?? '' }}</td>
<td class="fw-bolder">{{ $s->marks ?? '—' }}</td>
<td>@if($s->status)<span class="badge badge-light-{{ $s->status->color }}">{{ $s->status->name_ar }}</span>@endif</td>
</tr>@empty<tr><td colspan="3" class="text-center text-muted py-5">{{ __('لا توجد تسليمات') }}</td></tr>@endforelse</tbody>
</table></div></div></div>

<div class="card"><div class="card-header"><h3 class="card-title">{{ __('المدفوعات') }}</h3><a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-light">{{ __('الحسابات') }}</a></div>
<div class="card-body p-0"><div class="table-responsive"><table class="table align-middle gy-2 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>{{ __('الشهر') }}</th><th>{{ __('المطلوب') }}</th><th>{{ __('المدفوع') }}</th><th>{{ __('الحالة') }}</th></tr></thead>
<tbody>@forelse($profile['payments'] as $p)<tr>
<td>{{ $p->month }}</td><td>{{ $p->amount }}</td><td class="text-success">{{ $p->paid_amount }}</td>
<td>@if($p->status)<span class="badge badge-light-{{ $p->status->color }}">{{ $p->status->name_ar }}</span>@endif</td>
</tr>@empty<tr><td colspan="4" class="text-center text-muted py-5">{{ __('لا توجد فواتير') }}</td></tr>@endforelse</tbody>
</table></div></div></div>
@endif
</div>
</div>
@endsection
