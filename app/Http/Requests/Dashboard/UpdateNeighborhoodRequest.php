<?php

namespace App\Http\Requests\Dashboard;

use App\Models\City;
use App\Models\District;
use App\Models\Neighborhood;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNeighborhoodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_neighborhood');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $neighborhood = request()->route('neighborhood');

        return [
        'name_ar' => [
           'required',
           'string',
           'max:255',
           Rule::unique('neighborhood', 'name_ar')
               ->ignore($neighborhood->id)
               ->where(fn ($q) => $q->where('district_id', $this->district_id)),
           new NotNumbersOnly(),
           new ExistButDeleted(new Neighborhood(), 'name_ar', 'district_id'),
        ],

        'name_en' => [
           'required',
           'string',
           'max:255',
           Rule::unique('neighborhood', 'name_en')
               ->ignore($neighborhood->id)
               ->where(fn ($q) => $q->where('district_id', $this->district_id)),
           new NotNumbersOnly(),
           new ExistButDeleted(new Neighborhood(), 'name_en', 'district_id'),
        ],

        'district_id' => 'required|exists:districts,id',
    ];
    }
}
