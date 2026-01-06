<?php

namespace App\Http\Requests\Dashboard;


use App\Models\Contract;
use App\Rules\NumbersOnly;
use App\Models\Municipality;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_contracts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // dd($this->request);

        return [
            'name_ar'    => ['required', 'string', 'max:255', 'unique:contracts', new NotNumbersOnly(), new ExistButDeleted(new Contract())],
            'name_en'    => ['nullable', 'string', 'max:255', 'unique:contracts', new NotNumbersOnly(), new ExistButDeleted(new Contract())],

            'type' => [
                'required',
                Rule::in(['consultant', 'contractor'])
            ],
            'district_id' => ['required', 'exists:districts,id'],
            'company_id'  => ['required', 'exists:companies,id'],
            'start_contract_date' => ['required', 'date', 'before:end_contract_date'],
            'end_contract_date' => ['required', 'date', 'after:start_contract_date'],
            'contract_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20000'],
            'contract_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2100', new NumbersOnly()],
            'contract_number' => [
                'required',
                'string',
                'regex:/^\d+$/'
            ],


            'contract_type_id' => ['required', 'exists:contract_types,id'],
            'competition_number' => ['required', new NumbersOnly(), 'integer'],
            'total_amount' => [
                'required',
                'string',
               'regex:/^\d+(\.\d+)?$/'

            ],

        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'total_amount' => preg_replace('/[^\d.]/', '', $this->total_amount),
            'contract_number' => preg_replace('/\D/', '', $this->contract_number),
        ]);
    }
}
