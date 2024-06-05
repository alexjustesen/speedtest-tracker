<?php

namespace App\Console\Commands;

use App\Helpers\TimeZoneHelper;
use App\Settings\GeneralSettings;
use Cron\CronExpression;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class UpdateGeneralSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-general-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application\'s general settings.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $settings = new GeneralSettings();

        $this->updateTimeZone($settings);
        $this->updateSchedule($settings);
        $this->resetSevers($settings);

        $this->line('✅  Settings updated!');
    }

    protected function resetSevers($settings): void
    {
        $confirmed = confirm(
            label: 'Do you want to reset the server list?',
            default: false,
            yes: 'Yes, reset it',
            no: 'No, keep it'
        );

        if ($confirmed) {
            $settings->speedtest_server = [];

            $settings->save();
        }
    }

    protected function updateSchedule($settings): void
    {
        $cron = text(
            label: 'What is the schedule?',
            placeholder: '0 * * * *',
            default: $settings->speedtest_schedule ?? '0 * * * *',
            required: true,
            validate: fn (string $value) => match (true) {
                ! CronExpression::isValidExpression($value) => 'The schedule expression is invalid.',
                default => null
            }
        );

        if ($cron) {
            $settings->speedtest_schedule = $cron;

            $settings->save();
        }
    }

    protected function updateTimeZone($settings): void
    {
        $timezone = text(
            label: 'What is the time zone?',
            placeholder: 'UTC',
            default: $settings->timezone,
            required: true,
            validate: fn (string $value) => match (true) {
                ! TimeZoneHelper::validate($value) => 'The time zone must be a valid time zone.',
                default => null
            }
        );

        if ($timezone) {
            $settings->timezone = $timezone;

            $settings->save();
        }
    }
}
