<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Quality;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQualityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
              return abilities()->contains('update_qualities');

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //
        $quality = $this->route('quality'); // جلب الـ id من الـ route


         return [
            'title' => [
                'sometimes',          // يعني مش لازم يكون موجود في كل تحديث
                'required',
                'string',
                'max:255',
                'unique:qualities,title,' . $quality->id, // تجاهل العنوان الحالي
                new NotNumbersOnly(),
                new ExistButDeleted(new Quality()),
            ],

            'description' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                new NotNumbersOnly(),
                new ExistButDeleted(new Quality()),
            ],



            'upload' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:1024',
            ],
        ];
    }
}
