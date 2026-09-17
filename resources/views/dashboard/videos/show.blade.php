@extends('dashboard.partials.master')

@section('content')
<div class="d-flex align-items-center gap-3 mb-7">
    <a href="{{ route('admin.videos.index') }}" class="btn btn-light btn-sm">
        <i class="ki-outline ki-arrow-right fs-2"></i> {{ __('الفيديوهات') }}
    </a>
    <h2 class="fw-bold mb-0">{{ $video->title }}</h2>
</div>

<div class="row g-6 g-xl-9">
    <div class="col-xl-8">
        <div class="card card-flush">
            <div class="card-body p-0">
                <div class="rounded-top overflow-hidden bg-dark">
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $video->video_url }}" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" oncontextmenu="return false"></iframe>
                    </div>
                </div>
                <div class="p-7">
                    <p class="text-gray-700 fs-6">{{ $video->description }}</p>
                    <div class="d-flex flex-wrap gap-5 mt-5 pt-5 border-top text-muted fs-7">
                        <span><i class="ki-outline ki-book-open me-1"></i>{{ __($video->grade) }}</span>
                        <span><i class="ki-outline ki-eye me-1"></i>{{ $video->views_count }} {{ __('مشاهدة') }}</span>
                        <span><i class="ki-outline ki-profile-circle me-1"></i>{{ $video->teacher->name ?? '' }}</span>
                        @if($video->duration_seconds)
                            <span><i class="ki-outline ki-time me-1"></i>{{ gmdate('H:i:s', $video->duration_seconds) }}</span>
                        @endif
                    </div>
                </div>
                @if(auth('admin')->user()->type === 'student')
                <div class="p-7 pt-0">
                    <div class="d-flex justify-content-between fs-8 mb-1"><span class="fw-bold">{{ __('تقدم المشاهدة') }}</span><span id="vp-label">{{ $myProgress->percent ?? 0 }}%</span></div>
                    <div class="progress h-8px"><div id="vp-bar" class="progress-bar bg-success" style="width:{{ $myProgress->percent ?? 0 }}%"></div></div>
                    @if($video->duration_seconds)<button id="vp-sim" class="btn btn-sm btn-light-success mt-3">{{ __('تسجيل 5 دقائق مشاهدة (تجريبي)') }}</button>@endif
                </div>
                @endif
            </div>
        </div>
        {{-- التعليقات --}}
        <div class="card card-flush mt-6"><div class="card-header"><h3 class="card-title">{{ __('التعليقات') }} (<span id="ccount">{{ $comments->count() }}</span>)</h3></div>
        <div class="card-body">
            <form id="cform" class="d-flex gap-2 mb-4"><input id="cbody" class="form-control" placeholder="{{ __('اكتب تعليقاً...') }}" maxlength="1000" required /><button class="btn btn-primary">{{ __('نشر') }}</button></form>
            <div id="clist">@foreach($comments as $c)<div class="border rounded p-3 mb-2"><b>{{ $c->author->name ?? '' }}</b> <span class="text-muted fs-8">{{ $c->created_at->diffForHumans() }}</span><p class="mb-0 mt-1">{{ $c->body }}</p></div>@endforeach</div>
        </div></div>
    </div>
    <div class="col-xl-4">
        @if(in_array(auth('admin')->user()->type, ['admin', 'teacher']))
            <div class="card mb-6">
                <div class="card-body d-flex gap-3">
                    <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-light-primary flex-fill">
                        <i class="ki-outline ki-pencil fs-2"></i> {{ __('تعديل') }}
                    </a>
                    <form action="{{ route('admin.videos.destroy', $video->id) }}" method="POST" class="ajax-form flex-fill" data-success-callback="onAjaxSuccess" >
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" type="submit" class="btn btn-light-danger w-100">
                            <i class="ki-outline ki-trash fs-2"></i> {{ __('حذف') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
        <div class="card">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('فيديوهات مشابهة') }}</h3></div>
            <div class="card-body py-5">
                @forelse($related as $rel)
                    <a href="{{ route('admin.videos.show', $rel->id) }}" class="d-flex gap-4 mb-5 text-hover-primary">
                        <span class="symbol symbol-80px flex-shrink-0">
                            <span class="symbol-label fs-2 fw-bold bg-light-primary text-primary">{{ mb_substr($rel->title, 0, 1) }}</span>
                        </span>
                        <span>
                            <span class="d-block fw-bold text-gray-900">{{ \Illuminate\Support\Str::limit($rel->title, 50) }}</span>
                            <span class="d-block text-muted fs-8 mt-1">{{ $rel->views_count }} {{ __('مشاهدة') }}</span>
                        </span>
                    </a>
                @empty
                    <p class="text-muted mb-0">{{ __('لا توجد فيديوهات مشابهة') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // تقدم المشاهدة (تجريبي: زر + إرسال دوري)
  const sim = document.getElementById('vp-sim');
  let watched = {{ $myProgress->watched_seconds ?? 0 }};
  const dur = {{ $video->duration_seconds ?: 3600 }};
  async function push() {
    const r = await fetch("{{ route('admin.videos.progress', $video->id) }}", {method:'POST', headers:{'X-CSRF-TOKEN':"{{ csrf_token() }}",'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify({watched_seconds: watched, duration: dur})});
    if (r.ok) { const j = await r.json(); document.getElementById('vp-bar').style.width = j.percent+'%'; document.getElementById('vp-label').textContent = j.percent+'%'; }
  }
  if (sim) sim.onclick = () => { watched += 300; push(); };
  // تعليقات
  const cf = document.getElementById('cform');
  if (cf) cf.onsubmit = async e => {
    e.preventDefault();
    const body = document.getElementById('cbody').value; document.getElementById('cbody').value = '';
    const r = await fetch("{{ route('admin.comments.store', ['type'=>'video','id'=>$video->id]) }}", {method:'POST', headers:{'X-CSRF-TOKEN':"{{ csrf_token() }}",'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify({body})});
    if (r.ok) { const j = await r.json(); const d = document.createElement('div'); d.className='border rounded p-3 mb-2'; d.innerHTML = '<b>'+(j.comment.author?.name||'')+'</b><p class="mb-0 mt-1"></p>'; d.querySelector('p').textContent = j.comment.body; document.getElementById('clist').prepend(d); }
  };
});
</script>
@endpush
@endsection
