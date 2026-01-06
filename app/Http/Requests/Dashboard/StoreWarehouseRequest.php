<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_warehouses');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255', 'unique:warehouses', new NotNumbersOnly(), new ExistButDeleted(new Warehouse())],
            'name_en'    => ['nullable', 'string', 'max:255', 'unique:warehouses', new NotNumbersOnly(), new ExistButDeleted(new Warehouse())],
            'district_id' => 'required|exists:districts,id',
            'city_id' => 'required|exists:cities,id',
            'municipality_id' => ['nullable', 'exists:municipalities,id'],
            'manager_id' => 'required|exists:admins,id',

        ];
    }
}
