<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NotNumbersOnly;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Rules\CurrentAdminPassword;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePasswordRequest extends FormRequest
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
            'current_password' => ['required', new CurrentAdminPassword, Rule::excludeIf(!is_null($this->current_password))],
            'password' => ['required_with:current_password',  Password::min(8)->max(16)->letters()->numbers()],
            'password_confirmation' => ['required_with:password', 'same:password', Rule::excludeIf(!is_null($this->password_confirmation))],
        ];
    }
}