<?php

namespace App\Livewire;

use App\Enums\ResultStatus;
use App\Helpers\Bitrate;
use App\Models\Result;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Metrics Dashboard')]
class MetricsDashboard extends Component
{
    public string $startDate = '';

    public string $endDate = '';

    public ?int $lastResultId = null;

    public function mount(): void
    {
        if (empty($this->startDate)) {
            $this->startDate = $this->getDefaultStartDate()->format('Y-m-d');
        }

        if (empty($this->endDate)) {
            $this->endDate = now()->format('Y-m-d');
        }

        $this->lastResultId = Result::whereIn('status', [ResultStatus::Completed, ResultStatus::Failed])
            ->latest()
            ->value('id');
    }

    protected function getDefaultStartDate(): \Carbon\Carbon
    {
        $chartRange = config('speedtest.default_chart_range', '24h');

        return match (true) {
            str_ends_with($chartRange, 'h') => now()->subHours((int) rtrim($chartRange, 'h')),
            str_ends_with($chartRange, 'd') => now()->subDays((int) rtrim($chartRange, 'd')),
            str_ends_with($chartRange, 'w') => now()->subWeeks((int) rtrim($chartRange, 'w')),
            str_ends_with($chartRange, 'm') => now()->subMonths((int) rtrim($chartRange, 'm')),
            default => now()->subDay(),
        };
    }

    public function updatedStartDate(): void
    {
        $this->dispatch('charts-updated', chartData: $this->getChartData());
    }

    public function updatedEndDate(): void
    {
        $this->dispatch('charts-updated', chartData: $this->getChartData());
    }

    public function setLastDay(): void
    {
        $this->startDate = now()->subDay()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->dispatch('charts-updated', chartData: $this->getChartData());
    }

    public function setLastWeek(): void
    {
        $this->startDate = now()->subWeek()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->dispatch('charts-updated', chartData: $this->getChartData());
    }

