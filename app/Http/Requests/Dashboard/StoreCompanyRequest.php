<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Company;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // هنا غيّر الـ ability لو عندك صلاحية خاصة بالشركات
        return abilities()->contains('create_companies');
    }

    public function rules(): array
    {
        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                'unique:companies,name_ar',
                new NotNumbersOnly(),
                new ExistButDeleted(new Company()),
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:255',
                'unique:companies,name_en',
                new NotNumbersOnly(),
                new ExistButDeleted(new Company()),
            ],
            'address' => ['required', 'string', 'max:255'],
            'tax_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:companies,email'],
            'city_id' => ['required', 'exists:cities,id'],
            'stamp' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg,webp,bmp,tiff,tif,heic,heif', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg,webp,bmp,tiff,tif,heic,heif', 'max:2048'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/', new PhoneNumber()],


            'municipality_id'  => 'nullable|exists:municipalities,id',
            'district_id' => 'required|exists:districts,id',
            'sector_id' => 'required|exists:sectors,id',
            'neighborhood_id'  => 'required|exists:neighborhood,id',
            'type' => ['required', Rule::in(['contractor', 'consultant'])],


        ];
    }
}
