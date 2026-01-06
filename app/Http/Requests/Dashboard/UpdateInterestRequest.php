<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NotNumbersOnly;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInterestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_interests');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $interest = request()->route('interest');
        return [
            'name_ar'    => ['required', 'string', 'max:255', 'unique:cities,name_ar,' . $interest->id, new NotNumbersOnly()],
            'name_en'    => ['nullable', 'string', 'max:255', 'unique:cities,name_en,' . $interest->id, new NotNumbersOnly()],
        ];
    }
}
