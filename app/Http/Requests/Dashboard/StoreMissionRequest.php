<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PhoneNumber;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\Mission;
use App\Rules\ExistPhone;

class StoreMissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_missions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'complaint_number' => 'required|string|max:50|unique:missions,complaint_number',
            'customer_name'    => ['required', 'string', 'max:150', new NotNumbersOnly(), new ExistButDeleted(new Mission())],
            'customer_phone'   => ['required', 'string', 'max:10', new PhoneNumber(), new ExistPhone(new Mission())],
            'address'          => 'required|string|max:255',
            'street'           => 'required|string|max:150',
            'plot_number'      => 'required|string|max:50',
            'warning_icon'     => 'nullable|string|max:50',
            'qrcode'           => 'nullable|string|unique:missions,id',
            'is_completed'     => 'sometimes|boolean',
            'completed_at'     => 'nullable|date',
            'location_lat'     => 'required|numeric|between:-90,90',
            'location_long'    => 'required|numeric|between:-180,180',
            'title' => 'nullable',
            'description' => 'nullable',
            'statue_id'        => 'nullable|exists:status,id',

            // foreign keys
            'license_type_id'  => 'required|exists:license_type,id',
            'municipality_id'  => 'nullable|exists:municipalities,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'sector_id' => 'required|exists:sectors,id',
            'priority_id'      => 'required|exists:priorities,id',
            'neighborhood_id'  => 'required|exists:neighborhood,id',
            'category_id'      => 'required|exists:categories,id',
            'station_id'        => 'nullable',
            'light_columns_id'        => 'nullable',

            'actions' => 'required|array|min:1',
            'actions.*.action_id' => 'required|string|max:150',
            'actions.*.count' => 'required|integer|min:1',
            'feeder' => 'required|integer|min:1',

        ];
    }
}
