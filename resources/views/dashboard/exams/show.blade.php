@extends('dashboard.partials.master')
@section('content')
<div class="d-flex align-items-center gap-3 mb-7"><a href="{{ route('admin.exams.index') }}" class="btn btn-light btn-sm"><i class="ki-outline ki-arrow-right fs-2"></i> {{ __('الامتحانات') }}</a><h2 class="fw-bold mb-0">{{ $exam->title }}</h2>
<span class="badge badge-light-info">{{ $exam->questions->count() }} {{ __('سؤال') }}</span>
<span class="badge badge-light-success">{{ __('الدرجة') }}: {{ $exam->questions->sum('marks') ?: $exam->total_marks }}</span>
@if($exam->duration_minutes)<span class="badge badge-light-warning"><i class="ki-outline ki-time me-1"></i>{{ $exam->duration_minutes }} {{ __('دقيقة') }}</span>@endif</div>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<div class="card mb-6" style="border-right: 3px solid #1b84ff"><div class="card-body py-4 px-6 d-flex align-items-center gap-3">
<span class="symbol symbol-40px"><span class="symbol-label bg-light-primary fw-bold">1</span></span>
<span class="fw-bold">{{ __('بيانات الامتحان') }} ✓</span>
<span class="text-muted">→</span>
<span class="symbol symbol-40px"><span class="symbol-label {{ $exam->questions->count() ? 'bg-light-success' : 'bg-light-warning' }} fw-bold">2</span></span>
<span class="fw-bold">{{ __('أضف الأسئلة بالأسفل') }}{{ $exam->questions->count() ? ' ✓' : '' }}</span>
</div></div>
@endif
<div class="row g-6">
<div class="col-xl-8">
<div class="card mb-6"><div class="card-body p-7">
<p class="text-gray-700">{{ $exam->description }}</p>
<div class="d-flex flex-wrap gap-5 mt-5 pt-5 border-top text-muted fs-7"><span>{{ __($exam->grade) }}</span><span>{{ $exam->exam_date?->format('Y-m-d H:i') }}</span><span>{{ $exam->duration_minutes }} {{ __('دقيقة') }}</span><span>{{ $exam->teacher->name ?? '' }}</span></div>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<div class="d-flex gap-3 mt-6"><a href="{{ route('admin.exams.edit',$exam->id) }}" class="btn btn-light-primary">{{ __('تعديل') }}</a>
<form action="{{ route('admin.exams.destroy',$exam->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" class="btn btn-light-danger">{{ __('حذف') }}</button></form></div>
@endif
</div></div>

{{-- أسئلة الامتحان (عرض للطالب أثناء الحل + إدارة للمدرس) --}}
<div class="card mb-6">
<div class="card-header"><h3 class="card-title">{{ __('أسئلة الامتحان') }}</h3></div>
<div class="card-body py-5">
@forelse($exam->questions as $i => $q)
<div class="border rounded p-4 mb-4">
<div class="d-flex justify-content-between align-items-center mb-2">
<span class="fw-bold">{{ __('سؤال') }} {{ $i + 1 }}</span>
<span class="d-flex gap-2 align-items-center">
<span class="badge badge-light-info">{{ $q->type === 'mcq' ? __('اختيارات') : ($q->type === 'true_false' ? __('صح/خطأ') : __('مقالي')) }}</span>
<span class="badge badge-light-success">{{ $q->marks }} {{ __('درجات') }}</span>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<form action="{{ route('admin.questions.destroy',$q->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf @method('DELETE')<button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" class="btn btn-sm btn-light-danger">{{ __('حذف') }}</button></form>
@endif
</span>
</div>
<p class="fw-semibold mb-3">{{ $q->question }}</p>
@if($q->type === 'mcq' && $q->options)
<ul class="list-unstyled mb-0">
@foreach($q->options as $idx => $opt)
<li class="py-1 fs-7">{{ ['أ','ب','ج','د','هـ'][$idx] ?? ($idx+1) }}) {{ $opt }}</li>
@endforeach
</ul>
@endif
</div>
@empty
<p class="text-muted">{{ __('لم تتم إضافة أسئلة بعد — أضف الأسئلة من الأسفل') }}</p>
@endforelse
</div>
</div>

@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<div class="card mb-6"><div class="card-header"><h3 class="card-title">{{ __('إضافة سؤال') }}</h3></div>
<form action="{{ route('admin.exams.questions.store',$exam->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf
<div class="card-body"><div class="row g-4">
<div class="col-md-4"><label class="form-label required">{{ __('النوع') }}</label>
<select data-placeholder="{{ __('النوع') }}" data-control="select2" name="type" class="form-select" id="q-type" required>
<option value="mcq">{{ __('اختيارات') }}</option>
<option value="true_false">{{ __('صح / خطأ') }}</option>
<option value="essay">{{ __('مقالي') }}</option>
</select></div>
<div class="col-md-2"><label class="form-label">{{ __('الدرجة') }}</label><input type="number" name="marks" class="form-control" min="0" step="0.5" value="1" /></div>
<div class="col-12"><label class="form-label required">{{ __('نص السؤال') }}</label><textarea name="question" class="form-control" rows="2" required></textarea></div>
<div class="col-12" id="q-options">
<label class="form-label">{{ __('الاختيارات (سطر لكل اختيار)') }}</label>
<textarea name="options_raw" class="form-control" rows="3" placeholder="القاهرة&#10;الجيزة&#10;الإسكندرية"></textarea>
</div>
<div class="col-md-6" id="q-correct-wrap"><label class="form-label">{{ __('الإجابة الصحيحة (انسخ نص الاختيار)') }}</label><input type="text" name="correct_answer" class="form-control" /></div>
</div></div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('إضافة السؤال') }}</button></div>
</form></div>
@endif

