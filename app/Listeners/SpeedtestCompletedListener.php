<?php

namespace App\Listeners;

use App\Events\ResultCreated;
use App\Mail\SpeedtestCompletedMail;
use App\Settings\GeneralSettings;
use App\Settings\NotificationSettings;
use App\Telegram\TelegramNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
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
    public function handle(ResultCreated $event): void
    {
        if ($this->notificationSettings->database_enabled) {
            if ($this->notificationSettings->database_on_speedtest_run) {
                Notification::make()
                    ->title('Speedtest completed')
                    ->success()
                    ->sendToDatabase($event->user);
            }
        }

        if ($this->notificationSettings->mail_enabled) {
            if ($this->notificationSettings->mail_on_speedtest_run && count($this->notificationSettings->mail_recipients)) {
                foreach ($this->notificationSettings->mail_recipients as $recipient) {
                    Mail::to($recipient)
                        ->send(new SpeedtestCompletedMail($event->result));
                }
            }
        }

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

        if ($this->notificationSettings->webhook_enabled) {
            if ($this->notificationSettings->webhook_on_speedtest_run && count($this->notificationSettings->webhook_urls)) {
                foreach ($this->notificationSettings->webhook_urls as $url) {
                    WebhookCall::create()
                        ->url($url['url'])
                        ->payload([
                            'result_id' => $event->result->id,
                            'site_name' => $this->generalSettings->site_name,
                            'ping' => $event->result->ping,
                            'download' => $event->result->downloadBits,
                            'upload' => $event->result->uploadBits,
                        ])
                        ->doNotSign()
                        ->dispatch();
                }
            }
        }
    }
}
