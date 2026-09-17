@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title">{{ __('الشهادات') }}</h3></div>
<div class="card-body"><table class="table"><thead><tr><th>{{ __('الطالب') }}</th><th>{{ __('الامتحان') }}</th><th>{{ __('الدرجة') }}</th><th>{{ __('الكود') }}</th><th></th></tr></thead>
<tbody>@forelse($certificates as $c)<tr><td>{{ $c->student->name ?? '-' }}</td><td>{{ $c->exam->title ?? '-' }}</td><td>{{ $c->score }}</td><td><code>{{ $c->code }}</code></td>
<td><a class="btn btn-sm btn-primary" href="{{ route('admin.certificates.pdf', $c->id) }}">{{ __('PDF') }}</a></td></tr>
@empty<tr><td colspan="5" class="text-center text-muted">{{ __('لا توجد شهادات بعد') }}</td></tr>@endforelse</tbody></table>
{{ $certificates->links() }}</div></div>
@endsection
