<?php

namespace App\Http\Requests\Dashboard;


use App\Rules\NumbersOnly;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLightColumnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_lightColumns');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $lightColumns = request()->route('LightColumn');

        return [
            'column_number' => 'required|string|max:255|unique:light_columns,column_number,' . $lightColumns,
            'station_id'    => 'required|exists:stations,id',
            'cable_number'  => ['required', 'string'],
            'plate_number'  => ['required', 'string'],
            'lights_count'  => ['required', 'numeric', 'min:1', new NumbersOnly()],
            'feeder'        => ['required', 'numeric', 'min:1', new NumbersOnly()],
        ];
    }
}
