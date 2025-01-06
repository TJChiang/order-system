<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneRule implements ValidationRule, Validable
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->isValid($value)) {
            $fail($this->message($attribute, $value));
        }
    }

    public function isValid(mixed $value): bool
    {
        return is_string($value)
            && (
                preg_match('/^09\d{8}$/', $value) > 0
                || preg_match('/^\+\d{4,18}$/', $value) > 0
            );
    }

    public function message(string $attribute, mixed $value): string
    {
        return "$attribute is invalid.";
    }
}
