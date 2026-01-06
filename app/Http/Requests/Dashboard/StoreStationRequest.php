<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Station;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class StoreStationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_stations');
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
                'unique:stations,name_ar',
                new NotNumbersOnly(),
                new ExistButDeleted(new Station()),
            ],
            'plate_number' => ['nullable', 'string', 'max:20', 'unique:stations,plate_number'],

            'address' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255', new NotNumbersOnly(), new ExistButDeleted(new Station())],
            'city_id' => ['required', 'exists:cities,id'],
            'municipality_id' => ['nullable', 'exists:municipalities,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'sector_id' => ['required', 'exists:sectors,id'],
            'neighborhood_id' => ['required', 'exists:neighborhood,id'],
            'is_active' => 'required|boolean',
            'established_at' => 'required|date',
            'last_maintenance' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,bmp|max:2048',
            'x'     => 'nullable|numeric',
            'y'    => 'nullable|numeric',
            'status_id' => ['nullable', 'exists:status,id'],


        ];
    }
}
