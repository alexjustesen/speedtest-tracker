<?php

namespace App\Jobs\Notifications\Database;

use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSpeedtestCompletedNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Handle the job.
     */
    public function handle(): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->database_enabled) {

            return;
        }

        if (! $notificationSettings->database_on_speedtest_run) {

            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Speedtest completed')
                ->success()
                ->sendToDatabase($user);
        }
    }
}
