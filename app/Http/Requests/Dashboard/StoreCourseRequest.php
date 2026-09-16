<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('admin')->user();
        return $user && in_array($user->type, ['admin', 'teacher']);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('courses', 'title')->where(fn ($q) => $q->where('grade', $this->input('grade'))->where('teacher_id', auth('admin')->id()))],
            'description' => ['nullable', 'string'],
            'grade' => ['required', Rule::in(['1_secondary', '2_secondary', '3_secondary'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
