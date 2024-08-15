<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DenomQty implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if at least one array contains a 'qty' with a minimum value of 1
        $valid = false;
        foreach ($value as $item) {
            if ((isset($item['qty']) && $item['qty'] >= 1) && (isset($item['denomination']) && $item['denomination'] >= 1)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            $fail('The :attribute must contain at least one array with a qty of at least 1.');
        }
    }
}
