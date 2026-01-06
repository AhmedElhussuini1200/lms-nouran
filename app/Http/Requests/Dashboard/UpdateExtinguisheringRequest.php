<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NumbersOnly;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExtinguisheringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_extinguisher');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        $extinguisher = $this->route('extinguisher'); // نفس اسم البارامتر في route

        return [
            'report_number' => [
                'nullable',
                'string',
                Rule::unique('extinguishers', 'report_number')->ignore($extinguisher?->id),
            ],
            'feeder' => ['required', 'integer', 'max:50', new NumbersOnly()],
            'priority_id' => ['required', 'exists:priorities,id'],
            'light_columns_id' => ['required', 'exists:light_columns,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'neighborhood_id' => ['required', 'exists:neighborhood,id'],
            'sector_id' => ['required', 'exists:sectors,id'],
            'municipality_id' => ['nullable', 'exists:municipalities,id'], // أو required حسب احتياجك
            'street' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'complaint_type_id' => ['required', 'exists:complaint_types,id'],
            'extinguish_type_id' => ['required', 'exists:extinguishing_types,id'],
            'station_id' => ['required', 'exists:stations,id'],
            'description' => ['required', 'string', 'max:500'],
            'report_time' => ['required', 'date'],
            'response_duration' => ['required', 'integer', 'min:0'],
            'created_by' => ['nullable', 'exists:admins,id'],
            'image_before' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'image_after' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'company_id' => ['required', 'exists:companies,id'],
            'contract_id' => ['required', 'exists:contracts,id'],
        ];
    }
}
