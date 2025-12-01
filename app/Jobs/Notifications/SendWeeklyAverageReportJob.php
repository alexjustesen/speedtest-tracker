<?php

namespace App\Jobs\Notifications;

use App\Enums\ResultStatus;
use App\Mail\PeriodicAverageMail;
use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendWeeklyAverageReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(NotificationSettings $settings): void
    {
        if (! $settings->mail_enabled || ! $settings->mail_weekly_average_enabled) {
            return;
        }

        if (empty($settings->mail_recipients)) {
            return;
        }

        $startOfWeek = now()->subWeek()->startOfWeek();
        $endOfWeek = now()->subWeek()->endOfWeek();

        $results = Result::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
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

        $period = 'Weekly';
        $periodLabel = $startOfWeek->format('M j').' - '.$endOfWeek->format('M j, Y');

        foreach ($settings->mail_recipients as $recipient) {
            Mail::to($recipient)->queue(
                new PeriodicAverageMail($stats, $period, $periodLabel)
            );
        }
    }
}
