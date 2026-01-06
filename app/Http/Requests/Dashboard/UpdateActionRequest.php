<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Action;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;

class UpdateActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_actions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
public function rules(): array
    {
        $action = request()->route('action');

        return [
            'name_ar'    => [ 'required','string','max:255','unique:actions,name_ar,' . $action->id ,new NotNumbersOnly(), new ExistButDeleted(new Action())],
            'name_en'    => [ 'required','string','max:255','unique:actions,name_en,' . $action->id,new NotNumbersOnly(), new ExistButDeleted(new Action())],
            'description_ar'    => ['nullable', 'string', 'max:255','unique:actions,description_ar,'.$action->id, new NotNumbersOnly(), new ExistButDeleted(new Action())],
            'description_en'    => ['nullable', 'string', 'max:255','unique:actions,description_en,'.$action->id, new NotNumbersOnly(), new ExistButDeleted(new Action())],
        ];
    }
}
