@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-body text-center py-10">
<h3>💳 {{ __('الدفع عبر') }} {{ $provider }}</h3>
<p>{{ __('المبلغ المتبقي') }}: <b>{{ $payment->remaining }} ج</b> — {{ __('المرجع') }}: <code>{{ $payment->transaction_ref }}</code></p>
<p class="text-muted">{{ __('وضع تجريبي: اربط مفاتيح Paymob/Fawry من الإعدادات للتفعيل الحقيقي') }}</p>
<form method="POST" action="{{ route('admin.onlinepay.confirm', $payment->id) }}">@csrf
<input type="number" name="paid_amount" class="form-control w-25 mx-auto mb-3" value="{{ $payment->remaining }}" min="1">
<button class="btn btn-success">{{ __('تأكيد الدفع') }}</button></form></div></div>
@endsection
