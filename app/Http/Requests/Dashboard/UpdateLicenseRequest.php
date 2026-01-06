<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\License;


class UpdateLicenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_license_types');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $license_type = request()->route('license_type');
        // dd($license_type);

         return [
            'name_ar'    => [ 'required','string','max:255','unique:license_type,name_ar,' . $license_type->id ,new NotNumbersOnly(), new ExistButDeleted(new License())],
            'name_en'    => [ 'required','string','max:255','unique:license_type,name_en,' . $license_type->id,new NotNumbersOnly(), new ExistButDeleted(new License())],
        ];
    }
}
