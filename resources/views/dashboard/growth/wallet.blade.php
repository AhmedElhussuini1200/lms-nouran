@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header"><h3 class="card-title fw-bold"><i class="ki-outline ki-wallet fs-2 me-2"></i>{{ __('محفظتي') }}: {{ $wallet->balance }} {{ __('ج') }}</h3></div>
<div class="card-body p-0">
<div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0">
<thead><tr class="fw-bold text-muted fs-8"><th>{{ __('النوع') }}</th><th>{{ __('المبلغ') }}</th><th>{{ __('السبب') }}</th><th>{{ __('التاريخ') }}</th></tr></thead>
<tbody>
@foreach($txs as $t)<tr><td><span class="badge badge-light-{{ $t->kind==='credit'?'success':'danger' }}">{{ $t->kind }}</span></td><td>{{ $t->amount }}</td><td>{{ $t->reason }}</td><td>{{ $t->created_at->format('Y-m-d') }}</td></tr>@endforeach
</tbody>
</table></div>
</div>
</div>
@endsection
