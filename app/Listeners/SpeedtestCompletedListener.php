<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Settings\GeneralSettings;
use App\Settings\NotificationSettings;
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
