<?php

namespace App\Services;

use App\Enums\ResultService;
use App\Settings\SpeedtestSettings;
use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Support\Collection;

class ScheduledSpeedtestService
{
    /**
     * Assess if there are scheduled speedtests and return the next scheduled time.
     *
     * @return Carbon|null Returns null if no tests are scheduled, or Carbon instance with next scheduled test
     */
    public static function getNextScheduledTest(): ?Carbon
    {
        return self::schedules()
            ->map(fn (array $schedule): Carbon => Carbon::parse(
                time: (new CronExpression($schedule['cron']))->getNextRunDate(timeZone: config('app.display_timezone'))
            ))
            ->sort()
            ->first();
    }

    /**
     * The speedtests to run on a schedule: the one from the SPEEDTEST_SCHEDULE environment variable
     * and the enabled ones from the speedtest settings.
     *
     * @return Collection<int, array{service: ResultService, cron: string, target: ?string}>
     */
    public static function schedules(): Collection
    {
        $schedules = collect();

        if ($schedule = self::environmentSchedule()) {
            $schedules->push([
                'service' => ResultService::tryFrom((string) config('speedtest.service')) ?? ResultService::Ookla,
                'cron' => $schedule,
                'target' => null,
            ]);
        }

        foreach (app(SpeedtestSettings::class)->schedules as $schedule) {
            $service = ResultService::tryFrom((string) ($schedule['service'] ?? ''));
            $cron = trim((string) ($schedule['cron'] ?? ''));

            if (! ($schedule['enabled'] ?? true) || ! $service || ! CronExpression::isValidExpression($cron)) {
                continue;
            }

            $schedules->push([
                'service' => $service,
                'cron' => $cron,
                'target' => filled($schedule['target'] ?? null) ? trim($schedule['target']) : null,
            ]);
        }

        return $schedules;
    }

    /**
     * The cron expression of the SPEEDTEST_SCHEDULE environment variable, if one is set.
     */
    public static function environmentSchedule(): ?string
    {
        $schedule = config('speedtest.schedule');

        return blank($schedule) || $schedule === false || $schedule === 'false' ? null : (string) $schedule;
    }
}
