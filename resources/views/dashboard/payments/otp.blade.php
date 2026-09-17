@extends('dashboard.partials.master')
@section('content')
<div class="card mx-auto" style="max-width:480px">
<div class="card-body text-center py-10">
<span class="symbol symbol-60px mx-auto mb-4"><span class="symbol-label bg-light-success"><i class="ki-outline ki-shield-tick fs-2x text-success"></i></span></span>
<h3 class="fw-bold">{{ __('كود التحقق') }}</h3>
<p class="text-muted fs-7">{{ __('بعتنا كود 6 أرقام على واتسابك — دخله للتأكيد') }} ({{ __('الفاتورة') }} {{ $payment->month }})</p>
@if(session('error_message'))<div class="alert alert-danger">{{ session('error_message') }}</div>@endif
<form method="POST" action="{{ route('admin.onlinepay.verify', $payment->id) }}">@csrf
<input type="text" name="code" class="form-control form-control-lg text-center mb-4" inputmode="numeric" maxlength="6" placeholder="••••••" required dir="ltr" />
<button class="btn btn-success btn-lg w-100">{{ __('تحقق وادفع') }}</button>
</form>
<form method="POST" action="{{ route('admin.onlinepay.confirm', $payment->id) }}" class="mt-3">@csrf
<input type="hidden" name="paid_amount" value="{{ $payment->remaining }}" />
<button class="btn btn-light btn-sm">{{ __('ابعت الكود تاني') }}</button>
</form>
</div>
</div>
@endsection
