<?php

namespace App\Notifications;

use App\Helpers\Number;
use App\Models\Result;
use Illuminate\Support\Str;

class SpeedtestNotificationData
{
    public static function make(Result $result): array
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
            'packetLoss' => is_numeric($result->packet_loss) ? $result->packet_loss : 'n/a',
            'speedtest_url' => $result->result_url,
            'url' => url('/admin/results'),
        ];
    }
}
