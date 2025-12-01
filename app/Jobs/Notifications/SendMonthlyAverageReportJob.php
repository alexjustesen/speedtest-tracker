<?php

namespace App\Jobs\Notifications;

use App\Enums\ResultStatus;
use App\Mail\PeriodicAverageMail;
use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendMonthlyAverageReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(NotificationSettings $settings): void
    {
        if (! $settings->mail_enabled || ! $settings->mail_monthly_average_enabled) {
            return;
        }

        if (empty($settings->mail_recipients)) {
            return;
        }

        $startOfMonth = now()->subMonth()->startOfMonth();
        $endOfMonth = now()->subMonth()->endOfMonth();

        $results = Result::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        if ($results->isEmpty()) {
            return;
        }

        $stats = [
            'download_avg' => $results->avg('download'),
            'upload_avg' => $results->avg('upload'),
            'ping_avg' => round($results->avg('ping'), 2),
            'total_tests' => $results->count(),
            'successful_tests' => $results->where('status', ResultStatus::Completed)->count(),
            'failed_tests' => $results->where('status', ResultStatus::Failed)->count(),
            'healthy_tests' => $results->where('healthy', '===', true)->count(),
            'unhealthy_tests' => $results->where('healthy', '===', false)->count(),
        ];

        // Calculate per-server averages (only completed tests)
        $serverStats = $results
            ->where('status', '===', ResultStatus::Completed)
            ->groupBy('server_name')
            ->map(function ($serverResults) {
                return [
                    'server_name' => $serverResults->first()->server_name ?? 'Unknown',
                    'count' => $serverResults->count(),
                    'download_avg' => $serverResults->avg('download'),
                    'upload_avg' => $serverResults->avg('upload'),
                    'ping_avg' => round($serverResults->avg('ping'), 2),
                ];
            })
            ->values()
            ->sortByDesc('count');

        $period = 'Monthly';
        $periodLabel = $startOfMonth->format('F Y');

        foreach ($settings->mail_recipients as $recipient) {
            Mail::to($recipient)->queue(
                new PeriodicAverageMail($stats, $period, $periodLabel, $serverStats)
            );
        }
    }
}
