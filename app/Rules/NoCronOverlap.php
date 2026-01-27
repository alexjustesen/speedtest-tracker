<?php

namespace App\Rules;

use App\Helpers\Cron;
use App\Models\Schedule;
use Illuminate\Contracts\Validation\Rule;

class NoCronOverlap implements Rule
{
    protected ?int $ignoreId;

    protected ?Schedule $schedule;

    public function __construct(?Schedule $schedule = null, ?int $ignoreId = null, bool $shouldCheck = true)
    {
        $this->schedule = $schedule;
        $this->ignoreId = $ignoreId;
        $this->shouldCheck = $shouldCheck;
    }

    public function passes($attribute, $value): bool
    {
        if (! $this->shouldCheck || ! $this->schedule || ! is_string($value)) {
            return true;
        }

        // Fetch all cron expressions of the same type, excluding the current schedule (if any)
        $existingCrons = Schedule::query()
            ->where('is_active', true)
            ->where('type', $this->schedule->type)  // Dynamically use the type from the schedule
            ->when($this->ignoreId, fn ($q) => $q->where('id', '!=', $this->ignoreId))
            ->get()
            ->pluck('schedule');  // Extract cron expressions

        foreach ($existingCrons as $existingCron) {
            \Log::info('Comparing:', ['existing' => $existingCron, 'new' => $value]);

            // Check if there's an overlap
            if (Cron::hasOverlap($existingCron, $value)) {

                return false;  // Return false if overlap is detected
            }
        }

        return true;  // Return true if no overlap is detected
    }

    public function message(): string
    {
        return 'This cron expression overlaps with another active schedule of the same type.';
    }
}
