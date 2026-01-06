<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Validation\Rule;
use App\Rules\CurrentAdminPassword;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileEmailRequest extends FormRequest
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
        $admin = auth()->user();
        return [
            'email' => ['required', 'string', 'email:rfc,dns', Rule::unique('admins')->ignore($admin->id)],
            'confirm_email_password' => ['required', new CurrentAdminPassword],
        ];
    }
}
