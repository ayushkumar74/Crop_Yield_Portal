<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRainfall implements ValidationRule
{
    /**
     * Run the validation rule.
     * Ensures rainfall is within a realistic agricultural range.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value) || $value < 0 || $value > 10000) {
            $fail('The rainfall must be a realistic value between 0 and 10,000 mm/year.');
        }
    }
}
