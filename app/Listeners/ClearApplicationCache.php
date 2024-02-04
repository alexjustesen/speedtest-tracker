<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelSettings\Events\SettingsSaved;

class ClearApplicationCache implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SettingsSaved $event): void
    {
        Cache::flush();
    }
}
