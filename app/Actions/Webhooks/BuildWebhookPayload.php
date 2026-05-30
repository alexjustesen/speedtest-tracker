<?php

namespace App\Actions\Webhooks;

use App\Enums\WebhookEvent;
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
    public function handle(Result $result, WebhookEvent $event): array
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

        if (in_array($event, [WebhookEvent::BenchmarkHealthy, WebhookEvent::BenchmarkUnhealthy], strict: true)) {
            $payload['benchmarks'] = $result->benchmarks;
        }

        return $payload;
    }
}
