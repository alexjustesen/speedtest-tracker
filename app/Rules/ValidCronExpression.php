<?php

namespace App\Rules;

use Closure;
use Cron\CronExpression;
use Illuminate\Contracts\Validation\InvokableRule;

class ValidCronExpression implements InvokableRule
{
    /**
     * Validates a string cron expression is correct
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function __invoke(string $attribute, mixed $value, Closure $fail): void
    {
        $is_valid = CronExpression::isValidExpression($value);

        if (! $is_valid) {
            $fail('Cron expression is not valid');
        }
    }
}
