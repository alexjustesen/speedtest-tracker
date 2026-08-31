<?php

namespace App\Services\Prometheus;

use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Exception;
use Flow\Snappy\Snappy;
use Illuminate\Support\Facades\Http;

class PrometheusRemoteWriteService extends PrometheusService
{
    public function send(Result $result): void
    {
        $settings = app(DataIntegrationSettings::class);

        $payload = $this->buildWriteRequest($result);
        $compressed = (new Snappy)->compress($payload);

        $request = Http::withHeaders([
            'Content-Encoding' => 'snappy',
            'X-Prometheus-Remote-Write-Version' => '0.1.0',
        ]);

        if ($settings->prometheus_remote_write_username && $settings->prometheus_remote_write_password) {
            $request = $request->withBasicAuth(
                $settings->prometheus_remote_write_username,
                $settings->prometheus_remote_write_password,
            );
        }

        $response = $request
            ->withBody($compressed, 'application/x-protobuf')
            ->post($settings->prometheus_remote_write_url);

        if (! $response->successful()) {
            throw new Exception("Prometheus remote write failed [{$response->status()}]: {$response->body()}");
        }
    }

    public function buildWriteRequest(Result $result): string
    {
        $timestampMs = ($result->updated_at?->timestamp ?? now()->timestamp) * 1000;
        $labels = $this->buildLabels($result);

        $writeRequest = '';

        // info metric (always 1)
        $writeRequest .= $this->lengthDelimited(1, $this->encodeTimeSeries(
            array_merge(['__name__' => 'speedtest_tracker_info'], $labels),
            1.0,
            $timestampMs,
        ));

        // up metric (always 1)
        $writeRequest .= $this->lengthDelimited(1, $this->encodeTimeSeries(
            ['__name__' => 'speedtest_tracker_up'],
            1.0,
            $timestampMs,
        ));

        // build_info metric (always 1)
        $writeRequest .= $this->lengthDelimited(1, $this->encodeTimeSeries(
            ['__name__' => 'speedtest_tracker_build_info', 'version' => config('speedtest.build_version', '')],
            1.0,
            $timestampMs,
        ));

        foreach ($this->collectMetrics($result) as $name => $metric) {
            if ($metric['value'] === null) {
                continue;
            }

            $writeRequest .= $this->lengthDelimited(
                1,
                $this->encodeTimeSeries(
                    array_merge(['__name__' => 'speedtest_tracker_'.$name], $labels),
                    $metric['value'],
                    $timestampMs,
                ),
            );
        }

        return $writeRequest;
    }

    private function encodeTimeSeries(array $labels, float $value, int $timestampMs): string
    {
        $encoded = '';

        foreach ($labels as $name => $labelValue) {
            $encoded .= $this->lengthDelimited(1, $this->encodeLabel($name, (string) $labelValue));
        }

        $encoded .= $this->lengthDelimited(2, $this->encodeSample($value, $timestampMs));

        return $encoded;
    }

    private function encodeLabel(string $name, string $value): string
    {
        return $this->lengthDelimited(1, $name).$this->lengthDelimited(2, $value);
    }

    private function encodeSample(float $value, int $timestampMs): string
    {
        // field 1: double (wire type 1 = 64-bit), field 2: int64 (wire type 0 = varint)
        $doubleTag = $this->varint((1 << 3) | 1);
        $doubleBytes = pack('e', $value); // little-endian IEEE 754 double

        $int64Tag = $this->varint((2 << 3) | 0);
        $int64Bytes = $this->varint($timestampMs);

        return $doubleTag.$doubleBytes.$int64Tag.$int64Bytes;
    }

    private function lengthDelimited(int $fieldNumber, string $data): string
    {
        return $this->varint(($fieldNumber << 3) | 2).$this->varint(strlen($data)).$data;
    }

    /**
     * Encode a non-negative integer as a protobuf base-128 varint.
     */
    private function varint(int $value): string
    {
        $bytes = '';

        while ($value > 0x7F) {
            $bytes .= chr(($value & 0x7F) | 0x80);
            $value >>= 7;
        }

        return $bytes.chr($value);
    }
}
