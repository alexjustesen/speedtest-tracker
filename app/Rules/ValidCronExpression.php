<?php

namespace App\Rules;

use Cron\CronExpression;
use Illuminate\Contracts\Validation\InvokableRule;

class ValidCronExpression implements InvokableRule
{
    /**
     * Validates a string cron expression is correct
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $is_valid = CronExpression::isValidExpression($value);

        if (! $is_valid) {
            $fail('Cron expression is not valid');
        }
    }
}
