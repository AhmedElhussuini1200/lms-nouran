@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('الواجبات') }}</h2><p class="text-muted mb-0">{{ __('سلم واجباتك وتابع درجاتك') }}</p></div>
<div class="mt-4 mt-md-0 d-flex gap-3">
<form method="GET" action="{{ route('admin.assignments.index') }}">@if(in_array(auth('admin')->user()->type,['admin','teacher']))<select data-placeholder="{{ __('الصف الدراسي') }}" data-control="select2" name="grade" class="form-select w-auto" onchange="this.form.submit()">@foreach($grades as $k=>$v)<option value="{{ $k }}" {{ request('grade')==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select>@else<span class="badge badge-light-info fs-7">{{ __('صفك') }}: {{ gradeLockLabel() }}</span>@endif @if(auth('admin')->user()->type==='admin' && isset($teachers))<select data-placeholder="{{ __('مدرس') }}" data-control="select2" name="teacher" class="form-select w-auto" onchange="this.form.submit()"><option value="all">{{ __('كل المدرسين') }}</option>@foreach($teachers as $t)<option value="{{ $t->id }}" {{ request('teacher')==$t->id?'selected':'' }}>{{ $t->name }}{{ $t->subject ? ' - '.$t->subject : '' }}</option>@endforeach</select>@endif</form>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))<a href="{{ route('admin.assignments.create') }}" class="btn btn-primary"><i class="ki-outline ki-plus fs-2"></i><span class="ms-2">{{ __('إضافة واجب') }}</span></a>@endif
</div></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted"><th>{{ __('العنوان') }}</th><th>{{ __('الصف') }}</th><th>{{ __('التسليم') }}</th><th>{{ __('الدرجة') }}</th><th>{{ __('التسليمات') }}</th><th></th></tr></thead>
<tbody>@forelse($assignments as $a)<tr>
<td class="fw-bold">{{ $a->title }}<div class="text-muted fw-normal fs-8">{{ $a->teacher->name ?? '' }}</div></td>
<td><span class="badge badge-light-info">{{ __($a->grade) }}</span></td>
<td class="text-muted fs-8">{{ $a->due_date?->format('Y-m-d') ?? '—' }}</td>
<td>{{ $a->total_marks ?? '—' }}</td><td>{{ $a->submissions_count }}</td>
<td class="text-end"><a href="{{ route('admin.assignments.show',$a->id) }}" class="btn btn-sm btn-light-primary">{{ __('عرض') }}</a></td>
</tr>@empty<tr><td colspan="6" class="text-center text-muted py-10">{{ __('لا توجد واجبات') }}</td></tr>@endforelse</tbody>
</table></div></div></div>
@if(method_exists($assignments,'links'))<div class="mt-7 d-flex justify-content-center">{{ $assignments->appends(request()->query())->links() }}</div>@endif
@endsection
