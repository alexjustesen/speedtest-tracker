<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContainsString implements ValidationRule
{
    public function __construct(
        protected string $needle,
        protected bool $caseSensitive = false
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $haystack = $this->caseSensitive ? $value : strtolower($value);
        $needle = $this->caseSensitive ? $this->needle : strtolower($this->needle);

        if (! str_contains($haystack, $needle)) {
            $fail("The :attribute must contain '{$this->needle}'.");
        }
    }
}
