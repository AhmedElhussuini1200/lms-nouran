<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Action;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;

class StoreActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('create_actions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255','unique:actions,name_ar', new NotNumbersOnly(), new ExistButDeleted(new Action())],
            'name_en'    => ['nullable', 'string', 'max:255','unique:actions,name_en', new NotNumbersOnly(), new ExistButDeleted(new Action())],
            'description_ar'    => ['nullable', 'string', 'max:255','unique:actions,description_ar', new NotNumbersOnly(), new ExistButDeleted(new Action())],
            'description_en'    => ['nullable', 'string', 'max:255','unique:actions,description_en', new NotNumbersOnly(), new ExistButDeleted(new Action())],
        ];
    }
}
