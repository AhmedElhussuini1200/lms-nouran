<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistPhone implements ValidationRule
{
    private $model;
    private $adminId;
    private $includeTrashed;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Model $model, $adminId = null, $includeTrashed = true)
    {
        $this->model   = $model;
        $this->adminId = $adminId;
        $this->includeTrashed = $includeTrashed;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate($attribute, mixed $value, Closure $fail): void
    {
        $normalizedValue = Str::startsWith($value, '0') ? ltrim($value, '0') : '0' . $value;
        $query = $this->model
            ->where(function ($query) use ($normalizedValue, $value, $attribute) {
                $query->where($attribute, 'LIKE', "%" . $normalizedValue . "%")
                    ->orWhere($attribute, 'LIKE', "%" . $value . "%");
            });

        // Exclude the current admin's ID if provided
        if ($this->adminId) {
            $query->where('id', '!=', $this->adminId);
        }
        if ($this->includeTrashed) {
            $query->withTrashed();
        }
        $existsNormalized = $query->exists();
        if ($existsNormalized) {
            $fail(__("The") . " " . __(":attribute") . ' ' . __('has already been taken'));
        }
    }
}
