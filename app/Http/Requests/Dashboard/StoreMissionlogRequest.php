<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreMissionlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [

               'mission_id'   => 'required|exists:missions,id',
                         'to_user_id' => 'nullable|exists:admins,id',
            'company_id' => [
                'required',
                'exists:companies,id'],
               'notes'        => 'required|string',
               'reject_reason' => 'nullable|string|max:500',
           ];
    }
}
