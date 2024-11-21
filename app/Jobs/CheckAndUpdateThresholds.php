<?php

namespace App\Jobs;

use App\Helpers\Number;
use App\Models\Result;
use App\Settings\ThresholdSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckAndUpdateThresholds
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $result;

    /**
     * Create a new job instance.
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $thresholds = app(ThresholdSettings::class);

        // Determine if thresholds are enabled
        $thresholdsEnabled = $thresholds->absolute_enabled;

        // Convert bits to Mbits if needed
        $downloadInMbits = ! is_null($this->result->download)
            ? Number::bitsToMagnitude($this->result->download_bits, 2, 'mbit')
            : null;
        $uploadInMbits = ! is_null($this->result->upload)
            ? Number::bitsToMagnitude($this->result->upload_bits, 2, 'mbit')
            : null;

        // Only check thresholds if they are enabled and not set to 0
        $downloadBreached = $thresholdsEnabled
            && $thresholds->absolute_download > 0
            && $downloadInMbits !== null
            && $downloadInMbits < $thresholds->absolute_download;

        $uploadBreached = $thresholdsEnabled
            && $thresholds->absolute_upload > 0
            && $uploadInMbits !== null
            && $uploadInMbits < $thresholds->absolute_upload;

        $pingBreached = $thresholdsEnabled
            && $thresholds->absolute_ping > 0
            && $this->result->ping !== null
            && $this->result->ping > $thresholds->absolute_ping;

        // Calculate individual statuses
        $downloadStatus = $thresholdsEnabled && $thresholds->absolute_download > 0
            ? ($downloadBreached ? 'Failed' : 'Passed')
            : 'NotChecked';

        $uploadStatus = $thresholdsEnabled && $thresholds->absolute_upload > 0
            ? ($uploadBreached ? 'Failed' : 'Passed')
            : 'NotChecked';

        $pingStatus = $thresholdsEnabled && $thresholds->absolute_ping > 0
            ? ($pingBreached ? 'Failed' : 'Passed')
            : 'NotChecked';

        // Calculate the overall status
        $overallStatus = $thresholdsEnabled
            ? ($downloadBreached || $uploadBreached || $pingBreached ? 'Failed' : 'Passed')
            : 'NotChecked';

        // Update all relevant fields in the database
        $this->result->update([
            'threshold_breached_overall' => $overallStatus,
            'threshold_breached_download' => $downloadStatus,
            'threshold_breached_upload' => $uploadStatus,
            'threshold_breached_ping' => $pingStatus,
        ]);
    }
}
