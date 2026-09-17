@extends('dashboard.partials.master')
@section('content')
<div class="card mx-auto" style="max-width:480px"><div class="card-body text-center py-10">
<h3>🔐 {{ __('كود التحقق') }}</h3>
<p class="text-muted">{{ __('بعتنا كود 6 أرقام على واتسابك — دخله للتأكيد') }} ({{ __('الفاتورة') }} {{ $payment->month }})</p>
@if(session('error_message'))<div class="alert alert-danger">{{ session('error_message') }}</div>@endif
<form method="POST" action="{{ route('admin.onlinepay.verify', $payment->id) }}">@csrf
<input type="text" name="code" class="form-control form-control-lg text-center mb-3" inputmode="numeric" maxlength="6" placeholder="••••••" required dir="ltr" />
<button class="btn btn-success btn-lg w-100">{{ __('تحقق وادفع') }}</button></form>
<form method="POST" action="{{ route('admin.onlinepay.confirm', $payment->id) }}" class="mt-3">@csrf
<input type="hidden" name="paid_amount" value="{{ $payment->remaining }}" />
<button class="btn btn-light btn-sm">{{ __('ابعت الكود تاني') }}</button></form>
</div></div>
@endsection
