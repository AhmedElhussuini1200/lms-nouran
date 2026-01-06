<?php

namespace App\Http\Requests\Dashboard;

use App\Models\City;
use App\Models\District;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDistrictsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_districts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $districtId = $this->route('district'); // هنا district id كـ string


        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('districts', 'name_ar')
                    ->ignore($districtId)
                    ->where(fn ($q) => $q->where('city_id', $this->city_id)),
                new NotNumbersOnly(),
                new ExistButDeleted(new District(), 'name_ar', 'city_id'),
            ],

            'name_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('districts', 'name_en')
                    ->ignore($districtId)
                    ->where(fn ($q) => $q->where('city_id', $this->city_id)),
                new NotNumbersOnly(),
                new ExistButDeleted(new District(), 'name_en', 'city_id'),
            ],

            'city_id' => 'required|exists:cities,id',
        ];

    }

}
