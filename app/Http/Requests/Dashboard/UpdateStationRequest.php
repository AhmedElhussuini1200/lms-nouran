<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Station;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // نفس منطق الصلاحيات زي الـ Store
        return abilities()->contains('update_stations');
    }

    public function rules(): array
    {
        $stationId = $this->route('station'); // اسم الـ parameter في الـ route

        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',

                new NotNumbersOnly(),
                new ExistButDeleted(new Station()),
            ],
            'plate_number' => ['nullable', 'string', 'max:20'],

            'description' => ['required', 'string', 'max:255', new NotNumbersOnly(), new ExistButDeleted(new Station())],
            'address' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'exists:cities,id'],
            'municipality_id'  => 'nullable|exists:municipalities,id',
            'district_id' => 'required|exists:districts,id',
            'sector_id' => 'required|exists:sectors,id',
            'neighborhood_id'  => 'required|exists:neighborhood,id',
            'status_id' => ['nullable', 'exists:status,id'], // مهم جداً
            'is_active' => 'required|boolean',
            'established_at' => 'required|date',
            'last_maintenance' => 'required|date',
            'x' => ['nullable', 'numeric'],
            'y' => ['nullable', 'numeric'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,bmp', 'max:2048'],
        ];
    }
}
