@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">← العودة للحصص</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-4">
            <p class="text-gray-600">{{ $course->description }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">الصف:</span>
                <span class="text-gray-900 font-medium">{{ $course->grade }}</span>
            </div>
            <div>
                <span class="text-gray-500">تاريخ الحصة:</span>
                <span class="text-gray-900 font-medium">{{ $course->scheduled_at->format('Y-m-d H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-500">المعلم:</span>
                <span class="text-gray-900 font-medium">{{ $course->teacher->name }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

