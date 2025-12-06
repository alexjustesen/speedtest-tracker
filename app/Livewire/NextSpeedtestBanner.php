<?php

namespace App\Livewire;

use App\Services\ScheduledSpeedtestService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NextSpeedtestBanner extends Component
{
    #[Computed]
    public function nextSpeedtest(): ?Carbon
    {
        return ScheduledSpeedtestService::getNextScheduledTest();
    }

    public function render()
    {
        return view('livewire.next-speedtest-banner');
    }
}
