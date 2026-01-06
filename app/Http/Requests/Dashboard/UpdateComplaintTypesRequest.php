<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Status;
use App\Models\ComplaintType;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Foundation\Http\FormRequest;


class UpdateComplaintTypesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
                return abilities()->contains('update_complainttypes');

    }

    /**
     * Get the validation     rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
    {
        $complaint = request()->route('complainttype');

// dd($complaint);
        return [
            'name_ar' => [
                'required',
                'string',
                'max:255,' . $complaint->id,
                 new NotNumbersOnly(),
                new ExistButDeleted(new ComplaintType())
            ],

            'color' => [
                'required',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/'
            ],
        ];
    }
}
