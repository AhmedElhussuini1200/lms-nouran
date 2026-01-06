<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;

class StoreItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_items');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string'],

            'status_id'      => ['nullable', 'exists:status,id'],
            'unit_id'        => ['required', 'exists:unites,id'],
            'warehouse_id'   => ['required', 'exists:warehouses,id'],

            'reason'         => ['nullable', 'string', 'max:255'],
            'created_by' => ['nullable', 'exists:admins,id'],

            'approved_by'    => ['nullable', 'exists:admins,id'],
            'rejected_by'    => ['nullable', 'exists:admins,id'],
            'rejected_reason' => ['nullable', 'string', 'max:255'],
            'quantity' => ['integer',  'nullable'],

            'is_approved'    => 'boolean',
        ];
    }
}
