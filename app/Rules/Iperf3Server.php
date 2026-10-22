<?php

namespace App\Rules;

use App\Services\Speedtests\Iperf3Speedtest;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use RuntimeException;

class Iperf3Server implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            Iperf3Speedtest::parseTarget($value);
        } catch (RuntimeException $e) {
            $fail($e->getMessage());
        }
    }
}
