@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title">👛 {{ __('محفظتي') }}: {{ $wallet->balance }} ج</h3></div>
<div class="card-body"><table class="table"><thead><tr><th>{{ __('النوع') }}</th><th>{{ __('المبلغ') }}</th><th>{{ __('السبب') }}</th><th>{{ __('التاريخ') }}</th></tr></thead>
<tbody>@foreach($txs as $t)<tr><td>{{ $t->kind }}</td><td>{{ $t->amount }}</td><td>{{ $t->reason }}</td><td>{{ $t->created_at->format('Y-m-d') }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
