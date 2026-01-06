<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\Field;



class UpdateFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_fields');

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $field = request()->route('field');

        return [
            'name_ar'       => [ 'required','string','max:255','unique:fields,name_ar,' . $field->id ,new NotNumbersOnly(),new ExistButDeleted(new Field())],
            'name_en'       => [ 'required','string','max:255','unique:fields,name_en,' . $field->id,new NotNumbersOnly(),new ExistButDeleted(new Field()) ],
            'is_critical'  => ['sometimes','boolean'],
            'image'        =>['nullable', 'image', 'mimes:jpeg,jpg,png,svg', 'max:2048'],
        ];
    }
}