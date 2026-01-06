<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\ExtinguishingType;
use Illuminate\Foundation\Http\FormRequest;

class StoreExtinguishingTypesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
               return abilities()->contains('create_extinguishingtypes');

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255', 'unique:extinguishing_types', new NotNumbersOnly(), new ExistButDeleted(new ExtinguishingType())],
            'complaint_type_id'=>['required','exists:complaint_types,id'],
            'color' => [
                'required',
                'string',
                'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'
            ],
        ];
    }
}
