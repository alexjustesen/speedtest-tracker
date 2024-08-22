<?php

namespace App\Jobs\Pings;

use App\Models\PingResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecutePingTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    protected $pingCount;

    public function __construct($url)
    {
        $this->url = $url;
        $this->pingCount = config('ping.ping_count');
    }

    public function handle()
    {
        Log::info("Starting ping test for URL: {$this->url}");

        $command = sprintf(
            'ping -c %d %s',
            $this->pingCount,
            escapeshellarg($this->url)
        );

        $output = shell_exec($command);

        $latencies = $this->parseLatencies($output);
        $packetLoss = $this->parsePacketLoss($output);

        // Store the result in the database
        PingResult::create([
            'url' => $this->url,
            'min_latency' => $latencies['min'] ?? null,
            'avg_latency' => $latencies['avg'] ?? null,
            'max_latency' => $latencies['max'] ?? null,
            'packet_loss' => $packetLoss,
            'ping_count' => $this->pingCount,
        ]);
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
