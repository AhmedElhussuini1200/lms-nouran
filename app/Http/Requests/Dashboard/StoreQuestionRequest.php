<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('admin')->user();
        return $user && in_array($user->type, ['admin', 'teacher']);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['mcq', 'true_false', 'essay'])],
            'question' => ['required', 'string'],
            'options' => ['nullable', 'array', 'min:2'],
            'options.*' => ['nullable', 'string', 'max:500'],
            'correct_answer' => ['nullable', 'string', 'max:500'],
            'marks' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
