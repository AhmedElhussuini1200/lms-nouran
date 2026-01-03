@extends('layouts.app')

@section('title', $exam->title)

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('exams.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">← العودة للامتحانات</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="mb-4">
            <p class="text-gray-600">{{ $exam->description }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">الصف:</span>
                <span class="text-gray-900 font-medium">{{ $exam->grade }}</span>
            </div>
            <div>
                <span class="text-gray-500">تاريخ الامتحان:</span>
                <span class="text-gray-900 font-medium">{{ $exam->exam_date->format('Y-m-d H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-500">المدة:</span>
                <span class="text-gray-900 font-medium">{{ $exam->duration_minutes }} دقيقة</span>
            </div>
            <div>
                <span class="text-gray-500">الدرجة الكلية:</span>
                <span class="text-gray-900 font-medium">{{ $exam->total_marks }}</span>
            </div>
            <div>
                <span class="text-gray-500">المعلم:</span>
                <span class="text-gray-900 font-medium">{{ $exam->teacher->name }}</span>
            </div>
        </div>
    </div>

    @if(auth()->user()->isStudent())
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">نتيجة الامتحان</h2>
            @if($result)
                <div class="mb-4 p-4 bg-green-50 rounded-lg">
                    <p class="text-green-800 font-medium">تم تقديم الامتحان</p>
                    <p class="text-sm text-green-600 mt-2">الدرجة: {{ $result->marks_obtained }} / {{ $exam->total_marks }}</p>
                    <p class="text-sm text-green-600 mt-1">تاريخ التقديم: {{ $result->submitted_at->format('Y-m-d H:i') }}</p>
                </div>
            @else
                <p class="text-gray-600 mb-4">لم يتم تقديم الامتحان بعد</p>
                @if(now() >= $exam->exam_date)
                    <a href="{{ route('exams.take', $exam) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                        بدء الامتحان
                    </a>
                @else
                    <p class="text-gray-500">الامتحان سيبدأ في {{ $exam->exam_date->format('Y-m-d H:i') }}</p>
                @endif
            @endif
        </div>
    @endif
</div>
@endsection

