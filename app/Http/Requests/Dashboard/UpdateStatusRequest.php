<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Status;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return abilities()->contains('update_status');
    }

    public function rules(): array
    {
        $status = $this->route('status');


        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                new NotNumbersOnly(),
                Rule::unique('status', 'name_ar')->ignore($status->id),
                new ExistButDeleted(new Status())
            ],
            'name_en' => [
                'required',
                'string',
                'max:255',
                new NotNumbersOnly(),
                Rule::unique('status', 'name_en')->ignore($status->id),

                new ExistButDeleted(new Status())
            ],
            'color' => [
                'required',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/'
            ],
        ];
    }
}
