<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NumbersOnly;
use Illuminate\Foundation\Http\FormRequest;

class StoreExtinguisheringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_extinguisher');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_number'       => ['required', 'string', 'max:100', 'unique:extinguishers,report_number'],
            'feeder'              => ['required', 'integer', new NumbersOnly()],
            'light_columns_id'     => ['required', 'exists:light_columns,id'],
            'priority_id'     => ['required', 'exists:priorities,id'],


            'city_id'             => ['required', 'exists:cities,id'],
            'district_id'         => ['required', 'exists:districts,id'],
            'neighborhood_id'     => ['required', 'exists:neighborhood,id'],
            'sector_id'           => ['required', 'exists:sectors,id'],
            'municipality_id'     => ['nullable', 'exists:municipalities,id'],
            'contract_id'     => ['required', 'exists:contracts,id'],

            'street'              => ['required', 'string'],
            'address'             => ['required', 'string'],

            'complaint_type_id'   => ['required', 'exists:complaint_types,id'],
            'extinguish_type_id'  => ['required', 'exists:extinguishing_types,id'],
            'station_id'          => ['required', 'exists:stations,id'],

            'description'         => ['required', 'string', 'max:500'],
            'report_time'         => ['required', 'date'],
            'response_duration'   => ['required', 'integer', 'min:0'],

            'created_by'          => ['nullable', 'exists:admins,id'],

            'image_before'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'image_after'         => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'company_id' => ['required', 'exists:companies,id'],
            

        ];
    }
}
