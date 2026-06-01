<?php

namespace App\Data;

use Illuminate\Support\Arr;

readonly class OoklaResult
{
    public function __construct(
        public ?float $ping,
        public ?float $pingJitter,
        public ?float $pingLow,
        public ?float $pingHigh,
        public ?int $download,
        public ?int $downloadBytes,
        public ?float $downloadJitter,
        public ?float $downloadLatencyIqm,
        public ?float $downloadLatencyLow,
        public ?float $downloadLatencyHigh,
        public ?int $downloadElapsed,
        public ?int $upload,
        public ?int $uploadBytes,
        public ?float $uploadJitter,
        public ?float $uploadLatencyIqm,
        public ?float $uploadLatencyLow,
        public ?float $uploadLatencyHigh,
        public ?int $uploadElapsed,
        public ?float $packetLoss,
        public ?string $isp,
        public ?string $ipAddress,
        public ?int $serverId,
        public ?string $serverName,
        public ?string $serverHost,
        public ?string $serverIp,
        public ?string $serverCountry,
        public ?string $serverLocation,
        public ?string $resultUrl,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            ping: Arr::get($data, 'ping.latency'),
            pingJitter: Arr::get($data, 'ping.jitter'),
            pingLow: Arr::get($data, 'ping.low'),
            pingHigh: Arr::get($data, 'ping.high'),
            download: Arr::get($data, 'download.bandwidth'),
            downloadBytes: Arr::get($data, 'download.bytes'),
            downloadJitter: Arr::get($data, 'download.latency.jitter'),
            downloadLatencyIqm: Arr::get($data, 'download.latency.iqm'),
            downloadLatencyLow: Arr::get($data, 'download.latency.low'),
            downloadLatencyHigh: Arr::get($data, 'download.latency.high'),
            downloadElapsed: Arr::get($data, 'download.elapsed'),
            upload: Arr::get($data, 'upload.bandwidth'),
            uploadBytes: Arr::get($data, 'upload.bytes'),
            uploadJitter: Arr::get($data, 'upload.latency.jitter'),
            uploadLatencyIqm: Arr::get($data, 'upload.latency.iqm'),
            uploadLatencyLow: Arr::get($data, 'upload.latency.low'),
            uploadLatencyHigh: Arr::get($data, 'upload.latency.high'),
            uploadElapsed: Arr::get($data, 'upload.elapsed'),
            packetLoss: Arr::get($data, 'packetLoss'),
            isp: Arr::get($data, 'isp'),
            ipAddress: Arr::get($data, 'interface.externalIp'),
            serverId: Arr::get($data, 'server.id'),
            serverName: Arr::get($data, 'server.name'),
            serverHost: Arr::get($data, 'server.host'),
            serverIp: Arr::get($data, 'server.ip'),
            serverCountry: Arr::get($data, 'server.country'),
            serverLocation: Arr::get($data, 'server.location'),
            resultUrl: Arr::get($data, 'result.url'),
        );
    }

    public function toModelAttributes(): array
    {
        return [
            'ping' => $this->ping,
            'ping_jitter' => $this->pingJitter,
            'ping_low' => $this->pingLow,
            'ping_high' => $this->pingHigh,
            'download' => $this->download,
            'download_bytes' => $this->downloadBytes,
            'download_jitter' => $this->downloadJitter,
            'download_latency_iqm' => $this->downloadLatencyIqm,
            'download_latency_low' => $this->downloadLatencyLow,
            'download_latency_high' => $this->downloadLatencyHigh,
            'download_elapsed' => $this->downloadElapsed,
            'upload' => $this->upload,
            'upload_bytes' => $this->uploadBytes,
            'upload_jitter' => $this->uploadJitter,
            'upload_latency_iqm' => $this->uploadLatencyIqm,
            'upload_latency_low' => $this->uploadLatencyLow,
            'upload_latency_high' => $this->uploadLatencyHigh,
            'upload_elapsed' => $this->uploadElapsed,
            'packet_loss' => $this->packetLoss,
            'isp' => $this->isp,
            'ip_address' => $this->ipAddress,
            'server_id' => $this->serverId,
            'server_name' => $this->serverName,
            'server_host' => $this->serverHost,
            'server_ip' => $this->serverIp,
            'server_country' => $this->serverCountry,
            'server_location' => $this->serverLocation,
            'result_url' => $this->resultUrl,
        ];
    }
}
