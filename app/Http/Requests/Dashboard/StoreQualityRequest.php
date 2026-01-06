<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Quality;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class StoreQualityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
           return abilities()->contains('create_qualities');

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:qualities,title',
                new NotNumbersOnly(),
                new ExistButDeleted(new Quality()),
            ],

             'description' => [
                'required',
                'string',
                'max:255',
                new NotNumbersOnly(),
                new ExistButDeleted(new Quality()),
            ],
            'upload' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:1024'],

        ];
    }
}
