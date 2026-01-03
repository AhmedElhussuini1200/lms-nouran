@extends('layouts.app')

@section('title', 'الامتحانات')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">الامتحانات</h1>
        @if(auth()->user()->isTeacher())
            <a href="{{ route('exams.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                إضافة امتحان جديد
            </a>
        @endif
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($exams as $exam)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                <a href="{{ route('exams.show', $exam) }}" class="hover:text-indigo-600">
                                    {{ $exam->title }}
                                </a>
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">{{ Str::limit($exam->description, 150) }}</p>
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <span>الصف: {{ $exam->grade }}</span>
                                <span class="mx-2">•</span>
                                <span>تاريخ الامتحان: {{ $exam->exam_date->format('Y-m-d H:i') }}</span>
                                <span class="mx-2">•</span>
                                <span>المدة: {{ $exam->duration_minutes }} دقيقة</span>
                                <span class="mx-2">•</span>
                                <span>الدرجة الكلية: {{ $exam->total_marks }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-gray-500">لا توجد امتحانات</p>
                </div>
            @endforelse
        </div>

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $exams->links() }}
        </div>
    </div>
</div>
@endsection

