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
    protected $description = 'CLI to update the general settings.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = new GeneralSettings();

        $this->updateSiteName($settings);
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
            default: $settings->speedtest_schedule,
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

    protected function updateSiteName($settings): void
    {
        $name = text(
            label: 'What is the site name?',
            placeholder: 'Speedtest Tracker',
            default: $settings->site_name,
            required: true,
            validate: fn (string $value) => match (true) {
                strlen($value) < 2 => 'The site name must be at least 2 characters.',
                strlen($value) > 50 => 'The site name must not exceed 50 characters.',
                default => null
            }
        );

        if ($name) {
            $settings->site_name = $name;

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
