<?php

namespace App\Http\Requests\Dashboard;

use App\Models\City;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_cities');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $city = request()->route('city');

        return [
            'name_ar'    => [ 'required','string','max:255','unique:cities,name_ar,' . $city->id ,new NotNumbersOnly(), new ExistButDeleted(new City())],
            'name_en'    => [ 'required','string','max:255','unique:cities,name_en,' . $city->id,new NotNumbersOnly(), new ExistButDeleted(new City())],
        ];
    }
}
