<?php

namespace App\Actions\Schedules;

use Cron\CronExpression;
use Illuminate\Support\HtmlString;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\CronTranslator\CronTranslator;

class ExplainCronExpression
{
    use AsAction;

    public function handle(?string $expression)
    {
        if (blank($expression)) {
            return 'No cron expression provided.';
        }

        try {
            new CronExpression($expression);
        } catch (\InvalidArgumentException $e) {
            return new HtmlString('The cron expression is invalid.');
        }

        try {
            return CronTranslator::translate($expression);
        } catch (\Exception $e) {
            return new HtmlString('The cron expression is not supported.');
        }
    }
}
