<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Priority;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\NumbersOnly;
use App\Rules\ExistButDeleted;

class StorerPiorityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_priorities');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255', 'unique:priorities', new NotNumbersOnly(), new ExistButDeleted(new Priority())],
            'name_en'    => ['nullable', 'string', 'max:255', 'unique:priorities', new NotNumbersOnly(), new ExistButDeleted(new Priority())],
            'dayPertirty' => ['required', new ExistButDeleted(new Priority()),'integer',new NumbersOnly() ],
            'color' => [
                'required',
                'string',
                'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'
            ],
        ];
    }
}
