<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Company;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // صلاحية تحديث الشركات
        return abilities()->contains('update_companies');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $company = $this->route('company');

        if (is_numeric($company)) {
            $company = Company::findOrFail($company);
        }

        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                'unique:companies,name_ar,' . $company->id,
                new NotNumbersOnly(),
                new ExistButDeleted(new Company()),
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:255',
                'unique:companies,name_en,' . $company->id,
                new NotNumbersOnly(),
                new ExistButDeleted(new Company()),
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
            ],
            'tax_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:companies,email,' . $company->id,
            ],
            'city_id' => [
                'required',
                'exists:cities,id',
            ],
            'stamp' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,svg,webp,bmp,tiff,tif,heic,heif',
                'max:2048',
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,svg,webp,bmp,tiff,tif,heic,heif',
                'max:2048',
            ],

            'municipality_id'  => 'nullable|exists:municipalities,id',
            'neighborhood_id'  => 'nullable|exists:neighborhood,id',
            'category_id'      => 'nullable|exists:categories,id',
            'sector_id'      => 'nullable|exists:sectors,id',
            'type' => ['required', Rule::in(['contractor', 'consultant'])],

        ];
    }
}
