<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NotNumbersOnly;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_products');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'max:100', new NotNumbersOnly()],
            'name_en' => ['required', 'max:100', new NotNumbersOnly()],
            'description_ar' => ['required', 'max:255', new NotNumbersOnly()],
            'description_en' => ['required', 'max:255', new NotNumbersOnly()],
            'product_price' => ['required', 'numeric', 'min:1'],
            'minimum_bid' => ['required', 'numeric', 'min:1'],
            'bid_price' => ['required', 'numeric', 'min:1'],
            'start_price' => ['required', 'numeric', 'min:1'],
            'ticket_price' => ['required', 'numeric', 'min:1'],
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'variations' => ['required', 'array', 'min:1'],
            'variations.*.id' => ['nullable', 'numeric', 'gt:0'],
            'variations.*.bidding_discount_percentage' => ['required', 'numeric', 'gt:0'],
            'variations.*.final_bidding_percentage' => ['required', 'numeric', 'gt:0'],
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|mimes:jpeg,jpg,png,gif,svg|max:512',
        ];
    }

    /**
     * Modify the input before validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'product_price' => $this->sanitizeNumber($this->product_price),
            'minimum_bid' => $this->sanitizeNumber($this->minimum_bid),
            'bid_price' => $this->sanitizeNumber($this->bid_price),
            'start_price' => $this->sanitizeNumber($this->start_price),
            'ticket_price' => $this->sanitizeNumber($this->ticket_price),
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
