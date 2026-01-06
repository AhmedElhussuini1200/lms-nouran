<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NumbersOnly;
use App\Models\ContarctItem;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return abilities()->contains('create_contractItems');
    }
    public function rules(): array
    {
        return [
            'item_description' => ['required', 'array'],
            'item_description.*' => [
                'required',
                'string',

                new NotNumbersOnly(),
                'unique:contract_items,item_description', // ممكن تتركه لو تحب
            ],

            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],

            'quantity' => ['required', 'array'],
            'quantity.*' => ['required', 'integer', 'gt:0','min:0', new NumbersOnly()],

            'unit_price' => ['required', 'array'],
            'unit_price.*' => ['required', 'numeric', 'min:1', new NumbersOnly()],

            'unit_price_text' => ['required', 'array'],
            'unit_price_text.*' => ['required', 'string', 'max:255', new NotNumbersOnly()],

            'total_unit_price_text' => ['required', 'array'],
            'total_unit_price_text.*' => ['required', 'string', 'max:255', new NotNumbersOnly()],

            'total_price' => ['required', 'array'],
            'total_price.*' => ['required', 'numeric', 'min:1', new NumbersOnly()],

            'unit_id' => ['required', 'array'],
            'unit_id.*' => ['required', 'exists:unites,id'],

            'contract_id' => ['required', 'exists:contracts,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->input('item_description', []) as $desc) {
                $rule = new ExistButDeleted(new ContarctItem());
                if (!$rule->passes('item_description', $desc)) {
                    $validator->errors()->add('item_description', "Item '{$desc}' already exists or deleted.");
                }
            }
        });
    }
}
