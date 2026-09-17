@extends('dashboard.partials.master')
@section('content')
<div class="row g-6 g-xl-9">
<div class="col-xl-4">
<div class="card card-flush h-100">
<div class="card-header"><h3 class="card-title fw-bold">{{ __('سؤال للبنك') }}</h3></div>
<form method="POST" action="{{ route('admin.bank.store') }}">@csrf
<div class="card-body">
<div class="row g-4">
<div class="col-6"><label class="form-label fw-semibold">{{ __('الصف') }}</label><select name="grade" class="form-select" required><option value="1_secondary">{{ __('الأول الثانوي') }}</option><option value="2_secondary">{{ __('الثاني الثانوي') }}</option><option value="3_secondary">{{ __('الثالث الثانوي') }}</option></select></div>
<div class="col-6"><label class="form-label fw-semibold">{{ __('النوع') }}</label><select name="type" class="form-select"><option value="mcq">MCQ</option><option value="true_false">{{ __('صح/خطأ') }}</option><option value="essay">{{ __('مقالي') }}</option></select></div>
<div class="col-6"><label class="form-label fw-semibold">{{ __('الصعوبة') }}</label><select name="difficulty" class="form-select"><option value="easy">{{ __('سهل') }}</option><option value="medium" selected>{{ __('متوسط') }}</option><option value="hard">{{ __('صعب') }}</option></select></div>
<div class="col-6"><label class="form-label fw-semibold">{{ __('الموضوع') }}</label><input name="topic" class="form-control" placeholder="{{ __('مثال: الحركة') }}" /></div>
<div class="col-12"><label class="form-label fw-semibold">{{ __('السؤال') }}</label><textarea name="question" class="form-control" rows="2" required></textarea></div>
<div class="col-12"><label class="form-label fw-semibold">{{ __('الإجابة الصحيحة') }}</label><input name="correct_answer" class="form-control" /></div>
</div>
</div>
<div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ') }}</button></div>
</form>
</div>
</div>
<div class="col-xl-8">
<div class="card card-flush h-100">
<div class="card-header"><h3 class="card-title fw-bold">{{ __('بنك الأسئلة') }}</h3></div>
<div class="card-body p-0">
<div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>{{ __('السؤال') }}</th><th>{{ __('الصعوبة') }}</th><th>{{ __('الموضوع') }}</th></tr></thead>
<tbody>
@foreach($items as $it)<tr><td>{{ \Illuminate\Support\Str::limit($it->question,60) }}</td><td><span class="badge badge-light-{{ $it->difficulty==='easy'?'success':($it->difficulty==='hard'?'danger':'warning') }}">{{ __($it->difficulty) }}</span></td><td>{{ $it->topic }}</td></tr>@endforeach
</tbody>
</table></div>
</div>
</div>
<div class="mt-7 d-flex justify-content-center">{{ $items->links() }}</div>
</div>
</div>
@endsection
