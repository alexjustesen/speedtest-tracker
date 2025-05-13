<?php

namespace App\Actions\Influxdb\v2;

use App\Helpers\Bitrate;
use App\Helpers\Number;
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
        $point->addTag('result_id', $result->id)
            ->addTag('external_ip', Arr::get($result->data, 'interface.externalIp'))
            ->addTag('id', $result->id)
            ->addTag('isp', Arr::get($result->data, 'isp'))
            ->addTag('service', $result->service->value)
            ->addTag('server_id', Arr::get($result->data, 'server.id'))
            ->addTag('server_name', Arr::get($result->data, 'server.name'))
            ->addTag('server_country', Arr::get($result->data, 'server.country'))
            ->addTag('server_location', Arr::get($result->data, 'server.location'))
            ->addTag('healthy', $this->evalHealthyTag($result->healthy))
            ->addTag('status', $result->status->value)
            ->addTag('scheduled', $result->scheduled ? 'true' : 'false');

        // Core test fields — cast if present, skip if null
        $point->addField('download', Number::castToType($result->download, 'int'))
            ->addField('upload', Number::castToType($result->upload, 'int'))
            ->addField('ping', Number::castToType($result->ping, 'float'))
            ->addField('download_bits', ! blank($result->download) ? Number::castToType(Bitrate::bytesToBits($result->download), 'int') : null)
            ->addField('upload_bits', ! blank($result->upload) ? Number::castToType(Bitrate::bytesToBits($result->upload), 'int') : null)
            ->addField('download_jitter', Number::castToType(Arr::get($result->data, 'download.latency.jitter'), 'float'))
            ->addField('upload_jitter', Number::castToType(Arr::get($result->data, 'upload.latency.jitter'), 'float'))
            ->addField('ping_jitter', Number::castToType(Arr::get($result->data, 'ping.jitter'), 'float'))
            ->addField('download_latency_avg', Number::castToType(Arr::get($result->data, 'download.latency.iqm'), 'float'))
            ->addField('download_latency_high', Number::castToType(Arr::get($result->data, 'download.latency.high'), 'float'))
            ->addField('download_latency_low', Number::castToType(Arr::get($result->data, 'download.latency.low'), 'float'))
            ->addField('upload_latency_avg', Number::castToType(Arr::get($result->data, 'upload.latency.iqm'), 'float'))
            ->addField('upload_latency_high', Number::castToType(Arr::get($result->data, 'upload.latency.high'), 'float'))
            ->addField('upload_latency_low', Number::castToType(Arr::get($result->data, 'upload.latency.low'), 'float'))
            ->addField('packet_loss', Number::castToType(Arr::get($result->data, 'packetLoss'), 'float'))
            ->addField('log_message', Arr::get($result->data, 'message'));

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
