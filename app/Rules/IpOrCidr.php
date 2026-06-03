<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class IpOrCidr implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail(__('errors.ip_or_cidr_invalid'));

            return;
        }

        foreach ($value as $entry) {
            if (! $this->isValidIpOrCidr($entry)) {
                $fail(__('errors.ip_or_cidr_invalid'));

                return;
            }
        }
    }

    private function isValidIpOrCidr(string $entry): bool
    {
        if (filter_var($entry, FILTER_VALIDATE_IP)) {
            return true;
        }

        if (str_contains($entry, '/')) {
            [$ip, $prefix] = explode('/', $entry, 2);

            if (! filter_var($ip, FILTER_VALIDATE_IP)) {
                return false;
            }

            $maxPrefix = str_contains($ip, ':') ? 128 : 32;

            return is_numeric($prefix)
                && (int) $prefix >= 0
                && (int) $prefix <= $maxPrefix;
        }

        return false;
    }
}
