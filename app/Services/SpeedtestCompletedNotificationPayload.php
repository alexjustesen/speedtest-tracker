<?php

namespace App\Services;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use Illuminate\Support\Str;

class SpeedtestCompletedNotificationPayload
{
    /**
     * Generate the payload for the speedtest completed notification.
     */
    public function generateSpeedtestPayload(SpeedtestCompleted $event, string $viewName): string
    {
        return view($viewName, [
            'id' => $event->result->id,
            'service' => Str::title($event->result->service),
            'serverName' => $event->result->server_name,
            'serverId' => $event->result->server_id,
            'isp' => $event->result->isp,
            'ping' => round($event->result->ping).' ms',
            'download' => Number::toBitRate(bits: $event->result->download_bits, precision: 2),
            'upload' => Number::toBitRate(bits: $event->result->upload_bits, precision: 2),
            'packetLoss' => $event->result->packet_loss,
            'speedtest_url' => $event->result->result_url,
            'url' => url('/admin/results'),
        ])->render();
    }
}
