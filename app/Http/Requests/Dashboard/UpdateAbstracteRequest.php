<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Abstracte;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAbstracteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_abstractes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $abstracte = $this->route('abstracte'); // جلب الـ id من الـ route

        return [
            'title' => [
                'sometimes',          // يعني مش لازم يكون موجود في كل تحديث
                'required',
                'string',
                'max:255',
                'unique:abstractes,title,' . $abstracte->id, // تجاهل العنوان الحالي
                new NotNumbersOnly(),
                new ExistButDeleted(new Abstracte()),
            ],

            'description' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                new NotNumbersOnly(),
                new ExistButDeleted(new Abstracte()),
            ],

            'contract_id' => [
                'sometimes',
                'required',
                'exists:contracts,id',
            ],

            'upload' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:1024',
            ],
            'type' => ['required', 'in:ongoing,final'],

        ];
    }
}
