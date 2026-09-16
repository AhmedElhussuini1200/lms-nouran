@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('فاتورة شهرية جديدة') }}</h3><div class="card-toolbar"><a href="{{ route('admin.payments.index') }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.payments.store') }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf
<div class="card-body"><div class="row g-6">
<div class="col-md-6"><label class="form-label required">{{ __('الصف الدراسي') }}</label><select id="grade-filter" class="form-select" data-control="select2" data-placeholder="{{ __('اختر الصف أولاً') }}"><option value="">{{ __('كل الصفوف') }}</option>@foreach($grades as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label required">{{ __('الطالب') }}</label><select name="student_id" id="student-select" class="form-select" data-control="select2" data-placeholder="{{ __('اختر') }}" required><option value="">{{ __('اختر') }}</option>@foreach($students as $s)<option value="{{ $s->id }}" data-grade="{{ $s->grade }}">{{ $s->name }} - {{ __($s->grade ?? '') }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label required">{{ __('الشهر') }}</label><input type="month" name="month" class="form-control" required value="{{ date('Y-m') }}" /></div>
<div class="col-md-4"><label class="form-label required">{{ __('المبلغ المطلوب') }}</label><input type="number" name="amount" id="amount-input" class="form-control" required min="0" step="0.01" /></div>
<div class="col-md-4"><label class="form-label">{{ __('المدفوع مقدماً') }}</label><input type="number" name="paid_amount" class="form-control" min="0" step="0.01" value="0" /></div>
<div class="col-md-4"><label class="form-label">{{ __('طريقة الدفع') }}</label><select data-placeholder="{{ __('طريقة الدفع') }}" data-control="select2" name="method" class="form-select"><option value="">{{ __('اختر') }}</option><option value="cash">{{ __('كاش') }}</option><option value="vodafone">{{ __('فودافون كاش') }}</option><option value="instapay">{{ __('انستاباي') }}</option><option value="card">{{ __('كارت') }}</option></select></div>
<div class="col-12"><label class="form-label">{{ __('ملاحظات') }}</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
</div></div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('إنشاء الفاتورة') }}</button></div>
</form></div>

{{-- آلة حاسبة المحاسب --}}
<div class="card mt-7"><div class="card-header"><h3 class="card-title fw-bold">🧮 {{ __('آلة حاسبة') }}</h3></div>
<div class="card-body"><div class="row g-6">
<div class="col-md-6">
<div class="border rounded p-4">
<div class="fw-bold mb-3">{{ __('حساب من الحصص') }}</div>
<div class="row g-3">
<div class="col-6"><label class="form-label fs-8">{{ __('عدد الحصص') }}</label><input type="number" id="calc-sessions" class="form-control" min="0" value="8" /></div>
<div class="col-6"><label class="form-label fs-8">{{ __('سعر الحصة') }}</label><input type="number" id="calc-price" class="form-control" min="0" step="0.01" value="50" /></div>
<div class="col-6"><label class="form-label fs-8">{{ __('خصم') }}</label><input type="number" id="calc-discount" class="form-control" min="0" step="0.01" value="0" /></div>
<div class="col-6 d-flex align-items-end"><div class="fs-4 fw-bolder">= <span id="calc-result" class="text-primary">400</span> {{ __('جنيه') }}</div></div>
</div>
<button class="btn btn-light-primary btn-sm mt-3" id="calc-apply">{{ __('اعتماد المبلغ في الفاتورة') }}</button>
</div>
</div>
<div class="col-md-6">
<div class="border rounded p-4">
<div class="fw-bold mb-3">{{ __('حاسبة سريعة') }}</div>
<input type="text" id="mini-display" class="form-control text-end fs-4 mb-2" dir="ltr" readonly value="" placeholder="0" />
<div class="d-grid gap-2" style="grid-template-columns: repeat(4, 1fr); display: grid;">
@foreach(['7','8','9','/','4','5','6','*','1','2','3','-','0','.','C','+'] as $b)
<button type="button" class="btn btn-light mini-btn" data-v="{{ $b }}">{{ $b }}</button>
@endforeach
</div>
<button type="button" class="btn btn-primary w-100 mt-2" id="mini-eq">=</button>
</div>
</div>
</div></div></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeFilter = document.getElementById('grade-filter');
    const studentSelect = document.getElementById('student-select');
    if (!gradeFilter || !studentSelect) return;
    const allOptions = Array.from(studentSelect.querySelectorAll('option[data-grade]'));
    gradeFilter.addEventListener('change', function() {
        const g = this.value;
        studentSelect.value = '';
        allOptions.forEach(o => { o.hidden = !!(g && o.dataset.grade !== g); });
        if (window.jQuery && $(studentSelect).data('select2')) $(studentSelect).val('').trigger('change');
    });

    // حاسبة الحصص
    const sEl = document.getElementById('calc-sessions'), pEl = document.getElementById('calc-price'),
          dEl = document.getElementById('calc-discount'), rEl = document.getElementById('calc-result');
    function recalc() {
        const total = (parseFloat(sEl.value) || 0) * (parseFloat(pEl.value) || 0) - (parseFloat(dEl.value) || 0);
        rEl.textContent = Math.max(0, total);
    }
    [sEl, pEl, dEl].forEach(el => el && el.addEventListener('input', recalc)); recalc();
    const applyBtn = document.getElementById('calc-apply');
    if (applyBtn) applyBtn.addEventListener('click', function() {
        document.getElementById('amount-input').value = rEl.textContent;
    });

    // حاسبة سريعة
    const disp = document.getElementById('mini-display');
    document.querySelectorAll('.mini-btn').forEach(b => b.addEventListener('click', function() {
        const v = this.dataset.v;
        if (v === 'C') { disp.value = ''; return; }
        disp.value += v;
    }));
    const eq = document.getElementById('mini-eq');
    if (eq) eq.addEventListener('click', function() {
        try {
            const val = disp.value.replace(/[^0-9+\-*/.() ]/g, '');
            const res = Function('"use strict";return (' + (val || '0') + ')')();
            disp.value = Math.round(res * 100) / 100;
        } catch (e) { disp.value = ''; }
    });
});
</script>
@endpush
