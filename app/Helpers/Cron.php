use Cron\CronExpression;
use DateTime;

<?php

namespace App\Helpers;

use Cron\CronExpression;

class Cron
{
    /**
     * Check if two cron expressions overlap within a given time range
     *
     * @param string $cron1 First cron expression
     * @param string $cron2 Second cron expression
     * @param int $checkHours Number of hours to check for overlap (default: 24)
     * @return bool Returns true if schedules overlap, false otherwise
     */
    public static function hasOverlap(string $cron1, string $cron2, int $checkHours = 24): bool
    {
        $cron1Expression = new CronExpression($cron1);
        $cron2Expression = new CronExpression($cron2);

        $startTime = now();
        $endTime = now()->addHours($checkHours);

        $schedule1 = [];
        $schedule2 = [];

        $current = clone $startTime;

        // Get all run times for both cron expressions
        while ($current <= $endTime) {
            if ($cron1Expression->getNextRunDate($current, 0) <= $endTime) {
                $schedule1[] = $cron1Expression->getNextRunDate($current)->format('Y-m-d H:i');
            }

            if ($cron2Expression->getNextRunDate($current, 0) <= $endTime) {
                $schedule2[] = $cron2Expression->getNextRunDate($current)->format('Y-m-d H:i');
            }

            $current->addMinute();
        }

        // Check for any common execution times
        return ! empty(array_intersect($schedule1, $schedule2));
    }
}