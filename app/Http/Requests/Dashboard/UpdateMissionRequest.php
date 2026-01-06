<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_missions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // نجيب الـ mission id من الـ route
        $missionId = $this->route('mission')->id ?? null;

        return [
            'complaint_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('missions', 'complaint_number')->ignore($missionId),
            ],
            'customer_name'    => 'required|string|max:150',
            'customer_phone'   => 'required|string|max:20',
            'address'          => 'nullable|string|max:255',
            'street'           => 'nullable|string|max:150',
            'plot_number'      => 'nullable|string|max:50',
            'warning_icon'     => 'nullable|string|max:50',
            'qrcode'           => 'nullable|string',
            'is_completed'     => 'sometimes|boolean',
            'completed_at'     => 'nullable|date',
            'location_lat'     => 'nullable|numeric|between:-90,90',
            'location_long'    => 'nullable|numeric|between:-180,180',

            // foreign keys
            'license_type_id'  => 'nullable|exists:license_type,id',
            'municipality_id'  => 'nullable|exists:municipalities,id',
            'priority_id'      => 'nullable|exists:priorities,id',
            'neighborhood_id'  => 'nullable|exists:neighborhood,id',
            'category_id'      => 'nullable|exists:categories,id',
            'statue_id'        => 'nullable|exists:status,id',
            'sectors_id' => 'nullable|exists:sectors,id',
            'feeder' => 'required|integer|min:1',
            'station_id'        => 'nullable',
            'light_columns_id'        => 'nullable',



            // images
            // 'images'           => 'nullable|array',
            // 'images.*'         => 'image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
        ];
    }
}
