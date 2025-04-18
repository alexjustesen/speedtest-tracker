<?php

namespace App\Rules;

use App\Helpers\Cron;
use App\Models\Schedule;
use Illuminate\Contracts\Validation\Rule;

class NoCronOverlap implements Rule
{
    protected ?int $ignoreId;
    protected ?string $service;

    public function __construct(?string $service = null, ?int $ignoreId = null)
    {
        $this->service = $service;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value): bool
    {
        if (! $this->service || ! is_string($value)) {
            return true; // skip validation if missing context
        }

        $existingCrons = Schedule::query()
        ->where('is_active', true)
        ->where('service', $this->service)
        ->when($this->ignoreId, fn ($q) => $q->where('id', '!=', $this->ignoreId))
        ->get()
        ->pluck('options') // Get the whole options array
        ->pluck('cron_expression'); // Extract cron_expression from the options array

        foreach ($existingCrons as $existingCron) {
            if (Cron::hasOverlap($existingCron, $value)) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return 'This cron expression overlaps with another active schedule of the same Service.';
    }
}
