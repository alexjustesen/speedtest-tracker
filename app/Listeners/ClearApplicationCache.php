<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelSettings\Events\SettingsSaved;

class ClearApplicationCache implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SettingsSaved $event): void
    {
        Log::info('Settings saved, clearing application cache.');

        Cache::flush();
    }
}
