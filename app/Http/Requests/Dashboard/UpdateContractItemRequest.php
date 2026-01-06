<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NumbersOnly;
use App\Models\ContarctItem;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContractItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return abilities()->contains('update_contractItems');
    }

    public function rules(): array
    {
           $contractItem = $this->route('contractItem');
      return [
            // 'item_description' => ['required', 'array'],
            'item_description.*' => [
                'required',
                'string',
                new NotNumbersOnly(),
                new ExistButDeleted(new ContarctItem()),
                  Rule::unique('contract_items', 'item_description')->ignore($contractItem->id),
                new NotNumbersOnly(),
            ],

            'description.*' => [
                'nullable',
                'string',
            ],

            // 'quantity' => ['required', 'array'],
            'quantity.*' => ['required', 'integer', 'min:1', new NumbersOnly()],

            // 'unit_price' => ['required', 'array'],
            'unit_price.*' => ['required', 'numeric', 'min:0', new NumbersOnly()],

            // 'unit_price_text' => ['required', 'array'],
            'unit_price_text.*' => ['required', 'string', 'max:255', new NotNumbersOnly()],

            // 'total_unit_price_text' => ['required', 'array'],
            'total_unit_price_text.*' => ['required', 'string', 'max:255', new NotNumbersOnly()],

            // 'total_price' => ['required', 'array'],
            'total_price.*' => ['required', 'numeric', 'min:0', new NumbersOnly()],

            // 'unit_id' => ['required', 'array'],
            'unit_id.*' => ['required', 'exists:unites,id'],

            'contract_id' => ['required', 'exists:contracts,id'],
        ];
    }
}
