@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title fw-bold">{{ __('تعديل الدور') }}: {{ $role->name_ar }}</h3><div class="card-toolbar"><a href="{{ route('admin.roles.index') }}" class="btn btn-light">{{ __('رجوع') }}</a></div></div>
<form action="{{ route('admin.roles.update',$role->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('PUT')
<div class="card-body"><div class="row g-6 mb-6">
<div class="col-md-6"><label class="form-label required">{{ __('الاسم عربي') }}</label><input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar',$role->name_ar) }}" /></div>
<div class="col-md-6"><label class="form-label required">{{ __('الاسم إنجليزي') }}</label><input type="text" name="name_en" class="form-control" required dir="ltr" value="{{ old('name_en',$role->name_en) }}" /></div>
</div>
@php $mine = $role->abilities->pluck('id')->toArray(); @endphp
@foreach($grouped as $category => $abilities)
<div class="border rounded p-4 mb-4">
<div class="d-flex justify-content-between align-items-center mb-3">
<span class="fw-bold">{{ $category }}</span>
<button type="button" class="btn btn-sm btn-light check-all" data-target="cat-{{ $loop->index }}">{{ __('تحديد الكل') }}</button>
</div>
<div class="row g-2 cat-{{ $loop->index }}">
@foreach($abilities as $a)
<div class="col-md-4"><label class="d-flex align-items-center gap-2 border rounded p-2">
<input type="checkbox" name="abilities[]" value="{{ $a->id }}" class="form-check-input m-0" {{ in_array($a->id,$mine)?'checked':'' }} />
<span class="fs-8">{{ $a->action }}</span>
</label></div>
@endforeach
</div>
</div>
@endforeach
</div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ التعديلات') }}</button></div>
</form></div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.check-all')) {
        const box = document.querySelector('.' + e.target.closest('.check-all').dataset.target);
        const boxes = box.querySelectorAll('input[type="checkbox"]');
        const all = Array.from(boxes).every(b => b.checked);
        boxes.forEach(b => b.checked = !all);
    }
});
</script>
@endpush
