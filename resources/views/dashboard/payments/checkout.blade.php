@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header"><h3 class="card-title fw-bold"><i class="ki-outline ki-wallet fs-2 me-2"></i>{{ __('ادفع فاتورة') }} {{ $payment->month }}</h3></div>
<div class="card-body">
<div class="d-flex justify-content-between align-items-center bg-light rounded p-5 mb-6">
<span class="text-muted fw-semibold">{{ __('المبلغ المتبقي') }}</span>
<span class="fs-2 fw-bolder text-danger">{{ $payment->remaining }} {{ __('ج') }}</span>
</div>
@if($teacher)
<div class="notice d-flex bg-light-info rounded border-info border border-dashed p-5 mb-6">
<i class="ki-outline ki-teacher fs-2x text-info me-4"></i>
<div>
<div class="fw-bold text-gray-900">{{ __('هتدفع لمين؟') }} {{ $teacher->brand_name ?? $teacher->name }}</div>
<div class="fs-7 text-gray-700 mt-1">{{ __('الطرق المتاحة') }}: <b>{{ $teacher->pay_methods ?? 'cash' }}</b></div>
@if($teacher->pay_details)<div class="fs-7 mt-1" dir="ltr" style="text-align:end">{{ $teacher->pay_details }}</div>@endif
<div class="fs-8 text-muted mt-1">{{ __('باقته') }}: {{ $teacher->monthly_classes }} {{ __('حصة') }} × {{ $teacher->price_per_class }} {{ __('ج') }}</div>
</div>
</div>
@endif
@if(!empty($gatewayUrl))
<div class="text-center mb-4"><a href="{{ $gatewayUrl }}" class="btn btn-lg btn-primary"><i class="ki-outline ki-credit-cart fs-2"></i> {{ __('ادفع الآن عبر') }} {{ $provider }}</a></div>
@else
<div class="notice d-flex bg-light rounded p-5 mb-6">
<i class="ki-outline ki-information-5 fs-2 text-muted me-3"></i>
<div class="fs-7 text-gray-700">{{ __('وضع تجريبي: اربط مفاتيح Paymob/Fawry من الإعدادات للتفعيل الحقيقي') }} — {{ __('المرجع') }}: <code>{{ $payment->transaction_ref }}</code></div>
</div>
@endif
<div class="border border-dashed rounded p-6 mx-auto" style="max-width:480px">
<h5 class="fw-bold mb-4">{{ __('حوّلت؟ أكّد الدفع') }}</h5>
<form method="POST" action="{{ route('admin.onlinepay.confirm', $payment->id) }}" enctype="multipart/form-data">@csrf
<label class="form-label fw-semibold">{{ __('المبلغ اللي حولته') }}</label>
<input type="number" name="paid_amount" class="form-control mb-4" value="{{ $payment->remaining }}" min="1" />
<label class="form-label fw-semibold">{{ __('صورة إيصال التحويل (إجباري)') }}</label>
<input type="file" name="receipt" class="form-control mb-4" accept="image/*" required />
<button class="btn btn-success w-100"><i class="ki-outline ki-check fs-2"></i> {{ __('تأكيد الدفع') }}</button>
<p class="text-muted fs-8 mt-3 mb-0">{{ __('هيتسجل إنك انت اللي دفعت، والمدرس هيتأكد ويعتمدها') }}</p>
</form>
</div>
</div>
</div>
@endsection
