<?php

namespace App\Actions\Influxdb\v2;

use App\Helpers\Bitrate;
use App\Models\Result;
use Illuminate\Support\Arr;
use InfluxDB2\Point;
use Lorisleiva\Actions\Concerns\AsAction;

class BuildPointData
{
    use AsAction;

    public function handle(Result $result): Point
    {
        $point = Point::measurement('speedtest')
            ->addTag('app_name', config('app.name'))
            ->time($result->created_at->timestamp ?? time());

        // Qualitative tags
        $point->addTag('id', $result->id)
            ->addTag('external_ip', Arr::get($result->data, 'interface.externalIp'))
            ->addTag('id', $result->id)
            ->addTag('isp', $result->isp)
            ->addTag('service', $result->service->value)
            ->addTag('server_id', Arr::get($result->data, 'server.id'))
            ->addTag('server_name', Arr::get($result->data, 'server.name'))
            ->addTag('server_country', Arr::get($result->data, 'server.country'))
            ->addTag('server_location', Arr::get($result->data, 'server.location'))
            ->addTag('healthy', $this->evalHealthyTag($result->healthy))
            ->addTag('status', $result->status->value)
            ->addTag('scheduled', $result->scheduled ? 'true' : 'false');

        // Quantitative fields
        $point->addField('download', $result->download)
            ->addField('upload', $result->upload)
            ->addField('ping', $result->ping)
            ->addField('download_bits', ! blank($result->download) ? Bitrate::bytesToBits($result->download) : null)
            ->addField('upload_bits', ! blank($result->upload) ? Bitrate::bytesToBits($result->upload) : null)
            ->addField('download_jitter', Arr::get($result->data, 'download.latency.jitter'))
            ->addField('upload_jitter', Arr::get($result->data, 'upload.latency.jitter'))
            ->addField('ping_jitter', Arr::get($result->data, 'ping.jitter'))
            ->addField('download_latency_avg', Arr::get($result->data, 'download.latency.iqm'))
            ->addField('download_latency_high', Arr::get($result->data, 'download.latency.high'))
            ->addField('download_latency_low', Arr::get($result->data, 'download.latency.low'))
            ->addField('upload_latency_avg', Arr::get($result->data, 'upload.latency.iqm'))
            ->addField('upload_latency_high', Arr::get($result->data, 'upload.latency.high'))
            ->addField('upload_latency_low', Arr::get($result->data, 'upload.latency.low'))
            ->addField('packet_loss', Arr::get($result->data, 'packetLoss'));

        return $point;
    }

    private function evalHealthyTag(?bool $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return $value
            ? 'true'
            : 'false';
    }
}
