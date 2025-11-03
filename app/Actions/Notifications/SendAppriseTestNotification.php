<?php

namespace App\Actions\Notifications;

use App\Notifications\Apprise\TestNotification;
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

        foreach ($channel_urls as $row) {
            $channelUrl = $row['channel_url'] ?? null;
            if (! $channelUrl) {
                Notification::make()
                    ->title('Skipping missing channel URL!')
                    ->warning()
                    ->send();

                continue;
            }

            FacadesNotification::route('apprise_urls', $channelUrl)
                ->notify(new TestNotification);
        }

        Notification::make()
            ->title('Test Apprise notification sent.')
            ->success()
            ->send();
    }
}
