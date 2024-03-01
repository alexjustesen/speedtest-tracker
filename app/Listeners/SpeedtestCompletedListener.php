<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Settings\GeneralSettings;
use App\Settings\NotificationSettings;
use App\Telegram\TelegramNotification;
use Spatie\WebhookServer\WebhookCall;

class SpeedtestCompletedListener
{
    public $generalSettings;

    public $notificationSettings;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->generalSettings = new (GeneralSettings::class);

        $this->notificationSettings = new (NotificationSettings::class);
    }

    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        if ($this->notificationSettings->telegram_enabled) {
            if ($this->notificationSettings->telegram_on_speedtest_run && count($this->notificationSettings->telegram_recipients)) {
                foreach ($this->notificationSettings->telegram_recipients as $recipient) {
                    $download_value = toBits(convertSize($event->result->download), 2).' (Mbps)';

                    $upload_value = toBits(convertSize($event->result->upload), 2).' (Mbps)';

                    $ping_value = number_format($event->result->ping, 2).' (ms)';

                    $message = view('telegram.speedtest-completed', [
                        'id' => $event->result->id,
                        'site_name' => $this->generalSettings->site_name,
                        'ping' => $ping_value,
                        'download' => $download_value,
                        'upload' => $upload_value,
                    ])->render();

                    \Illuminate\Support\Facades\Notification::route('telegram_chat_id', $recipient['telegram_chat_id'])
                        ->notify(new TelegramNotification($message));
                }
            }
        }

        if ($this->notificationSettings->discord_enabled) {
            if ($this->notificationSettings->discord_on_speedtest_run && count($this->notificationSettings->discord_webhooks)) {
                foreach ($this->notificationSettings->discord_webhooks as $webhook) {
                    // Construct the payload
                    $payload = [
                        'content' => 'There are new speedtest results for your network.'.
                                        "\nResult ID: ".$event->result->id.
                                        "\nSite Name: ".$this->generalSettings->site_name.
                                        "\nPing: ".$event->result->ping.' ms'.
                                        "\nDownload: ".($event->result->downloadBits / 1000000).' (Mbps)'.
                                        "\nUpload: ".($event->result->uploadBits / 1000000).' (Mbps)',
                    ];
                    // Send the payload using WebhookCall
                    WebhookCall::create()
                        ->url($webhook['url'])
                        ->payload($payload)
                        ->doNotSign()
                        ->dispatch();

                }
            }
        }
    }
}
