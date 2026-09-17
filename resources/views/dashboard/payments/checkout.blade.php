@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-body py-10">
<div class="text-center mb-6">
<h3>💳 {{ __('ادفع فاتورة') }} {{ $payment->month }}</h3>
<p class="fs-5">{{ __('المبلغ المتبقي') }}: <b class="text-danger">{{ $payment->remaining }} ج</b> ({{ __('الطالب') }}: {{ $payment->student->name ?? '' }})</p>
</div>

{{-- تدفع لمين؟ --}}
@if($teacher)
<div class="alert alert-info d-flex gap-3 align-items-center">
<span class="fs-2">👨‍🏫</span>
<div>
<div class="fw-bold">{{ __('هتدفع لمين؟') }} {{ $teacher->brand_name ?? $teacher->name }}</div>
<div class="fs-8">{{ __('الطرق المتاحة') }}: <b>{{ $teacher->pay_methods ?? 'cash' }}</b></div>
@if($teacher->pay_details)<div class="fs-8 mt-1" dir="ltr" style="text-align:end">{{ $teacher->pay_details }}</div>@endif
<div class="fs-8 text-muted">{{ __('باقته') }}: {{ $teacher->monthly_classes }} {{ __('حصة') }} × {{ $teacher->price_per_class }} ج</div>
</div>
</div>
@endif

@if(!empty($gatewayUrl))
<div class="text-center mb-4"><a href="{{ $gatewayUrl }}" class="btn btn-primary btn-lg">💳 {{ __('ادفع الآن عبر') }} {{ $provider }}</a></div>
@else
<p class="text-center text-muted">{{ __('وضع تجريبي: اربط مفاتيح Paymob/Fawry من الإعدادات للتفعيل الحقيقي') }} — {{ __('المرجع') }}: <code>{{ $payment->transaction_ref }}</code></p>
@endif

{{-- تأكيد يدوي بعد التحويل --}}
<div class="border rounded p-5 mt-4 mx-auto" style="max-width:480px">
<h5 class="fw-bold mb-3">✅ {{ __('حوّلت؟ أكّد الدفع') }}</h5>
<form method="POST" action="{{ route('admin.onlinepay.confirm', $payment->id) }}">@csrf
<label class="form-label">{{ __('المبلغ اللي حولته') }}</label>
<input type="number" name="paid_amount" class="form-control mb-3" value="{{ $payment->remaining }}" min="1">
<button class="btn btn-success w-100">{{ __('تأكيد الدفع') }}</button>
<p class="text-muted fs-8 mt-2 mb-0">{{ __('هيتسجل إنك انت اللي دفعت، والمدرس هيتأكد ويعتمدها') }}</p></form>
</div>
</div></div>
@endsection
