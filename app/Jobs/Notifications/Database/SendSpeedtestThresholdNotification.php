<?php

namespace App\Jobs\Notifications\Database;

use App\Helpers\Number;
use App\Models\Result;
use App\Models\User;
use App\Settings\ThresholdSettings;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSpeedtestThresholdNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public Result $result;

    /**
     * Create a new job instance.
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Handle the job.
     */
    public function handle(): void
    {
        $thresholdSettings = new ThresholdSettings;

        if (! $thresholdSettings->absolute_enabled) {
            return;
        }

        if ($thresholdSettings->absolute_download > 0) {
            $this->absoluteDownloadThreshold($thresholdSettings);
        }

        if ($thresholdSettings->absolute_upload > 0) {
            $this->absoluteUploadThreshold($thresholdSettings);
        }

        if ($thresholdSettings->absolute_ping > 0) {
            $this->absolutePingThreshold($thresholdSettings);
        }
    }

    protected function absoluteDownloadThreshold(ThresholdSettings $thresholdSettings): void
    {
        if (! absoluteDownloadThresholdFailed($thresholdSettings->absolute_download, $this->result->download)) {

            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Download threshold breached!')
                ->body('Speedtest #'.$this->result->id.' breached the download threshold of '.$thresholdSettings->absolute_download.' Mbps at '.Number::toBitRate($this->result->download_bits).'.')
                ->warning()
                ->sendToDatabase($user);
        }
    }

    protected function absoluteUploadThreshold(ThresholdSettings $thresholdSettings): void
    {
        if (! absoluteUploadThresholdFailed($thresholdSettings->absolute_upload, $this->result->upload)) {

            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Upload threshold breached!')
                ->body('Speedtest #'.$this->result->id.' breached the upload threshold of '.$thresholdSettings->absolute_upload.' Mbps at '.Number::toBitRate($this->result->upload_bits).'.')
                ->warning()
                ->sendToDatabase($user);
        }
    }

    protected function absolutePingThreshold(ThresholdSettings $thresholdSettings): void
    {
        if (! absolutePingThresholdFailed($thresholdSettings->absolute_ping, $this->result->ping)) {

            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Ping threshold breached!')
                ->body('Speedtest #'.$this->result->id.' breached the ping threshold of '.$thresholdSettings->absolute_ping.'ms at '.$this->result->ping.'ms.')
                ->warning()
                ->sendToDatabase($user);
        }
    }
}
