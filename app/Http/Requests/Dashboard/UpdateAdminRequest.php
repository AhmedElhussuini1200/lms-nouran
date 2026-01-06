<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Admin;
use App\Rules\ExistPhone;
use App\Rules\PhoneNumber;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_admins');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $admin = $this->route('admin');
        return [
            'name' => ['required', 'string', 'max:255', new NotNumbersOnly()],
            'phone' => ['required', Rule::unique('admins')->ignore($admin->id), new PhoneNumber(), new ExistPhone(new Admin(), $admin->id), new ExistButDeleted(new Admin())],
            'email' => ['required', 'string', 'email', Rule::unique('admins')->ignore($admin->id), new ExistButDeleted(new Admin())],
            'roles' => ['required', 'array', 'min:1'],
            'password' => ['nullable', 'sometimes', Password::min(8)->max(16)->letters()->numbers()],
            'password_confirmation' => ['nullable', 'sometimes', 'same:password'],
            'start_contract_date' => ['nullable', 'date', 'before:end_contract_date'],
            'end_contract_date' => ['nullable', 'date', 'after:start_contract_date'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'contract_type_id' => ['nullable', 'exists:contract_types,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'municipality_id' => ['nullable', 'exists:municipalities,id'],
            'sectors_id' => ['nullable', 'exists:sectors,id'],
            'neighborhood_id' => ['nullable', 'exists:neighborhood,id'],
            'reporting_to_id' => ['nullable', 'exists:admins,id'],
            // 'is_blocked' => ['nullable', 'in:0,1'],
            // 'stamp' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'type' => ['required', Rule::in(['admin', 'consultant', 'contractor'])],
            'contract_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5048'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'contract_id' => ['nullable', 'exists:contracts,id'],
            'signature_required' => ['nullable', 'boolean'],
            'has_stamp' => ['nullable', 'boolean'],
           'stamp' => [
    Rule::requiredIf(function () use ($admin) {
        // مطلوب فقط لو has_stamp = 1 ومافيش ختم موجود بالفعل
        return request()->input('has_stamp') == 1 && !$admin->stamp;
    }),
    'image',
    'mimes:jpeg,png,jpg,gif',
    'max:2048',
],

'signature' => [
    Rule::requiredIf(function () use ($admin) {
        // مطلوب فقط لو signature_required = 1 ومافيش توقيع موجود بالفعل
        return request()->input('signature_required') == 1 && !$admin->signature;
    }),
    'image',
    'mimes:jpeg,png,jpg,gif',
    'max:2048',
],


            // 'signature' => [
            //     Rule::requiredIf(function () {
            //         return request()->input('signature_required') == 1;
            //     }),
            //     'required_if:signature_required,1',
            //     'image',
            //     'mimes:jpeg,png,jpg,gif',
            //     'max:2048',
            // ],
        ];
    }
}
