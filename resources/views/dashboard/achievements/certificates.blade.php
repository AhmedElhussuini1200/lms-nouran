@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header"><h3 class="card-title fw-bold"><i class="ki-outline ki-award fs-2 me-2"></i>{{ __('الشهادات') }}</h3></div>
<div class="card-body p-0">
<div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>{{ __('الطالب') }}</th><th>{{ __('الامتحان') }}</th><th>{{ __('الدرجة') }}</th><th>{{ __('الكود') }}</th><th></th></tr></thead>
<tbody>
@forelse($certificates as $c)
<tr><td class="fw-bold">{{ $c->student->name ?? '-' }}</td><td>{{ $c->exam->title ?? '-' }}</td><td>{{ $c->score }}</td><td><code>{{ $c->code }}</code></td>
<td class="text-end"><a class="btn btn-sm btn-light-primary" href="{{ route('admin.certificates.pdf', $c->id) }}"><i class="ki-outline ki-file-down fs-4"></i> PDF</a></td></tr>
@empty
<tr><td colspan="5" class="text-center text-muted py-10">{{ __('لا توجد شهادات بعد') }}</td></tr>
@endforelse
</tbody>
</table></div>
</div>
</div>
<div class="mt-7 d-flex justify-content-center">{{ $certificates->links() }}</div>
@endsection
