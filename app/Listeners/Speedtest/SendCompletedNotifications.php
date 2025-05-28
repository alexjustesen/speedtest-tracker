<?php

namespace App\Listeners\Speedtest;

use App\Events\SpeedtestCompleted;
use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Spatie\LaravelSettings\Settings;

class SendCompletedNotifications
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
        if (! $this->settings->database_enabled || ! $this->settings->database_on_speedtest_run) {
            return;
        }

        User::all()->each(function (User $user) use ($event) {
            Notification::make()
                ->title('Speedtest #'.$event->result->id.' - Completed')
                ->actions([
                    Action::make('view')
                        ->url(route('filament.admin.resources.results.index')),
                ])
                ->success()
                ->sendToDatabase($user);
        });
    }
}
