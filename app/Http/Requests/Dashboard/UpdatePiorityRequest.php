<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Priority;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\NumbersOnly;
use App\Rules\ExistButDeleted;

class UpdatePiorityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_priorities');
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
                new NotNumbersOnly(),
                new ExistButDeleted(new Priority())
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:255',
                new NotNumbersOnly(),
                new ExistButDeleted(new Priority())
            ],
            'color' => [
                'required',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/'
            ],
            'dayPertirty' => [
                'required',
                'integer',
                'max:255',
                new NumbersOnly(),
                new ExistButDeleted(new Priority())
            ],
        ];
    }
}
