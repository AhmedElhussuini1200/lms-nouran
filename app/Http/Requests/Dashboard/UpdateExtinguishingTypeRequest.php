<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use App\Models\ExtinguishingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExtinguishingTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return abilities()->contains('update_extinguishingtypes');
    }

    public function rules(): array
    {
        // 👇 لازم نفس اسم الباراميتر في route
        $extinguish = $this->route('extinguish');
// dd($extinguish);
        return [
            // 'name_ar' => [
            //     'required',
            //     'string',
            //     'max:255',
            //     Rule::unique('extinguishing_types', 'name_ar')
            //         ->ignore($extinguish?->id),

            //     new NotNumbersOnly(),
            //     new ExistButDeleted(new ExtinguishingType(), $extinguish?->id),
            // ],
            'name_ar'    => [ 'required','string','max:255','unique:extinguishing_types,name_ar,' . $extinguish->id ,new NotNumbersOnly(), new ExistButDeleted(new ExtinguishingType())],
            'complaint_type_id' => [
                'required',
                'exists:complaint_types,id'
            ],
            'color' => [
                'required',
                'string',
                'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'
            ],
        ];
    }
}
