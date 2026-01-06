<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\Unit;

class StoreUniteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_unites');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255', 'unique:unites', new NotNumbersOnly(), new ExistButDeleted(new Unit())],
            'name_en'    => ['nullable', 'string', 'max:255', 'unique:unites', new NotNumbersOnly(), new ExistButDeleted(new Unit())],
        ];
    }
}
