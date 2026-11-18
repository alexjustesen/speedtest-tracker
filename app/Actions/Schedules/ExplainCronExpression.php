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
            return __('schedules.schedule_empty');
        }

        if (! CronExpression::isValidExpression($expression)) {
            return new HtmlString(__('schedules.schedule_invalid'));
        }

        try {
            return CronTranslator::translate($expression);
        } catch (\Exception $e) {
            return new HtmlString(__('schedules.schedule_unsupported'));
        }
    }
}
