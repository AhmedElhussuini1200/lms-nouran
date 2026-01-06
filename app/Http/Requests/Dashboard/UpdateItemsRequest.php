<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_items');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $item = $this->route('items'); // السجل الحالي

        return [
            'title'          => 'sometimes|required|string|max:255',
            'description'    => 'sometimes|nullable|string',

            'status_id'      => 'sometimes|nullable|exists:status,id',
            'unit_id'        => 'sometimes|required|exists:unites,id',
            'warehouse_id'   => 'sometimes|required|exists:warehouses,id',

            'reason'         => 'sometimes|nullable|string|max:255',

            'approved_by'    => 'sometimes|nullable|exists:admins,id',
            'rejected_by'    => 'sometimes|nullable|exists:admins,id',
            'rejected_reason' => 'sometimes|nullable|string|max:255',

            'is_approved'    => 'sometimes|boolean',
            'quantity' => ['integer',   'nullable'],

        ];
    }
}
