<?php

namespace App\Actions\Notifications;

use App\Models\Result;
use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\WebhookCall;

class SendWebhookTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title('You need to add webhook URLs!')
                ->warning()
                ->send();

            return;
        }

        // Generate a fake Result (NOT saved to database)
        $fakeResult = Result::factory()->make();

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload([
                    'result_id' => fake()->uuid(),
                    'site_name' => config('app.name'),
                    'isp' => $fakeResult->data['isp'],
                    'ping' => $fakeResult->ping,
                    'download' => $fakeResult->download,
                    'upload' => $fakeResult->upload,
                    'packetLoss' => $fakeResult->data['packetLoss'],
                    'speedtest_url' => $fakeResult->data['result']['url'],
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title('Test webhook notification sent.')
            ->success()
            ->send();
    }
}
