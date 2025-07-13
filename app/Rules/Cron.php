<?php

namespace App\Rules;

use Illuminate\Translation\PotentiallyTranslatedString;
use Closure;
use Cron\CronExpression;
use Illuminate\Contracts\Validation\ValidationRule;

class Cron implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string):PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! CronExpression::isValidExpression($value)) {
            $fail('Cron expression is not valid');
        }
    }
}
