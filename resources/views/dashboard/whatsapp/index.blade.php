@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body py-6 px-6">
<div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
<div class="flex-grow-1">
<h2 class="fw-bold mb-2"><i class="ki-outline ki-whatsapp fs-2 text-success"></i> {{ __('إرسال واتساب يدوي') }}</h2>
<p class="text-muted mb-0">{{ __('اختر المستخدم واكتب الرسالة — تصل فوراً على واتساب') }}</p>
</div>
<div class="d-flex gap-2">
<a href="{{ route('admin.whatsapp.logs') }}" class="btn btn-light-info"><i class="ki-outline ki-time fs-4"></i> {{ __('السجل') }}</a>
<a href="{{ route('admin.whatsapp.templates') }}" class="btn btn-light-primary"><i class="ki-outline ki-file fs-4"></i> {{ __('القوالب') }}</a>
</div>
</div>
@if(app(\App\Services\WhatsappService::class)->enabled())
<div class="alert alert-success d-flex align-items-center mb-0"><i class="ki-outline ki-check-circle fs-2 me-2"></i>{{ __('خدمة الواتساب مفعلة — الرسائل تصل فوراً') }} ({{ config('services.whatsapp.provider') }})</div>
@else
<div class="alert alert-warning mb-0">{{ __('خدمة الواتساب غير مفعلة — الرسائل لن تصل. فعّلها من') }} <b dir="ltr">WHATSAPP_ENABLED=true + WHATSAPP_PHONE_ID</b> {{ __('في ملف .env (من حساب Meta للمطورين — WhatsApp Cloud API)') }}</div>
@endif
<div class="alert alert-info mt-3 mb-0">
<b>{{ __('التفعيل المجاني (CallMeBot)') }}:</b>
1) {{ __('احفظ رقم واتساب') }} <b dir="ltr">+34 644 10 55 84</b> {{ __('في جهات اتصالك') }}
2) {{ __('ابعت له رسالة') }}: <b dir="ltr">I allow callmebot to send me messages</b>
3) {{ __('هيرد عليك بمفتاح apikey — حطه في خانة مفتاح الواتساب للمستخدم أو في') }} <b dir="ltr">WHATSAPP_CALLMEBOT_KEY</b>
</div>

@if(isset($gateway))
<div class="alert {{ !empty($gateway['connected']) ? 'alert-success' : 'alert-warning' }} mt-3 mb-0 d-flex align-items-center gap-4">
@if(!empty($gateway['connected']))
<i class="ki-outline ki-check-circle fs-2"></i>
<span>{{ __('رقم الواتساب مربوط') }}: <b dir="ltr">{{ $gateway['number'] ?? '' }}</b> — {{ __('الرسائل تخرج من رقمك وتظهر في واتسابك') }}</span>
@else
<div>
<div class="fw-bold mb-2">{{ __('اربط رقم واتسابك: افتح واتساب ← الأجهزة المرتبطة ← ربط جهاز ← امسح الكود') }}</div>
<img id="wa-qr" src="{{ rtrim(config('services.whatsapp.gateway_url'), '/') }}/qr" style="width:220px;height:220px;background:#fff;padding:8px;border-radius:8px;" />
<div class="mt-2 text-muted fs-8" id="wa-qr-hint">{{ __('الكود يتحدث تلقائياً — بعد المسح الصفحة هتتحدث لوحدها') }}</div>
</div>
@push('scripts')
<script>
(function() {
    const base = @json(rtrim(config('services.whatsapp.gateway_url'), '/'));
    const img = document.getElementById('wa-qr');
    if (!img) return;
    // تحديث الكود كل 25 ثانية (صلاحيته قصيرة)
    setInterval(() => { img.src = base + '/qr?t=' + Date.now(); }, 25000);
    // فحص الاتصال كل 5 ثواني — لو اتربط نحدث الصفحة تلقائياً
    setInterval(async () => {
        try {
            const r = await fetch(base + '/status');
            const j = await r.json();
            if (j.connected) location.reload();
        } catch (e) {}
    }, 5000);
})();
</script>
@endpush
@endif
</div>
@endif
</div></div>

