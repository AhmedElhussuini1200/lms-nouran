@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('الحسابات والمدفوعات') }}</h2>
@if($summary)<p class="text-muted mb-0">{{ $summary['year'] }} • {{ __('المطلوب') }}: <b>{{ $summary['total'] }}</b> • {{ __('المحصل') }}: <b class="text-success">{{ $summary['paid'] }}</b> • {{ __('المتبقي') }}: <b class="text-danger">{{ $summary['remaining'] }}</b></p>@else<p class="text-muted mb-0">{{ __('فواتيرك الشهرية') }}</p>@endif</div>
<div class="mt-4 mt-md-0 d-flex gap-2">
<form method="GET" action="{{ route('admin.payments.index') }}" class="d-flex gap-2">
<input type="month" name="month" class="form-control w-auto" value="{{ request('month') }}" onchange="this.form.submit()" />
<select data-placeholder="{{ __('الحالة') }}" data-control="select2" name="status" class="form-select w-auto" onchange="this.form.submit()">
<option value="all">{{ __('كل الحالات') }}</option>
@foreach(['pending'=>__('معلق'),'partial'=>__('جزئي'),'paid'=>__('مدفوع')] as $k=>$v)<option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>@endforeach
</select>
</form>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))<a href="{{ route('admin.payments.create') }}" class="btn btn-primary"><i class="ki-outline ki-plus fs-2"></i> {{ __('فاتورة جديدة') }}</a>
<a href="{{ route('admin.payments.monthly-pdf', ['month' => request('month', date('Y-m'))]) }}" class="btn btn-light-danger"><i class="ki-outline ki-file-down fs-2"></i> PDF</a>@endif
</div></div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted"><th>{{ __('الطالب') }}</th><th>{{ __('الشهر') }}</th><th>{{ __('المطلوب') }}</th><th>{{ __('المدفوع') }}</th><th>{{ __('المتبقي') }}</th><th>{{ __('الحالة') }}</th><th>{{ __('الدافع') }}</th><th>{{ __('الطريقة') }}</th><th></th></tr></thead>
<tbody>@forelse($payments as $p)<tr>
<td class="fw-bold">{{ $p->student->name ?? '' }}<div class="text-muted fw-normal fs-8">{{ __($p->student->grade ?? '') }}</div></td>
<td>{{ $p->month }}</td><td>{{ $p->amount }}</td><td class="text-success">{{ $p->paid_amount }}</td>
<td class="text-danger">{{ $p->remaining }}</td>
<td>@if($p->status)<span class="badge badge-light-{{ $p->status->color }}">{{ $p->status->name_ar }}</span>@else — @endif</td>
<td class="fs-8">{{ $p->payer_label }}</td>
<td class="text-muted fs-8">{{ $p->method ?? '—' }}</td>
<td class="text-end"><span class="d-inline-flex gap-2">@if(in_array(auth('admin')->user()->type,['admin','teacher']))<a href="{{ route('admin.payments.edit',$p->id) }}" class="btn btn-sm btn-light-primary">{{ __('تحصيل') }}</a>@endif
@if(in_array(auth('admin')->user()->type,['parent','student']) && $p->remaining > 0)<form method="POST" action="{{ route('admin.onlinepay.checkout',$p->id) }}">@csrf<input type="hidden" name="provider" value="paymob" /><button class="btn btn-sm btn-success">💳 {{ __('ادفع أونلاين') }}</button></form>@endif</span></td>
</tr>@empty<tr><td colspan="9" class="text-center text-muted py-10">{{ __('لا توجد فواتير') }}</td></tr>@endforelse</tbody>
</table></div></div></div>
<div class="mt-7 d-flex justify-content-center">{{ $payments->appends(request()->query())->links() }}</div>
@endsection
