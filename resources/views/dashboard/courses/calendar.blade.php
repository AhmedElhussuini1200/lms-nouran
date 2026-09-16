@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
<div class="flex-grow-1"><h2 class="fw-bold mb-2">{{ __('تقويم الحصص') }}</h2><p class="text-muted mb-0">{{ __('اضغط على أي حصة للتفاصيل') }}</p></div>
<div class="mt-4 mt-md-0 d-flex gap-2">
<a href="{{ route('admin.courses.index') }}" class="btn btn-light">{{ __('عرض القائمة') }}</a>
@if(in_array(auth('admin')->user()->type,['admin','teacher']))<a href="{{ route('admin.courses.create') }}" class="btn btn-primary">{{ __('إضافة حصة') }}</a>@endif
</div></div></div>

<div class="card"><div class="card-body p-6">
<div id="sessions-calendar"></div>
</div></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('sessions-calendar');
    if (!el || typeof FullCalendar === 'undefined') return;
    const calendar = new FullCalendar.Calendar(el, {
        locale: "{{ app()->getLocale() }}",
        direction: "{{ getDirection() }}",
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
        buttonText: { today: "{{ __('اليوم') }}", month: "{{ __('شهر') }}", week: "{{ __('أسبوع') }}", list: "{{ __('قائمة') }}" },
        events: "{{ route('admin.courses.events') }}",
        eventClick: function(info) {
            if (info.event.url) { info.jsEvent.preventDefault(); window.location.href = info.event.url; }
        }
    });
    calendar.render();
});
</script>
@endpush
