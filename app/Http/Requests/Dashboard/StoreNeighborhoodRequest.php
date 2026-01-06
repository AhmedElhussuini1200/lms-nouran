<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Municipality;
use App\Models\Neighborhood;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNeighborhoodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_neighborhood');
    }


    public function rules(): array
    {
        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('neighborhood', 'name_ar')
                    ->where(fn ($q) => $q->where('district_id', $this->district_id)),
                new NotNumbersOnly(),
                new ExistButDeleted(new Neighborhood(), 'name_ar', 'district_id'),
            ],

            'name_en' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('neighborhood', 'name_en')
                    ->where(fn ($q) => $q->where('district_id', $this->district_id)),
                new NotNumbersOnly(),
                new ExistButDeleted(new Neighborhood(), 'name_en', 'district_id'),
            ],

            'district_id' => 'required|exists:districts,id',
        ];
    }

}
