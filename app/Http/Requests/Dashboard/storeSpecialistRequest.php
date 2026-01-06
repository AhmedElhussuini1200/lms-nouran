<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;

class storeSpecialistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_specialists');

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
             'name_ar'    => ['required', 'string', 'max:255', 'unique:specialists', new NotNumbersOnly()],
             'name_en'    => ['nullable', 'string', 'max:255', 'unique:specialists', new NotNumbersOnly()],
             'field_id' => 'required|exists:fields,id',
        ];
    }
}
