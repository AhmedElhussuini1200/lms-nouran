<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return abilities()->contains('create_promo_codes');
    }
    public function rules()
    {
        return [
            'code' => 'required|string|max:6|unique:promo_codes,code',
            'promo_code_type_id' => 'required|in:1,2',
            'promo_applicable_to_id' => 'required|exists:promo_applicable_to,id',
            'value' => 'required|numeric|min:0',
            'starts_promo' => 'required|date|after_or_equal:today',
            'expires_at' => 'required|date|after:starts_promo',
            'usage_limit' => 'required|integer|min:0',
            'is_active' => 'required|boolean',

        ];
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'usage_limit' => $this->input('usage_limit', 0),
        ]);
    }
}
