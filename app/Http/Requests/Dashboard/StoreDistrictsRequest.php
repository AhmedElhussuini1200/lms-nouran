<?php

namespace App\Http\Requests\Dashboard;

use App\Models\District;
use App\Models\Municipality;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDistrictsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_districts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'name_ar' => [
               'required',
               'string',
               'max:255',
               Rule::unique('districts', 'name_ar')
                   ->where(fn ($q) => $q->where('city_id', $this->city_id)),
               new NotNumbersOnly(),
               new ExistButDeleted(new District(), 'name_ar', 'city_id'),
           ],

           'name_en' => [
               'nullable',
               'string',
               'max:255',
               Rule::unique('districts', 'name_en')
                   ->where(fn ($q) => $q->where('city_id', $this->city_id)),
               new NotNumbersOnly(),
               new ExistButDeleted(new District(), 'name_en', 'city_id'),
           ],

           'city_id' => 'required|exists:cities,id',
];
    }
}
