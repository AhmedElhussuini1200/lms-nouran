@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
<h3 class="card-title fw-bold">{{ $course->title }} @if($course->is_live)<span class="badge badge-light-danger ms-2">{{ __('مباشر الآن') }}</span>@endif</h3>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))
<form method="POST" action="{{ route('admin.live.stop',$course->id) }}">@csrf<button class="btn btn-sm btn-danger">{{ __('إنهاء البث') }}</button></form>
@endif
</div>
<div class="card-body">
<div id="chat" class="border rounded p-4 mb-4 bg-light" style="height:300px;overflow-y:auto">
@foreach($messages as $m)<div class="py-1"><b>{{ $m->author->name ?? '' }}:</b> {{ $m->message }}</div>@endforeach
</div>
<form id="chatform" class="d-flex gap-2"><input id="msg" class="form-control" placeholder="{{ __('اكتب رسالة...') }}" required />
<button class="btn btn-primary"><i class="ki-outline ki-send fs-4"></i> {{ __('إرسال') }}</button></form>
</div>
</div>
<script>
let last={{ $messages->last()->id ?? 0 }};
document.getElementById('chatform').onsubmit=async e=>{e.preventDefault();
const v=document.getElementById('msg').value;document.getElementById('msg').value='';
let r=await fetch("{{ route('admin.live.message',$course->id) }}",{method:'POST',headers:{'X-CSRF-TOKEN':"{{ csrf_token() }}",'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({message:v})});
let j=await r.json();last=j.message.id;addMsg(j.message);};
function addMsg(m){let d=document.createElement('div');d.className='py-1';d.innerHTML='<b>'+(m.author?.name||'')+':</b> '+m.message;document.getElementById('chat').appendChild(d);}
setInterval(async ()=>{let r=await fetch("{{ route('admin.live.feed',$course->id) }}?after="+last);let j=await r.json();j.forEach(m=>{last=Math.max(last,m.id);addMsg(m);});},4000);
</script>
@endsection
