<?php

namespace App\Actions\Influxdb\v2;

use App\Helpers\Bitrate;
use App\Helpers\Number;
use App\Models\Result;
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
            ->addTag('external_ip', $result->ip_address)
            ->addTag('id', $result->id)
            ->addTag('isp', $result->isp)
            ->addTag('service', $result->service->value)
            ->addTag('server_id', $result->server_id)
            ->addTag('server_name', $result->server_name)
            ->addTag('server_country', $result->server_country)
            ->addTag('server_location', $result->server_location)
            ->addTag('healthy', $this->evalHealthyTag($result->healthy))
            ->addTag('status', $result->status->value)
            ->addTag('scheduled', $result->scheduled ? 'true' : 'false');

        // Quantitative fields
        $point->addField('download', Number::castToType($result->download, 'int'))
            ->addField('upload', Number::castToType($result->upload, 'int'))
            ->addField('ping', Number::castToType($result->ping, 'float'))
            ->addField('download_bits', ! blank($result->download) ? Number::castToType(Bitrate::bytesToBits($result->download), 'int') : null)
            ->addField('upload_bits', ! blank($result->upload) ? Number::castToType(Bitrate::bytesToBits($result->upload), 'int') : null)
            ->addField('download_elapsed', Number::castToType($result->download_elapsed, 'float'))
            ->addField('upload_elapsed', Number::castToType($result->upload_elapsed, 'float'))
            ->addField('download_jitter', Number::castToType($result->download_jitter, 'float'))
            ->addField('upload_jitter', Number::castToType($result->upload_jitter, 'float'))
            ->addField('ping_jitter', Number::castToType($result->ping_jitter, 'float'))
            ->addField('download_latency_avg', Number::castToType($result->download_latency_iqm, 'float'))
            ->addField('download_latency_high', Number::castToType($result->download_latency_high, 'float'))
            ->addField('download_latency_low', Number::castToType($result->download_latency_low, 'float'))
            ->addField('upload_latency_avg', Number::castToType($result->upload_latency_iqm, 'float'))
            ->addField('upload_latency_high', Number::castToType($result->upload_latency_high, 'float'))
            ->addField('upload_latency_low', Number::castToType($result->upload_latency_low, 'float'))
            ->addField('downloaded_bytes', Number::castToType($result->download_bytes, 'float'))
            ->addField('uploaded_bytes', Number::castToType($result->upload_bytes, 'float'))
            ->addField('packet_loss', Number::castToType($result->packet_loss, 'float'))
            ->addField('log_message', $result->error_message);

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
