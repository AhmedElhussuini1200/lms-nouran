<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\PhoneNumber;
use App\Rules\NotNumbersOnly;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validationKey = request()->query('type');
        $validations =  [
            "general" => [
                'address_en'    => ['required', 'string', 'max:255', new NotNumbersOnly()],
                'address_ar'    => ['required', 'string', 'max:255', new NotNumbersOnly()],
                'phone'    => ['required', new PhoneNumber()],
                'email'    => ['required', 'string', 'email:rfc,dns'],
            ],
            "terms" => [
                'terms_ar' => ['required', new NotNumbersOnly()],
                'terms_en' => ['required', new NotNumbersOnly()],
            ],
            "privacy" => [
                'privacy_policy_ar' => ['required', new NotNumbersOnly()],
                'privacy_policy_en' => ['required', new NotNumbersOnly()],
            ],
            "commission_tax" => [
                'owner_commition_percentage' => ['nullable', 'numeric'],
                'owner_commition_value' => ['nullable', 'numeric'],
                'vendor_commition_percentage' => ['nullable', 'numeric'],
                'vendor_commition_value' => ['nullable', 'numeric'],
                'tax' =>  ['required', 'numeric', 'gt:0']
            ],
            "penalties" => [
                'penalty_amount' => ['required', 'numeric'],
                'max_cancellations' => ['required', 'numeric'],
            ],
            "mobile_app" => [
                'android_exact_blocked_version' => ['nullable', 'string'],
                'android_min_supported_version' => ['nullable', 'string'],
                'android_maintenance_mode' => ['nullable', 'boolean'],
                'android_maintenance_message' => ['nullable', 'string'],
                'ios_exact_blocked_version' => ['nullable', 'string'],
                'ios_min_supported_version' => ['nullable', 'string'],
                'ios_maintenance_mode' => ['nullable', 'boolean'],
                'ios_maintenance_message' => ['nullable', 'string']
            ]
        ];
        return request()->isMethod('post') ? $validations[$validationKey] : [];
    }
    /**
     * Modify the input before validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'owner_commission' => $this->sanitizeNumber($this->owner_commission),
            'vendor_commission' => $this->sanitizeNumber($this->vendor_commission),
            'penalty_amount' => $this->sanitizeNumber($this->penalty_amount),
            'max_cancellations' => $this->sanitizeNumber($this->max_cancellations),
        ]);
    }

    /**
     * Remove commas from numeric inputs.
     */
    private function sanitizeNumber($value)
    {
        return $value !== null ? str_replace(',', '', $value) : null;
    }
}
