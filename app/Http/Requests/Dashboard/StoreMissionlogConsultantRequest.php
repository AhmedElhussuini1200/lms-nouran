<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreMissionlogConsultantRequest extends FormRequest
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
            'mission_id' => 'required|exists:missions,id',
            'to_user_id' => 'required|exists:admins,id',
            'notes' => 'required|string',
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
                        $user = \App\Models\Admin::find($toUserId);
                        if ($user && $user->type !== 'consultant' && empty($value)) {
                            $fail('The ' . $attribute . ' field is required for non-consultant employees.');
                        }
                    }
                },
            ],

            'required_action_id' => [
                'nullable',
                'exists:actions,id',
                function ($attribute, $value, $fail) {
                    $toUserId = $this->input('to_user_id');
                    if ($toUserId) {
                        $user = \App\Models\Admin::find($toUserId);
                        if ($user && $user->type !== 'consultant' && empty($value)) {
                            $fail('The ' . $attribute . ' field is required for non-consultant employees.');
                        }
                    }
                },
        ],

            'action_id' => [
                'nullable',
                'exists:actions,id',
                function ($attribute, $value, $fail) {
                    $toUserId = $this->input('to_user_id');
                    if ($toUserId) {
                        $user = \App\Models\Admin::find($toUserId);
                        if ($user && $user->type !== 'consultant' && empty($value)) {
                            $fail('The ' . $attribute . ' field is required for non-consultant employees.');
                        }
                    }
                },
        ],

            // 'execution_action_id'  => 'nullable|exists:actions,id',

            // 🔹 Image validations
            'images' => 'nullable|array|max:3',
            'images.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images.*.name' => 'nullable|string|max:255',
        ];
    }

}
