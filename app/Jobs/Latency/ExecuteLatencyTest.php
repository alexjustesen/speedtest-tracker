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

    protected array $target_urls;

    protected int $pingCount;

    public function __construct(array $target_urls)
    {
        $settings = app(LatencySettings::class);
        $this->target_urls = $target_urls;
        $this->pingCount = $settings->ping_count;
    }

    public function handle()
    {
        // Rebuild URLs string from target URLs
        $urlsString = implode(' ', array_map('escapeshellarg', $this->target_urls));

        try {
            // Build and log the command
            $command = sprintf('fping -c %d -q %s', $this->pingCount, $urlsString);

            // Execute the command and log both stdout and stderr
            $output = shell_exec($command.' 2>&1');

            if ($output === null) {
                Log::warning('fping command returned null output.');

                return;
            }

            // Parse and log the results
            $results = $this->parseFpingOutput($output);

            foreach ($results as $url => $latencies) {
                try {
                    // Store latency results in the database
                    LatencyResult::create([
                        'target_url' => $url,
                        'target_name' => $this->getTargetName($url),
                        'min_latency' => $latencies['min'] ?? null,
                        'avg_latency' => $latencies['avg'] ?? null,
                        'max_latency' => $latencies['max'] ?? null,
                        'packet_loss' => $latencies['packet_loss'] ?? null,
                        'ping_count' => $this->pingCount,
                    ]);

                } catch (\Exception $e) {
                    Log::error('Database insert error for URL: '.$url.'. Error: '.$e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('Error executing latency test for URLs: '.implode(', ', $this->target_urls).'. Error: '.$e->getMessage());
        }
    }

    protected function parseFpingOutput($output)
    {
        $results = [];
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            // Handle successful ping results with min/avg/max values
            if (preg_match('/^(\S+)\s+:\s+xmt\/rcv\/%loss\s+=\s+\d+\/\d+\/(\d+)%.*, min\/avg\/max\s+=\s+([\d.]+)\/([\d.]+)\/([\d.]+)\s*$/', $line, $matches)) {
                $url = $matches[1];
                $packetLoss = $matches[2]; // %loss value
                $min = $matches[3]; // min latency
                $avg = $matches[4]; // avg latency
                $max = $matches[5]; // max latency

                $results[$url] = [
                    'packet_loss' => $packetLoss,
                    'min' => $min,
                    'avg' => $avg,
                    'max' => $max,
                ];
            }
            // Handle 100% packet loss (no latency values)
            elseif (preg_match('/^(\S+)\s+:\s+xmt\/rcv\/%loss\s+=\s+\d+\/\d+\/(\d+)%/', $line, $matches)) {
                $url = $matches[1];
                $packetLoss = $matches[2]; // %loss value

                $results[$url] = [
                    'packet_loss' => $packetLoss,
                    'min' => null, // No latency data available
                    'avg' => null, // No latency data available
                    'max' => null, // No latency data available
                ];
            } else {
                Log::warning('Unrecognized line format: '.$line);
            }
        }

        return $results;
    }

    protected function getTargetName($url)
    {
        // Assuming you have a method to retrieve the target name based on the URL
        $settings = app(LatencySettings::class);
        foreach ($settings->target_url as $target) {
            if (isset($target['url']) && $target['url'] === $url) {
                return $target['target_name'] ?? 'Unnamed';
            }
        }

        return 'Unnamed'; // Default if no name is found
    }
}
