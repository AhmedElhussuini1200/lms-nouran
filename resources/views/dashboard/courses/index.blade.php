@extends('layouts.app')

@section('title', 'الحصص')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">الحصص</h1>
        @if(auth()->user()->isTeacher())
            <a href="{{ route('courses.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                إضافة حصة جديدة
            </a>
        @endif
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($courses as $course)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                <a href="{{ route('courses.show', $course) }}" class="hover:text-indigo-600">
                                    {{ $course->title }}
                                </a>
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $course->description }}</p>
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <span>الصف: {{ $course->grade }}</span>
                                <span class="mx-2">•</span>
                                <span>التاريخ: {{ $course->scheduled_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-gray-500">لا توجد حصص</p>
                </div>
            @endforelse
        </div>

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $courses->links() }}
        </div>
    </div>
</div>
@endsection

