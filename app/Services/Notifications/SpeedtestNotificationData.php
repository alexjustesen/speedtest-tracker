<?php

namespace App\Services\Notifications;

use App\Helpers\Number;
use App\Models\Result;
use Illuminate\Support\Str;

class SpeedtestNotificationData
{
    public static function make(Result $result, array $failed = []): array
    {
        return [
            'id' => $result->id,
            'service' => Str::title($result->service->getLabel()),
            'serverName' => $result->server_name,
            'serverId' => $result->server_id,
            'isp' => $result->isp,
            'ping' => round($result->ping, 2).' ms',
            'download' => Number::toBitRate(bits: $result->download_bits, precision: 2),
            'upload' => Number::toBitRate(bits: $result->upload_bits, precision: 2),
            'packetLoss' => is_numeric($result->packet_loss) ? $result->packet_loss : 'n/a'.' %',
            'pingJitter' => $result->ping_jitter.' ms',
            'pingLow' => $result->ping_low.' ms',
            'pingHigh' => $result->ping_high.' ms',
            'downloadBytes' => $result->download_bytes,
            'downloadLatencyIqm' => $result->download_latency_iqm.' ms',
            'downloadLatencyLow' => $result->download_latency_low.' ms',
            'downloadLatencyHigh' => $result->download_latency_high.' ms',
            'downloadLatencyJitter' => $result->download_latency_jitter.' ms',
            'uploadBytes' => $result->upload_bytes,
            'uploadLatencyIqm' => $result->upload_latency_iqm.' ms',
            'uploadLatencyLow' => $result->upload_latency_low.' ms',
            'uploadLatencyHigh' => $result->upload_latency_high.' ms',
            'uploadLatencyJitter' => $result->upload_latency_jitter.' ms',
            'externalIp' => $result->ip_address,
            'serverHost' => $result->server_host,
            'serverPort' => $result->server_port,
            'serverLocation' => $result->server_location,
            'serverCountry' => $result->server_country,
            'serverIp' => $result->server_ip,
            'speedtest_url' => $result->result_url,
            'url' => url('/admin/results'),
            'metrics' => $failed,
            'app_name' => config('app.name'),
        ];
    }
}
