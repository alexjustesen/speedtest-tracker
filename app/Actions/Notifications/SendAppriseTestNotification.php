<?php

namespace App\Actions\Notifications;

use App\Notifications\Apprise\TestNotification;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Lorisleiva\Actions\Concerns\AsAction;

class SendAppriseTestNotification
{
    use AsAction;

    public function handle(array $channel_urls)
    {
        if (! count($channel_urls)) {
            Notification::make()
                ->title('You need to add Apprise channel URLs!')
                ->warning()
                ->send();

            return;
        }

        $settings = app(NotificationSettings::class);
        $appriseUrl = rtrim($settings->apprise_server_url ?? '', '/');

        if (empty($appriseUrl)) {
            Notification::make()
                ->title('Apprise Server URL is not configured')
                ->body('Please configure the Apprise Server URL in the settings above.')
                ->danger()
                ->send();

            return;
        }

        try {
            foreach ($channel_urls as $row) {
                $channelUrl = $row['channel_url'] ?? null;
                if (! $channelUrl) {
                    Notification::make()
                        ->title('Skipping missing channel URL!')
                        ->warning()
                        ->send();

                    continue;
                }

                // Use sendNow() to send synchronously even though notification implements ShouldQueue
                // This allows us to catch exceptions and show them in the UI immediately
                FacadesNotification::route('apprise_urls', $channelUrl)
                    ->notifyNow(new TestNotification);
            }

            Notification::make()
                ->title('Test Apprise notification sent.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Failed to send Apprise test notification')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
