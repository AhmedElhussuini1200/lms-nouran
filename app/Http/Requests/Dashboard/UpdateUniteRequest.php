<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\Unit;

class UpdateUniteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize(): bool
    {
        return abilities()->contains('update_unites');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
                $unite = request()->route('unite');

         return [
            'name_ar'    => [ 'required','string','max:255','unique:unites,name_ar,' . $unite->id ,new NotNumbersOnly(), new ExistButDeleted(new Unit())],
            'name_en'    => [ 'required','string','max:255','unique:unites,name_en,' . $unite->id,new NotNumbersOnly(), new ExistButDeleted(new Unit())],
        ];
    }
}
