<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\Category;



class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
                return abilities()->contains('update_categories');

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
                new ExistButDeleted(new Category())
            ],
            'name_en' => [
                'required',
                'string',
                'max:255',
                new NotNumbersOnly(),
                new ExistButDeleted(new Category())
            ],
            'description_ar' => [
                'required',
                'string',
                'max:255',
                new NotNumbersOnly(),
                new ExistButDeleted(new Category())
            ],
            'description_en' => [
                'required',
                'string',
                'max:255',
                new NotNumbersOnly(),
                new ExistButDeleted(new Category())
            ],

        ];
    }
}
