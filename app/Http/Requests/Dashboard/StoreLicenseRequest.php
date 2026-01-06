<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\License;

class StoreLicenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_license_types');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'name_ar'    => ['required', 'string', 'max:255', 'unique:license_type', new NotNumbersOnly(), new ExistButDeleted(new License())],
            'name_en'    => ['nullable', 'string', 'max:255', 'unique:license_type', new NotNumbersOnly(), new ExistButDeleted(new License())],
        ];
    }
}
