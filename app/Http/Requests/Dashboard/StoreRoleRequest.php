<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NotNumbersOnly;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_roles');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name_ar" => ['required', 'string', 'max:255', 'unique:roles', new NotNumbersOnly()],
            "name_en" => ['nullable', 'string', 'max:255', 'unique:roles', new NotNumbersOnly()],
            'abilities' => ['required', 'array', 'min:1'],
            // 'type' => ['nullable', 'in:admin,consultant,contractor'],
        ];
    }
}
