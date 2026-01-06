<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NumbersOnly implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // يتحقق إذا القيمة مش أرقام فقط
        if (!preg_match('/^\d+$/', $value)) {
            $fail(__(":attribute must contain numbers only"));
        }
    }

    public function message()
    {
        return __(":attribute") . __(' must be valid');
    }
}