    public function setLastMonth(): void
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->dispatch('charts-updated', chartData: $this->getChartData());
    }

    public function checkForNewResults(): void
    {
        $latestResultId = Result::whereIn('status', [ResultStatus::Completed, ResultStatus::Failed])
            ->latest()
            ->value('id');

        if ($latestResultId && $latestResultId !== $this->lastResultId) {
            $this->lastResultId = $latestResultId;
            $this->refreshCharts();
        }
    }

    public function refreshCharts(): void
    {
        $this->dispatch('charts-updated', chartData: $this->getChartData());
    }

    public function getChartData(): array
    {
        $startDate = \Carbon\Carbon::parse($this->startDate)->startOfDay();
        $endDate = \Carbon\Carbon::parse($this->endDate)->endOfDay();

        $results = Result::whereIn('status', [ResultStatus::Completed, ResultStatus::Failed])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();

        // Determine label format based on date range duration
        $daysDifference = $startDate->diffInDays($endDate);
        $labelFormat = match (true) {
            $daysDifference <= 1 => 'g:i A',           // 3:45 PM
            $daysDifference <= 7 => 'D g:i A',          // Mon 3:45 PM
            default => 'M j g:i A',                     // Dec 15 3:45 PM
        };

        $labels = [];
        $resultIds = [];
        $downloadData = [];
        $uploadData = [];
        $pingData = [];
        $downloadLatencyData = [];
        $uploadLatencyData = [];
        $downloadJitterData = [];
        $uploadJitterData = [];
        $pingJitterData = [];
        $downloadBenchmarkFailed = [];
        $uploadBenchmarkFailed = [];
        $pingBenchmarkFailed = [];
        $downloadBenchmarks = [];
        $uploadBenchmarks = [];
        $pingBenchmarks = [];

        foreach ($results as $result) {
            // Format timestamp for label
            $labels[] = $result->created_at->format($labelFormat);

            // Store result ID
            $resultIds[] = $result->id;

            // Convert download from bytes/sec to Mbps
            $downloadBits = Bitrate::bytesToBits($result->download ?? 0);
            $downloadData[] = round($downloadBits / 1000000, 2); // Convert to Mbps

            // Convert upload from bytes/sec to Mbps
            $uploadBits = Bitrate::bytesToBits($result->upload ?? 0);
            $uploadData[] = round($uploadBits / 1000000, 2); // Convert to Mbps

            // Ping in milliseconds
            $pingData[] = round($result->ping ?? 0, 2);

            // Latency IQM in milliseconds
            $downloadLatencyData[] = round($result->downloadlatencyiqm ?? 0, 2);
            $uploadLatencyData[] = round($result->uploadlatencyiqm ?? 0, 2);

            // Jitter in milliseconds
            $downloadJitterData[] = round($result->downloadJitter ?? 0, 2);
            $uploadJitterData[] = round($result->uploadJitter ?? 0, 2);
            $pingJitterData[] = round($result->pingJitter ?? 0, 2);

            // Track benchmark failures and full benchmark data
            $benchmarks = $result->benchmarks ?? [];
            $downloadBenchmarkFailed[] = isset($benchmarks['download']) && $benchmarks['download']['passed'] === false;
            $uploadBenchmarkFailed[] = isset($benchmarks['upload']) && $benchmarks['upload']['passed'] === false;
            $pingBenchmarkFailed[] = isset($benchmarks['ping']) && $benchmarks['ping']['passed'] === false;

            $downloadBenchmarks[] = $benchmarks['download'] ?? null;
            $uploadBenchmarks[] = $benchmarks['upload'] ?? null;
            $pingBenchmarks[] = $benchmarks['ping'] ?? null;
        }

        // Calculate download statistics
        $downloadLatest = count($downloadData) > 0 ? end($downloadData) : 0;
        $downloadAvg = count($downloadData) > 0 ? round(array_sum($downloadData) / count($downloadData), 2) : 0;
        $downloadP95 = $this->calculatePercentile($downloadData, 95);
        $downloadMax = count($downloadData) > 0 ? round(max($downloadData), 2) : 0;
        $downloadMin = count($downloadData) > 0 ? round(min($downloadData), 2) : 0;

        // Calculate upload statistics
        $uploadLatest = count($uploadData) > 0 ? end($uploadData) : 0;
        $uploadAvg = count($uploadData) > 0 ? round(array_sum($uploadData) / count($uploadData), 2) : 0;
        $uploadP95 = $this->calculatePercentile($uploadData, 95);
        $uploadMax = count($uploadData) > 0 ? round(max($uploadData), 2) : 0;
        $uploadMin = count($uploadData) > 0 ? round(min($uploadData), 2) : 0;

        // Calculate ping statistics
        $pingLatest = count($pingData) > 0 ? end($pingData) : 0;
        $pingAvg = count($pingData) > 0 ? round(array_sum($pingData) / count($pingData), 2) : 0;
        $pingP95 = $this->calculatePercentile($pingData, 95);
        $pingMax = count($pingData) > 0 ? round(max($pingData), 2) : 0;
        $pingMin = count($pingData) > 0 ? round(min($pingData), 2) : 0;

        // Calculate jitter statistics
        $downloadJitterLatest = count($downloadJitterData) > 0 ? end($downloadJitterData) : 0;
        $downloadJitterAvg = count($downloadJitterData) > 0 ? round(array_sum($downloadJitterData) / count($downloadJitterData), 2) : 0;
        $downloadJitterP95 = $this->calculatePercentile($downloadJitterData, 95);
        $downloadJitterMax = count($downloadJitterData) > 0 ? round(max($downloadJitterData), 2) : 0;
        $downloadJitterMin = count($downloadJitterData) > 0 ? round(min($downloadJitterData), 2) : 0;

        $uploadJitterLatest = count($uploadJitterData) > 0 ? end($uploadJitterData) : 0;
        $uploadJitterAvg = count($uploadJitterData) > 0 ? round(array_sum($uploadJitterData) / count($uploadJitterData), 2) : 0;
        $uploadJitterP95 = $this->calculatePercentile($uploadJitterData, 95);
        $uploadJitterMax = count($uploadJitterData) > 0 ? round(max($uploadJitterData), 2) : 0;
        $uploadJitterMin = count($uploadJitterData) > 0 ? round(min($uploadJitterData), 2) : 0;

        $pingJitterLatest = count($pingJitterData) > 0 ? end($pingJitterData) : 0;
        $pingJitterAvg = count($pingJitterData) > 0 ? round(array_sum($pingJitterData) / count($pingJitterData), 2) : 0;
        $pingJitterP95 = $this->calculatePercentile($pingJitterData, 95);
        $pingJitterMax = count($pingJitterData) > 0 ? round(max($pingJitterData), 2) : 0;
        $pingJitterMin = count($pingJitterData) > 0 ? round(min($pingJitterData), 2) : 0;

        // Calculate latency statistics
        $downloadLatencyLatest = count($downloadLatencyData) > 0 ? end($downloadLatencyData) : 0;
        $downloadLatencyMax = count($downloadLatencyData) > 0 ? round(max($downloadLatencyData), 2) : 0;
        $downloadLatencyMin = count($downloadLatencyData) > 0 ? round(min($downloadLatencyData), 2) : 0;

        $uploadLatencyLatest = count($uploadLatencyData) > 0 ? end($uploadLatencyData) : 0;
        $uploadLatencyMax = count($uploadLatencyData) > 0 ? round(max($uploadLatencyData), 2) : 0;
        $uploadLatencyMin = count($uploadLatencyData) > 0 ? round(min($uploadLatencyData), 2) : 0;

        // Calculate healthy ratio for each metric based on benchmark KPI
        $downloadPassedCount = collect($downloadBenchmarkFailed)->filter(fn ($failed) => $failed === false)->count();
        $uploadPassedCount = collect($uploadBenchmarkFailed)->filter(fn ($failed) => $failed === false)->count();
        $pingPassedCount = collect($pingBenchmarkFailed)->filter(fn ($failed) => $failed === false)->count();

        $downloadHealthyRatio = count($results) > 0 ? round(($downloadPassedCount / count($results)) * 100, 1) : 0;
        $uploadHealthyRatio = count($results) > 0 ? round(($uploadPassedCount / count($results)) * 100, 1) : 0;
        $pingHealthyRatio = count($results) > 0 ? round(($pingPassedCount / count($results)) * 100, 1) : 0;

        // Determine if latest stat failed benchmark
        $downloadLatestFailed = count($downloadBenchmarkFailed) > 0 ? end($downloadBenchmarkFailed) : false;
        $uploadLatestFailed = count($uploadBenchmarkFailed) > 0 ? end($uploadBenchmarkFailed) : false;
        $pingLatestFailed = count($pingBenchmarkFailed) > 0 ? end($pingBenchmarkFailed) : false;

        // Get latest benchmark data
        $downloadLatestBenchmark = count($downloadBenchmarks) > 0 ? end($downloadBenchmarks) : null;
        $uploadLatestBenchmark = count($uploadBenchmarks) > 0 ? end($uploadBenchmarks) : null;
        $pingLatestBenchmark = count($pingBenchmarks) > 0 ? end($pingBenchmarks) : null;

        // Check if there are any failed benchmarks in the date range
        $hasFailedResults = collect($downloadBenchmarkFailed)->contains(true) ||
                           collect($uploadBenchmarkFailed)->contains(true) ||
                           collect($pingBenchmarkFailed)->contains(true);

        return [
            'labels' => $labels,
            'resultIds' => $resultIds,
            'hasFailedResults' => $hasFailedResults,
            'download' => $downloadData,
            'upload' => $uploadData,
            'ping' => $pingData,
            'downloadLatency' => $downloadLatencyData,
            'uploadLatency' => $uploadLatencyData,
            'downloadJitter' => $downloadJitterData,
            'uploadJitter' => $uploadJitterData,
            'pingJitter' => $pingJitterData,
            'downloadBenchmarkFailed' => $downloadBenchmarkFailed,
            'uploadBenchmarkFailed' => $uploadBenchmarkFailed,
            'pingBenchmarkFailed' => $pingBenchmarkFailed,
            'downloadBenchmarks' => $downloadBenchmarks,
            'uploadBenchmarks' => $uploadBenchmarks,
            'pingBenchmarks' => $pingBenchmarks,
            'count' => count($results),
            'downloadStats' => [
                'latest' => $downloadLatest,
                'latestFailed' => $downloadLatestFailed,
                'latestBenchmark' => $downloadLatestBenchmark,
                'average' => $downloadAvg,
                'p95' => $downloadP95,
                'maximum' => $downloadMax,
                'minimum' => $downloadMin,
                'healthy' => $downloadHealthyRatio,
                'tests' => count($results),
            ],
            'uploadStats' => [
                'latest' => $uploadLatest,
                'latestFailed' => $uploadLatestFailed,
                'latestBenchmark' => $uploadLatestBenchmark,
                'average' => $uploadAvg,
                'p95' => $uploadP95,
                'maximum' => $uploadMax,
                'minimum' => $uploadMin,
                'healthy' => $uploadHealthyRatio,
                'tests' => count($results),
            ],
            'pingStats' => [
                'latest' => $pingLatest,
                'latestFailed' => $pingLatestFailed,
                'latestBenchmark' => $pingLatestBenchmark,
                'average' => $pingAvg,
                'p95' => $pingP95,
                'maximum' => $pingMax,
                'minimum' => $pingMin,
                'healthy' => $pingHealthyRatio,
                'tests' => count($results),
            ],
            'latencyStats' => [
                'downloadLatest' => $downloadLatencyLatest,
                'downloadMaximum' => $downloadLatencyMax,
                'downloadMinimum' => $downloadLatencyMin,
                'uploadLatest' => $uploadLatencyLatest,
                'uploadMaximum' => $uploadLatencyMax,
                'uploadMinimum' => $uploadLatencyMin,
                'tests' => count($results),
            ],
            'jitterStats' => [
                'downloadLatest' => $downloadJitterLatest,
                'downloadAverage' => $downloadJitterAvg,
                'downloadP95' => $downloadJitterP95,
                'downloadMaximum' => $downloadJitterMax,
                'downloadMinimum' => $downloadJitterMin,
                'uploadLatest' => $uploadJitterLatest,
                'uploadAverage' => $uploadJitterAvg,
                'uploadP95' => $uploadJitterP95,
                'uploadMaximum' => $uploadJitterMax,
                'uploadMinimum' => $uploadJitterMin,
                'pingLatest' => $pingJitterLatest,
                'pingAverage' => $pingJitterAvg,
                'pingP95' => $pingJitterP95,
                'pingMaximum' => $pingJitterMax,
                'pingMinimum' => $pingJitterMin,
                'tests' => count($results),
            ],
        ];
    }

    protected function calculatePercentile(array $data, int $percentile): float
    {
        if (count($data) === 0) {
            return 0;
        }

        sort($data);
        $index = (int) ceil(($percentile / 100) * count($data)) - 1;
        $index = max(0, min($index, count($data) - 1));

        return round($data[$index], 2);
    }

    public function render()
    {
        return view('livewire.metrics-dashboard', [
            'chartData' => $this->getChartData(),
        ]);
    }
}
