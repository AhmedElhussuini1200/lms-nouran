<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Sector;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotNumbersOnly;
use App\Rules\ExistButDeleted;
use Illuminate\Validation\Rule;

class UpdateSectorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return abilities()->contains('update_sectors');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $sector = request()->route('sector');
        // dd($sector);

        return [
    'name_ar' => [
          'required',
          'string',
          'max:255',
          Rule::unique('sectors', 'name_ar')
              ->ignore($sector->id)
              ->where(fn ($q) => $q->where('neighborhood_id', $this->neighborhood_id)),
          new NotNumbersOnly(),
          new ExistButDeleted(new Sector(), 'name_ar', 'neighborhood_id'),
    ],

    'name_en' => [
          'required',
          'string',
          'max:255',
          Rule::unique('sectors', 'name_en')
              ->ignore($sector->id)
              ->where(fn ($q) => $q->where('neighborhood_id', $this->neighborhood_id)),
          new NotNumbersOnly(),
          new ExistButDeleted(new Sector(), 'name_en', 'neighborhood_id'),
    ],

    'neighborhood_id' => 'required|exists:neighborhood,id',
];
    }
}
