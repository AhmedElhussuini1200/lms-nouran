@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('سجل رسائل الواتساب') }}</h2><p class="text-muted mb-0">{{ __('كل رسالة خرجت من النظام — ناجحة أو فاشلة — بالسبب') }}</p></div>
<div class="mt-4 mt-md-0 d-flex gap-2">
<form method="GET" action="{{ route('admin.whatsapp.logs') }}" class="d-flex gap-2">
<select name="status" class="form-select w-auto" data-control="select2" data-placeholder="{{ __('الحالة') }}" onchange="this.form.submit()">
<option value="all">{{ __('الكل') }}</option>
@foreach(['sent'=>__('تم الإرسال'),'failed'=>__('فشل'),'skipped'=>__('اتخطى')] as $k=>$v)<option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>@endforeach
</select>
</form>
<a href="{{ route('admin.whatsapp.index') }}" class="btn btn-success">{{ __('إرسال جديد') }}</a>
</div></div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted"><th>#</th><th>{{ __('المستلم') }}</th><th>{{ __('الرسالة') }}</th><th>{{ __('المزود') }}</th><th>{{ __('الحالة') }}</th><th>{{ __('بواسطة') }}</th><th>{{ __('التاريخ') }}</th></tr></thead>
<tbody>
@forelse($logs as $log)
<tr>
<td class="text-muted">{{ $log->id }}</td>
<td class="fw-bold">{{ $log->recipient->name ?? '' }}<div class="text-muted fw-normal fs-8" dir="ltr">{{ $log->phone }}</div></td>
<td class="fs-8">{{ \Illuminate\Support\Str::limit($log->message, 80) }}</td>
<td><span class="badge badge-light-info" dir="ltr">{{ $log->provider }}</span></td>
<td>
@if($log->status==='sent')<span class="badge badge-light-success">{{ __('تم الإرسال') }}</span>
@elseif($log->status==='failed')<span class="badge badge-light-danger" title="{{ $log->error }}">{{ __('فشل') }}</span>
@else<span class="badge badge-light-warning" title="{{ $log->error }}">{{ __('اتخطى') }}</span>@endif
</td>
<td class="text-muted fs-8">{{ $log->sender->name ?? '—' }}</td>
<td class="text-muted fs-8">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-10">{{ __('لا توجد رسائل مسجلة') }}</td></tr>
@endforelse
</tbody>
</table></div></div></div>
<div class="mt-7 d-flex justify-content-center">{{ $logs->appends(request()->query())->links() }}</div>
@endsection
