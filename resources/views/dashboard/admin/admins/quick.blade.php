@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header"><h3 class="card-title fw-bold"><i class="ki-outline ki-plus fs-2 me-2"></i>{{ __('إضافة سريعة لطلبة') }}</h3>
<span class="card-toolbar text-muted fs-8">{{ __('كلمة المرور الافتراضية') }}: <code dir="ltr">12345678</code></span></div>
<form method="POST" action="{{ route('admin.admins.quick-store') }}">@csrf
<div class="card-body p-0">
<div class="table-responsive"><table class="table align-middle gy-3 mb-0" id="repeater-table">
<thead><tr class="fw-bold text-muted fs-8"><th>#</th><th>{{ __('الاسم') }}</th><th>{{ __('البريد') }}</th><th>{{ __('الهاتف') }}</th><th>{{ __('الصف') }}</th><th>{{ __('المدرس') }}</th><th></th></tr></thead>
<tbody id="repeater-body"></tbody>
</table></div>
<div class="p-5"><button type="button" id="repeater-add" class="btn btn-light-primary btn-sm"><i class="ki-outline ki-plus fs-4"></i> {{ __('صف جديد') }}</button></div>
</div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ الكل') }}</button></div>
</form>
</div>
<template id="repeater-tpl">
<tr>
<td class="rn fw-bold text-muted"></td>
<td><input name="rows[__i__][name]" class="form-control form-control-sm" required /></td>
<td><input name="rows[__i__][email]" type="email" class="form-control form-control-sm" dir="ltr" required /></td>
<td><input name="rows[__i__][phone]" class="form-control form-control-sm" dir="ltr" /></td>
<td><select name="rows[__i__][grade]" class="form-select form-select-sm">@foreach($grades as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></td>
<td><select name="rows[__i__][teacher_id]" class="form-select form-select-sm"><option value="">{{ __('بدون') }}</option>@foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->name }}{{ $t->subject ? ' - '.$t->subject : '' }}</option>@endforeach</select></td>
<td><button type="button" class="btn btn-sm btn-icon btn-light-danger row-del"><i class="ki-outline ki-trash fs-4"></i></button></td>
</tr>
</template>
@endsection
@push('scripts')
<script>
(function(){
let i = 0;
const body = document.getElementById('repeater-body');
const tpl = document.getElementById('repeater-tpl').innerHTML;
function renum(){ body.querySelectorAll('tr').forEach((tr,idx)=>{ tr.querySelector('.rn').textContent = idx+1; }); }
function add(){ body.insertAdjacentHTML('beforeend', tpl.replaceAll('__i__', i++)); renum(); }
document.getElementById('repeater-add').addEventListener('click', add);
body.addEventListener('click', e=>{ const b = e.target.closest('.row-del'); if(b){ b.closest('tr').remove(); renum(); } });
add(); add(); add();
})();
</script>
@endpush
