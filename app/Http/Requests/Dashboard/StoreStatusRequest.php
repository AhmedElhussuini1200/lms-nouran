<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Status;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class StoreStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_status');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255', 'unique:status', new NotNumbersOnly(), new ExistButDeleted(new Status())],
            'name_en'    => ['nullable', 'string', 'max:255', 'unique:status', new NotNumbersOnly(), new ExistButDeleted(new Status())],
            'color' => [
                'required',
                'string',
                'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'
            ],
        ];
    }
}
