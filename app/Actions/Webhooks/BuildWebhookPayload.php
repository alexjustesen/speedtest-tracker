<?php

namespace App\Actions\Webhooks;

use App\Enums\NotificationEvent;
use App\Helpers\Number;
use App\Models\Result;
use Lorisleiva\Actions\Concerns\AsAction;

class BuildWebhookPayload
{
    use AsAction;

    /**
     * Build the webhook payload for a result and event.
     *
     * @return array<string, mixed>
     */
    public function handle(Result $result, NotificationEvent $event): array
    {
        $payload = [
            'event' => $event->value,
            'result_id' => $result->id,
            'site_name' => config('app.name'),
            'server_name' => $result->server_name,
            'server_id' => $result->server_id,
            'status' => $result->status,
            'isp' => $result->isp,
            'ping' => $result->ping ? round($result->ping) : null,
            'download' => $result->download_bits !== null
                ? Number::bitsToMagnitude(bits: $result->download_bits, precision: 0, magnitude: 'mbit')
                : null,
            'upload' => $result->upload_bits !== null
                ? Number::bitsToMagnitude(bits: $result->upload_bits, precision: 0, magnitude: 'mbit')
                : null,
            'packet_loss' => $result->packet_loss,
            'speedtest_url' => $result->result_url,
            'url' => url('/admin/results'),
        ];

        if ($event->includesBenchmarks()) {
            $payload['benchmarks'] = $result->benchmarks;
        }

        return $payload;
    }
}
