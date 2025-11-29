<?php

namespace App\Rules;

use App\Helpers\FileSize;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileSizeString implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! FileSize::isValid($value)) {
            $fail('The :attribute must be a valid file size (e.g., 100 MB, 1GB, 2TB).');
        }
    }
}
