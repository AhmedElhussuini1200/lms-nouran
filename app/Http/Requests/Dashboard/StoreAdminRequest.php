<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() && auth('admin')->user()->type === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:admins,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->max(32)],
            'type' => ['required', Rule::in(['admin', 'teacher', 'student', 'parent'])],
            'grade' => ['required_if:type,student', 'nullable', Rule::in(['1_secondary', '2_secondary', '3_secondary'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'brand_primary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'brand_secondary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'whatsapp_key' => ['nullable', 'string', 'max:255'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ];
    }
}
