<?php

namespace App\Http\Requests\Dashboard;

use App\Models\City;
use App\Models\Municipality;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_cities');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255','unique:cities,name_ar', new NotNumbersOnly(), new ExistButDeleted(new City())],
            'name_en'    => ['nullable', 'string', 'max:255','unique:cities,name_en', new NotNumbersOnly(), new ExistButDeleted(new City())],
        ];
    }
}
