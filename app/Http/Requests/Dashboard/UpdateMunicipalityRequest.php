<?php

namespace App\Http\Requests\Dashboard;

use App\Models\City;
use App\Models\Municipality;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMunicipalityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_municipalities');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $municipality = request()->route('municipality'); // lowercase

        return [
    'name_ar' => [
           'required',
           'string',
           'max:255',
           Rule::unique('municipalities', 'name_ar')
               ->ignore($municipality->id)
               ->where(fn ($q) => $q->where('city_id', $this->city_id)),
           new NotNumbersOnly(),
           new ExistButDeleted(new Municipality(), 'name_ar', 'city_id'),
    ],

    

    'city_id' => 'required|exists:cities,id',
];
    }
}
