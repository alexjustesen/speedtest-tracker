<?php

namespace App\Jobs\Latency;

use App\Models\LatencyResult;
use App\Settings\LatencySettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteLatencyTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $target_url;

    protected string $target_name;

    protected int $pingCount;

    public function __construct(string $target_url, string $target_name)
    {
        $settings = app(LatencySettings::class);
        $this->target_url = $target_url;
        $this->target_name = $target_name;
        $this->pingCount = $settings->ping_count;
    }

    public function handle()
    {
        Log::info("Starting ping test for URL: {$this->target_url}");

        try {
            $command = sprintf(
                'ping -c %d %s',
                $this->pingCount,
                escapeshellarg($this->target_url)
            );

            $output = shell_exec($command);

            if ($output === null) {
                Log::error("Failed to execute ping command for URL: {$this->target_url}");

                return;
            }

            $latencies = $this->parseLatencies($output);
            $packetLoss = $this->parsePacketLoss($output);

            LatencyResult::create([
                'target_url' => $this->target_url,
                'target_name' => $this->target_name,
                'min_latency' => $latencies['min'] ?? null,
                'avg_latency' => $latencies['avg'] ?? null,
                'max_latency' => $latencies['max'] ?? null,
                'packet_loss' => $packetLoss,
                'ping_count' => $this->pingCount,
            ]);

        } catch (\Exception $e) {
            Log::error("Error executing latency test for URL: {$this->target_url}. Error: {$e->getMessage()}");
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
