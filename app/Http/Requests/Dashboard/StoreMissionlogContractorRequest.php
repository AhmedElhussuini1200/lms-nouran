<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;

class StoreMissionlogContractorRequest extends FormRequest
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
                'to_user_id' => 'required_unless:new_assign,action|exists:admins,id',

           'result' => 'required_without:notes|string',
'notes'   => 'required_without:result|string',

               'reject_reason' => 'nullable|string|max:500',
                // 'causes' => [
                //     'required_without:required_action_id',
                //     'string',
                // ],
'causes' => [
    'nullable',
    'string',
],
                'company_id' => [
                                'nullable',
                                'exists:companies,id',
                                function ($attribute, $value, $fail) {
                                    $toUserId = $this->input('to_user_id');
                                    if ($toUserId) {
                                        $user = Admin::find($toUserId);
                                        if ($user && $user->type !== 'Contractor' && empty($value)) {
                                            $fail('The ' . $attribute . ' field is required for non-contractor employees.');
                                        }
                                    }
                                },
                ],
            'new_assign' => 'required|string',
                'execution_action_id' => 'required_unless:new_assign,new|exists:actions,id',
                'required_action_id' => 'required_unless:new_assign,new|exists:actions,id',

                'action_id' => 'required_unless:new_assign,new|exists:actions,id',
                // 'is_completed' => 'required_unless:new_assign,new|boolean',

                'images' => 'required_unless:new_assign,new|array|max:3',
               'images.*.image'       => 'required_unless:new_assign,new|image|mimes:jpg,jpeg,png,webp|max:2048',
               'images.*.name'       => 'required_unless:new_assign,new|string|max:255',
           ];
    }
}
