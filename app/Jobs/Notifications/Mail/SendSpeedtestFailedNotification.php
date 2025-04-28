<?php

namespace App\Jobs\Notifications\Mail;

use App\Mail\SpeedtestFailedMail;
use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSpeedtestFailedNotification implements ShouldQueue
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
        $notificationSettings = new NotificationSettings;

        if (! count($notificationSettings->mail_recipients)) {
            Log::warning('Mail recipients not found, check mail notification channel settings.');

            return;
        }

        foreach ($notificationSettings->mail_recipients as $recipient) {
            Mail::to($recipient)
                ->send(new SpeedtestFailedMail($this->result));
        }
    }
}
