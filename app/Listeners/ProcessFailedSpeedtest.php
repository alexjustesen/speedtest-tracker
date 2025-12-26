<?php

namespace App\Listeners;

use App\Events\SpeedtestFailed;
use App\Models\Result;

class ProcessFailedSpeedtest
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestFailed $event): void
    {
        $result = $event->result;

        // Don't send notifications for unscheduled speedtests.
        if ($result->unscheduled) {
            return;
        }

        // $this->notifyAppriseChannels($result);
    }

    /**
     * Notify Apprise channels.
     */
    private function notifyAppriseChannels(Result $result): void
    {
        //
    }
}