<div class="row g-6">
<div class="col-xl-8">
<div class="card"><div class="card-body">
<form action="{{ route('admin.whatsapp.send') }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf
<div class="row g-4">
<div class="col-md-6"><label class="form-label">{{ __('النوع') }}</label>
<select class="form-select" data-control="select2" data-placeholder="{{ __('النوع') }}" onchange="location.href='{{ route('admin.whatsapp.index') }}?type='+this.value">
<option value="all">{{ __('الكل') }}</option>
@foreach(['admin'=>__('إدمن'),'teacher'=>__('مدرس'),'student'=>__('طالب'),'parent'=>__('ولي أمر')] as $k=>$v)<option value="{{ $k }}" {{ request('type')==$k?'selected':'' }}>{{ $v }}</option>@endforeach
</select></div>
<div class="col-md-6"><label class="form-label required">{{ __('المستلمون (طالب أو أكثر)') }}</label>
<select name="admin_ids[]" id="wa-user" class="form-select" data-control="select2" data-placeholder="{{ __('اختر مستلماً أو أكثر') }}" multiple required>
@foreach($users as $u)<option value="{{ $u->id }}" data-phone="{{ $u->phone }}" {{ in_array($u->id, (array)$prefillUser) ? 'selected' : '' }}>{{ $u->name }} — {{ $u->phone }}</option>@endforeach
</select></div>
<div class="col-12"><label class="form-label required">{{ __('الرسالة') }}</label>
<textarea name="message" id="wa-message" class="form-control" rows="4" required>{{ $prefillMsg }}</textarea>
<div class="text-muted fs-8 mt-1"><span id="wa-count">0</span> / 1000</div></div>
</div>
<div class="d-flex justify-content-end gap-2 mt-4">
<a href="#" target="_blank" id="wa-open" class="btn btn-light-success"><i class="ki-outline ki-whatsapp fs-2"></i> {{ __('فتح واتساب مباشرة (تجربة فورية)') }}</a>
<button class="btn btn-success"><i class="ki-outline ki-whatsapp fs-2"></i> {{ __('إرسال') }}</button>
</div>
</form>
</div></div>
</div>
<div class="col-xl-4">
<div class="card"><div class="card-header"><h3 class="card-title">{{ __('قوالب جاهزة') }}</h3><a href="{{ route('admin.whatsapp.templates') }}" class="btn btn-sm btn-light">{{ __('إدارة') }}</a></div>
<div class="card-body py-5">
@foreach($templates as $title => $text)
<button class="btn btn-light w-100 mb-2 text-start wa-template" data-text="{{ $text }}">{{ $title }}</button>
@endforeach
</div></div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const msg = document.getElementById('wa-message'), count = document.getElementById('wa-count');
    if (msg) msg.addEventListener('input', () => count.textContent = msg.value.length);
    if (msg && count) count.textContent = msg.value.length;
    document.querySelectorAll('.wa-template').forEach(b => b.addEventListener('click', function() {
        msg.value = this.dataset.text; count.textContent = msg.value.length; msg.focus(); updateWaLink();
    }));

    // فتح واتساب مباشرة wa.me — تجربة فورية بدون API
    const waOpen = document.getElementById('wa-open'), userSel = document.getElementById('wa-user');
    function updateWaLink() {
        const selected = Array.from(userSel.selectedOptions).filter(o => o.value);
        const opt = selected[0];
        const phone = opt && opt.dataset.phone ? opt.dataset.phone.replace(/\D+/g, '') : '';
        let intl = phone;
        if (/^01\d{9}$/.test(phone)) intl = '2' + phone;
        else if (/^0020/.test(phone)) intl = phone.substring(2);
        waOpen.href = (intl && msg.value) ? 'https://wa.me/' + intl + '?text=' + encodeURIComponent(msg.value) : '#';
        waOpen.style.display = selected.length === 1 ? '' : 'none';
    }
    if (userSel) userSel.addEventListener('change', updateWaLink);
    if (msg) msg.addEventListener('input', updateWaLink);
    updateWaLink();
});
</script>
@endpush
