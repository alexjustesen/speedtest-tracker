<?php

namespace App\Listeners\Database;

use App\Events\SpeedtestCompleted;
use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SendSpeedtestCompletedNotification
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->database_enabled) {
            return;
        }

        if (! $notificationSettings->database_on_speedtest_run) {
            return;
        }

        foreach (User::all() as $user) {
            Log::info('Notifying user', ['id' => $user->id, 'email' => $user->email]);
            Notification::make()
                ->title('Speedtest completed')
                ->success()
                ->sendToDatabase($user);
        }
    }
}
