<?php

namespace App\Jobs\Latency;

use App\Models\LatencyResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Settings\LatencySettings;

class ExecuteLatencyTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $urls; // Define this property to store the URLs
    protected int $pingCount;

    public function __construct()
    {
        $settings = app(LatencySettings::class);
        $this->pingCount = $settings->ping_count;
        $this->urls = $settings->ping_urls; // Fetch URLs from settings
    }

    public function handle()
    {
        foreach ($this->urls as $urlData) {
            $url = $urlData['url'];
            Log::info("Starting ping test for URL: {$url}");

            $command = sprintf(
                'ping -c %d %s',
                $this->pingCount,
                escapeshellarg($url)
            );

            $output = shell_exec($command);

            $latencies = $this->parseLatencies($output);
            $packetLoss = $this->parsePacketLoss($output);

            // Store the result in the database
            LatencyResult::create([
                'url' => $url,
                'min_latency' => $latencies['min'] ?? null,
                'avg_latency' => $latencies['avg'] ?? null,
                'max_latency' => $latencies['max'] ?? null,
                'packet_loss' => $packetLoss,
                'ping_count' => $this->pingCount,
            ]);
        }
    }

    protected function parseLatencies($output)
    {
        $latencies = [];
        if (preg_match_all('/time=(\d+\.?\d*) ms/', $output, $matches)) {
            $latencies = array_map('floatval', $matches[1]);
        }

        $min = $max = $avg = null;

        if (count($latencies) > 0) {
            $min = min($latencies);
            $max = max($latencies);
            $avg = array_sum($latencies) / count($latencies);
        }

        return [
            'min' => $min,
            'avg' => $avg,
            'max' => $max,
        ];
    }

    protected function parsePacketLoss($output)
    {
        if (preg_match('/(\d+)% packet loss/', $output, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
