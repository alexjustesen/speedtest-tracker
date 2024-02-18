<?php

namespace App\Console\Commands;

use App\Models\JobTracking;
use App\Models\JobTrackingStatusEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearJobTrackingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-job-tracking
                            {age? : Maximum age of the tracking records, default 30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears completed speedtest job trackings older than x Days (default 30)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $max_age = 30;
        $argument = $this->argument('age');

        if ($argument && intval($argument) != 0) {
            $max_age = intval($argument);
        }

        JobTracking::where('created_at', '<', Carbon::now()->subDays($max_age))
            ->orWhere('status', JobTrackingStatusEnum::Failed)
            ->delete();
    }
}
