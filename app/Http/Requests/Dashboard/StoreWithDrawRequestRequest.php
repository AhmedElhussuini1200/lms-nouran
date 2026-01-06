<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreWithDrawRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_withDrawRequest');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'type' => 'nullable|in:mission,extinguisher',

            'mission_id' => 'nullable|exists:missions,id',
            'extinguisher_id' => 'nullable|exists:extinguishers,id',
            'warehouse_id' => 'nullable' | 'exists:warehouses,id',
            'file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5000',
            'quantity'    => ['required', 'integer', 'min:1'],
            'comment'     => 'nullable|string|max:2000',
        ];
    }
}
