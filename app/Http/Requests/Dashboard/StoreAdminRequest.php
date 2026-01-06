<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Admin;
use App\Rules\ExistPhone;
use App\Rules\PhoneNumber;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_admins');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'name' => ['required', 'string', 'max:255', new NotNumbersOnly(), new ExistButDeleted(new Admin())],
            'phone' => ['required', 'string', new PhoneNumber(), new ExistButDeleted(new Admin()), new ExistPhone(new Admin()), new ExistButDeleted(new Admin()), 'unique:admins'],
            'email' => ['required', 'email:rfc,dns,filter', new ExistButDeleted(new Admin()), 'unique:admins'],
            'roles' => ['required', 'array', 'min:1'],
            'password' => ['required', Password::min(8)->max(16)->letters()->numbers()],
            'password_confirmation' => ['required', 'same:password'],
            'start_contract_date' => ['nullable', 'date', 'before:end_contract_date'],
            'end_contract_date' => ['nullable', 'date', 'after:start_contract_date'],
            'type' => ['required', Rule::in(['admin', 'consultant', 'contractor'])],

            'company_id' => [
                'nullable',
                'required_unless:type,admin',
                'exists:companies,id'
            ],
            'contract_id' => ['nullable', 'exists:contracts,id'],
            'contract_type_id' => ['nullable', 'exists:contract_types,id'],
            'contract_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5048'],
            'city_id' => ['required', 'exists:cities,id'],
            'municipality_id' => ['nullable', 'exists:municipalities,id'],
            'sectors_id' => ['required', 'exists:sectors,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'neighborhood_id' => ['required', 'exists:neighborhood,id'],
            'reporting_to_id' => ['nullable', 'exists:admins,id'],
            // 'is_blocked' => ['required', 'in:0,1'],
            'signature_required' => ['nullable', 'boolean'],
            'has_stamp' => ['nullable', 'boolean'],
            'stamp' => [
                Rule::requiredIf(function () {
                    return request()->input('has_stamp') == 1;
                }),
                'required_if:has_stamp,1',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],


            'signature' => [
                Rule::requiredIf(function () {
                    return request()->input('signature_required') == 1;
                }),
                'required_if:signature_required,1',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],

        ];
    }
}
