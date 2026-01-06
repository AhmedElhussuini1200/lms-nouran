<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateMaxImages implements ValidationRule
{
    private $product = 0;
    private $deletedProducts = 0;

    public function __construct(Product $product, $deletedProducts = null)
    {
        $this->product = $product;
        $this->deletedProducts = $deletedProducts;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $conditionOfStore = count(request()->images) + $this->product->images()->count();
        if($this->deletedProducts != 0)
        {
            $conditionOfUpdate = $this->product->images()->count() - count($this->deletedProducts) + count(request()->images);
        }

        if(isset($conditionOfUpdate))
        {
            if( $conditionOfUpdate > 5 )
            {
                $fail(__('Images exceeds the maximum number'));
            }
        }else{
            if( $conditionOfStore > 5 )
            {
                $fail(__('Images exceeds the maximum number'));
            }
        }
    }
}
