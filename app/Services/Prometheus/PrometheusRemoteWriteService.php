<?php

namespace App\Services\Prometheus;

use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Exception;
use Flow\Snappy\Snappy;
use Illuminate\Support\Facades\Http;

class PrometheusRemoteWriteService
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

        $metrics = [
            'speedtest_tracker_download_bytes_per_second' => $result->download !== null ? (float) $result->download : null,
            'speedtest_tracker_upload_bytes_per_second' => $result->upload !== null ? (float) $result->upload : null,
            'speedtest_tracker_download_bits_per_second' => $result->download_bits !== null ? (float) $result->download_bits : null,
            'speedtest_tracker_upload_bits_per_second' => $result->upload_bits !== null ? (float) $result->upload_bits : null,
            'speedtest_tracker_ping_ms' => $result->ping !== null ? (float) $result->ping : null,
            'speedtest_tracker_ping_low_ms' => $result->ping_low !== null ? (float) $result->ping_low : null,
            'speedtest_tracker_ping_high_ms' => $result->ping_high !== null ? (float) $result->ping_high : null,
            'speedtest_tracker_ping_jitter_ms' => $result->ping_jitter !== null ? (float) $result->ping_jitter : null,
            'speedtest_tracker_download_jitter_ms' => $result->download_jitter !== null ? (float) $result->download_jitter : null,
            'speedtest_tracker_upload_jitter_ms' => $result->upload_jitter !== null ? (float) $result->upload_jitter : null,
            'speedtest_tracker_packet_loss_percent' => $result->packet_loss !== null ? (float) $result->packet_loss : null,
            'speedtest_tracker_download_latency_iqm_ms' => $result->downloadlatencyiqm !== null ? (float) $result->downloadlatencyiqm : null,
            'speedtest_tracker_download_latency_low_ms' => $result->downloadlatency_low !== null ? (float) $result->downloadlatency_low : null,
            'speedtest_tracker_download_latency_high_ms' => $result->downloadlatency_high !== null ? (float) $result->downloadlatency_high : null,
            'speedtest_tracker_upload_latency_iqm_ms' => $result->uploadlatencyiqm !== null ? (float) $result->uploadlatencyiqm : null,
            'speedtest_tracker_upload_latency_low_ms' => $result->uploadlatency_low !== null ? (float) $result->uploadlatency_low : null,
            'speedtest_tracker_upload_latency_high_ms' => $result->uploadlatency_high !== null ? (float) $result->uploadlatency_high : null,
            'speedtest_tracker_test_downloaded_bytes_total' => $result->downloaded_bytes !== null ? (float) $result->downloaded_bytes : null,
            'speedtest_tracker_test_uploaded_bytes_total' => $result->uploaded_bytes !== null ? (float) $result->uploaded_bytes : null,
            'speedtest_tracker_download_elapsed_ms' => $result->download_elapsed !== null ? (float) $result->download_elapsed : null,
            'speedtest_tracker_upload_elapsed_ms' => $result->upload_elapsed !== null ? (float) $result->upload_elapsed : null,
        ];

        $writeRequest = '';

        // info metric (always 1)
        $infoLabels = array_merge(['__name__' => 'speedtest_tracker_info'], $labels);
        $writeRequest .= $this->lengthDelimited(1, $this->encodeTimeSeries($infoLabels, 1.0, $timestampMs));

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

        foreach ($metrics as $name => $value) {
            if ($value === null) {
                continue;
            }

            $writeRequest .= $this->lengthDelimited(
                1,
                $this->encodeTimeSeries(
                    array_merge(['__name__' => $name], $labels),
                    $value,
                    $timestampMs,
                ),
            );
        }

        return $writeRequest;
    }

    private function buildLabels(Result $result): array
    {
        return [
            'server_name' => $result->server_name ?? '',
            'server_location' => $result->server_location ?? '',
            'isp' => $result->isp ?? '',
            'scheduled' => $result->scheduled ? 'true' : 'false',
            'healthy' => $result->healthy ? 'true' : 'false',
            'status' => $result->status->value,
            'app_name' => config('app.name', 'Speedtest Tracker'),
        ];
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
