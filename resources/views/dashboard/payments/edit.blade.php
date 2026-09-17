@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('تحصيل') }}: {{ $payment->student->name ?? '' }} - {{ $payment->month }}</h3><div class="card-toolbar"><a href="{{ route('admin.payments.index') }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.payments.update',$payment->id) }}" method="POST" id="payment-update-form" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<div class="card-body"><div class="row g-6">
<div class="alert alert-info">{{ __('المطلوب') }}: <b>{{ $payment->amount }}</b> • {{ __('المدفوع') }}: <b>{{ $payment->paid_amount }}</b> • {{ __('المتبقي') }}: <b>{{ $payment->remaining }}</b></div>
<div class="col-md-4"><label class="form-label required">{{ __('المبلغ المطلوب') }}</label><input type="number" name="amount" class="form-control" required min="0" step="0.01" value="{{ old('amount',$payment->amount) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('إجمالي المدفوع') }}</label><input type="number" name="paid_amount" class="form-control" min="0" step="0.01" value="{{ old('paid_amount',$payment->paid_amount) }}" /></div>
<div class="col-md-4"><label class="form-label">{{ __('طريقة الدفع') }}</label><select data-placeholder="{{ __('طريقة الدفع') }}" data-control="select2" name="method" class="form-select"><option value="">{{ __('اختر') }}</option>@foreach(['cash'=>__('كاش'),'vodafone'=>__('فودافون كاش'),'instapay'=>__('انستاباي'),'card'=>__('كارت')] as $k=>$v)<option value="{{ $k }}" {{ old('method',$payment->method)==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">{{ __('الدافع') }}</label><input type="text" name="payer_name" class="form-control" placeholder="{{ __('مثال: والد الطالب') }}" value="{{ old('payer_name', $payment->payer_name ?? $payment->payer->name ?? '') }}" /></div>
@if($payment->payer)<div class="col-md-8"><div class="alert alert-light-success mb-0">{{ __('الدافع المسجل') }}: <b>{{ $payment->payer_label }}</b></div></div>@endif
<div class="col-12"><label class="form-label">{{ __('ملاحظات') }}</label><textarea name="notes" class="form-control" rows="3">{{ old('notes',$payment->notes) }}</textarea></div>
</div></div>
</form>
<div class="card-footer d-flex justify-content-between">
<form action="{{ route('admin.payments.destroy',$payment->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" class="btn btn-light-danger">{{ __('حذف') }}</button></form>
<button class="btn btn-primary" form="payment-update-form">{{ __('حفظ التحصيل') }}</button>
</div>
</div>
@endsection
