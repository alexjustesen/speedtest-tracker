<?php

namespace App\Services\Speedtests;

use App\Services\Speedtests\Contracts\Speedtest;

abstract class HttpSpeedtest implements Speedtest
{
    /**
     * Resolve the endpoints to measure against.
     */
    abstract protected function plan(?string $target): HttpPlan;

    public function run(?string $target = null): array
    {
        $plan = $this->plan($target);

        $meter = $this->meter($plan);

        $ping = LatencyStats::summarize($meter->latency($plan->latencyUrl, ranged: $plan->rangedLatency));

        $download = empty($plan->downloadUrls)
            ? null
            : $meter->download($plan->downloadUrls, $plan->latencyUrl, $plan->rangedLatency);

        $upload = empty($plan->uploadUrls)
            ? null
            : $meter->upload($plan->uploadUrls, $plan->uploadChunk, $plan->latencyUrl, $plan->rangedLatency);

        return SpeedtestOutput::make(
            ping: $ping,
            download: $download,
            upload: $upload,
            server: $plan->server,
            isp: $plan->isp,
            externalIp: $plan->externalIp,
        );
    }

    protected function meter(HttpPlan $plan): HttpMeter
    {
        return HttpMeter::fromSettings($plan->headers);
    }
}
