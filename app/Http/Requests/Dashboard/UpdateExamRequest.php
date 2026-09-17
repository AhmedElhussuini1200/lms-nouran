<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('admin')->user();
        return $user && in_array($user->type, ['admin', 'teacher']);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'grade' => ['required', Rule::in(['1_secondary', '2_secondary', '3_secondary'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'exam_date' => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'total_marks' => ['nullable', 'numeric', 'min:0'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'shuffle_questions' => ['nullable', 'boolean'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
            'passing_marks' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'anti_cheat' => ['nullable', 'boolean'],
        ];
    }
}
