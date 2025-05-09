<?php

namespace App\Listeners\Speedtest;

use App\Events\SpeedtestCompleted;
use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Spatie\LaravelSettings\Settings;

class SendBenchmarkNotifications
{
    public Settings $settings;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->settings = new NotificationSettings;
    }

    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        //
    }

    /**
     * Handle database notifications.
     *
     * * This is done here since Filament database notifications are a different format than the default.
     */
    public function handleDatabaseNotifications(SpeedtestCompleted $event): void
    {
        if (! $this->settings->database_enabled || ! $this->settings->database_on_threshold_failure) {
            return;
        }

        if ($event->result->healthy) {
            return;
        }

        if (Arr::has($event->result->benchmarks, 'ping') && ! Arr::boolean($event->result->benchmarks, 'ping.passed')) {
            $message = number_format($event->result->ping, 0)
                .Arr::string($event->result->benchmarks, 'ping.unit')
                .' exceeded the '
                .Arr::string($event->result->benchmarks, 'ping.bar')
                .' value of '
                .Arr::integer($event->result->benchmarks, 'ping.value')
                .Arr::string($event->result->benchmarks, 'ping.unit').'.';

            User::all()->each(function (User $user) use ($event, $message) {
                Notification::make()
                    ->title('Speedtest #'.$event->result->id.' - Ping benchmark failed')
                    ->body($message)
                    ->actions([
                        Action::make('view')
                            ->url(route('filament.admin.resources.results.index')),
                    ])
                    ->warning()
                    ->sendToDatabase($user);
            });
        }
    }
}
