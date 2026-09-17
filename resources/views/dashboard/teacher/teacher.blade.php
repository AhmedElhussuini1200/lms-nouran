@extends('dashboard.partials.master')
@include('dashboard.partials.design-system')
@push('styles')
    <link href="{{ asset('assets/css/datatables' . (isDarkMode() ? '.dark' : '') . '.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/js/custom/datatables/datatables.bundle' . (isArabic() ? '.rtl' : '') . '.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        #kt_datatable th,
        #kt_datatable td {
            text-align: center !important;
        }
    </style>
@endpush
@section('title', 'لوحة تحكم المعلم')
@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">مرحباً، {{ auth('admin')->user()->name }}</h1>
        <p class="text-gray-600">لوحة تحكم المعلم</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">الحصص</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['courses'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">الواجبات</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['assignments'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">الامتحانات</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['exams'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">الفيديوهات</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['videos'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Courses -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">الحصص الأخيرة</h3>
            <div class="space-y-4">
                @forelse($recentCourses as $course)
                    <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                        <h4 class="text-sm font-medium text-gray-900">{{ $course->title }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $course->description }}</p>
                        <p class="text-xs text-gray-400 mt-2">تاريخ الحصة:
                            {{ $course->scheduled_at?->format('Y-m-d H:i') ?? '—' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">لا توجد حصص</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">الواجبات الأخيرة</h3>
            <div class="space-y-4">
                @forelse($recentAssignments as $assignment)
                    <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                        <h4 class="text-sm font-medium text-gray-900">{{ $assignment->title }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $assignment->description }}</p>
                        <p class="text-xs text-gray-400 mt-2">تاريخ الاستحقاق:
                            {{ $assignment->due_date?->format('Y-m-d H:i') ?? '—' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">لا توجد واجبات</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    let userType = @json(auth('admin')->user()->type ?? null);
    // console.log(userType)
</script>
<script src="{{ asset('assets/js/global/datatable-config.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
{{-- <script src="{{ asset('assets/js/datatables/admins.js') }}"></script> --}}
<script src="{{ asset('assets/js/datatables/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/global/crud-operations.js') }}"></script>
{{-- <script src="{{ asset('assets/js/global/scripts.js') }}"></script> --}}
@endpush
