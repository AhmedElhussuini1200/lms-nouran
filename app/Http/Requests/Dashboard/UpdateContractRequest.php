<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Contract;
use App\Rules\NumbersOnly;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_contracts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // dd($this->request);
        // العقد الحالي من الـ route parameter
        $contract = $this->route('contract');

        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contracts', 'name_ar')->ignore($contract->id),
                new NotNumbersOnly(),
                new ExistButDeleted(new Contract()),
            ],

            'type' => [
                'required',
                Rule::in(['consultant', 'contractor'])
            ],


            'district_id' => ['required', 'exists:districts,id'],

            'company_id'  => ['required', 'exists:companies,id'],

            'start_contract_date' => ['nullable', 'date', 'before:end_contract_date'],

            'end_contract_date'   => ['nullable', 'date', 'after:start_contract_date'],

            'contract_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:1024'],

            'contract_type_id' => ['nullable', 'exists:contract_types,id'],
            'contract_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2100', new NumbersOnly()],
            'contract_number' => ['required', 'integer', new NumbersOnly()],
            'is_active' => ['nullable', 'boolean'],
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