@if(auth('admin')->user()->type==='student')
<div class="card"><div class="card-header"><h3 class="card-title">{{ __('حل الامتحان') }}</h3>
<div class="d-flex gap-2 align-items-center">
@if($exam->duration_minutes)<span class="badge badge-light-warning fs-6" id="exam-timer" data-minutes="{{ $exam->duration_minutes }}">--:--</span>@endif
@if($myResult)<span class="badge badge-light-{{ $myResult->status?->color ?? 'success' }}">{{ $myResult->status->name_ar ?? '' }}</span>@endif
</div></div>
@if($exam->questions->isEmpty())
<div class="card-body"><p class="text-muted">{{ __('لا توجد أسئلة بعد') }}</p></div>
@else
<form action="{{ route('admin.exams.submit',$exam->id) }}" method="POST" class="ajax-form" data-success-callback="onAjaxSuccess">@csrf
<div class="card-body">
@if($myResult)<div class="alert alert-{{ $myResult->status?->color ?? 'success' }}">{{ __('تم التسليم') }} @if(!is_null($myResult->marks_obtained)) • {{ __('درجتك') }}: <b>{{ $myResult->marks_obtained }}</b> @endif</div>@endif
@foreach($exam->questions as $i => $q)
<div class="border rounded p-4 mb-4">
<p class="fw-bold">{{ __('سؤال') }} {{ $i+1 }}: {{ $q->question }} <span class="badge badge-light-success ms-2">{{ $q->marks }}</span></p>
@if($q->type === 'mcq' && $q->options)
@foreach($q->options as $opt)
<label class="d-flex align-items-center gap-2 py-1"><input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" {{ ($myResult->answers[$q->id] ?? null) == $opt ? 'checked' : '' }} /> {{ $opt }}</label>
@endforeach
@elseif($q->type === 'true_false')
<label class="d-flex align-items-center gap-2 py-1"><input type="radio" name="answers[{{ $q->id }}]" value="صح" {{ ($myResult->answers[$q->id] ?? null) == 'صح' ? 'checked' : '' }} /> {{ __('صح') }}</label>
<label class="d-flex align-items-center gap-2 py-1"><input type="radio" name="answers[{{ $q->id }}]" value="خطأ" {{ ($myResult->answers[$q->id] ?? null) == 'خطأ' ? 'checked' : '' }} /> {{ __('خطأ') }}</label>
@else
<textarea name="answers[{{ $q->id }}]" class="form-control" rows="3">{{ $myResult->answers[$q->id] ?? '' }}</textarea>
@endif
</div>
@endforeach
</div><div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('تسليم الامتحان') }}</button></div></form>
@endif
</div>
@endif
</div>
<div class="col-xl-4">
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<div class="card"><div class="card-header"><h3 class="card-title">{{ __('النتائج') }} ({{ $exam->results->count() }})</h3></div>
<div class="card-body py-5">@forelse($exam->results as $r)
<div class="border rounded p-4 mb-4">
<div class="d-flex justify-content-between align-items-center"><div class="fw-bold">{{ $r->student->name ?? '' }}</div>
@if($r->status)<span class="badge badge-light-{{ $r->status->color }}">{{ $r->status->name_ar }}</span>@endif</div>
<div class="text-muted fs-8">{{ $r->submitted_at?->diffForHumans() }}</div>
<form action="{{ route('admin.results.grade',$r->id) }}" method="POST" class="ajax-form mt-3" data-success-callback="onAjaxSuccess">@csrf
<div class="d-flex gap-2"><input type="number" name="marks_obtained" class="form-control form-control-sm" value="{{ $r->marks_obtained }}" min="0" />
<select data-placeholder="{{ __('الحالة') }}" data-control="select2" name="status" class="form-select form-select-sm w-auto">
@foreach(['submitted'=>__('تم التسليم'),'under_review'=>__('قيد التصحيح'),'graded'=>__('تم التصحيح'),'returned'=>__('راجع للتعديل')] as $k=>$v)<option value="{{ $k }}" {{ $r->status?->slug==$k?'selected':'' }}>{{ $v }}</option>@endforeach
</select>
<button class="btn btn-sm btn-primary">{{ __('رصد') }}</button></div></form>
</div>@empty<p class="text-muted">{{ __('لا توجد نتائج') }}</p>@endforelse</div></div>
@endif
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSel = document.getElementById('q-type');
    const optBox = document.getElementById('q-options');
    const correctWrap = document.getElementById('q-correct-wrap');
    const rawArea = document.querySelector('textarea[name="options_raw"]');
    if (!typeSel) return;
    function toggle() {
        const isEssay = typeSel.value === 'essay';
        if (optBox) optBox.style.display = isEssay ? 'none' : '';
        if (correctWrap) correctWrap.style.display = isEssay ? 'none' : '';
    }
    typeSel.addEventListener('change', toggle); toggle();
    const form = typeSel.closest('form');
    if (form && rawArea) form.addEventListener('submit', function() {
        const lines = rawArea.value.split('\n').map(s => s.trim()).filter(Boolean);
        lines.forEach(l => {
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = 'options[]'; h.value = l;
            form.appendChild(h);
        });
    });
});
</script>
@endpush
