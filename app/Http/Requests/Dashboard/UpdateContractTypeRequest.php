<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NotNumbersOnly;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContractTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_contract_types');
    }

    
    public function rules(): array
    {
        $contractType = request()->route('contract_type');
        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                'unique:contract_types,name_ar,' . $contractType->id,
                new NotNumbersOnly()
            ],
            'name_en' => [
                'required',
                'string',
                'max:255',
                'unique:contract_types,name_en,' . $contractType->id,
                new NotNumbersOnly()
            ],
        ];
    }
}
