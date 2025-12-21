<?php

namespace App\Livewire;

use App\Enums\ResultStatus;
use App\Models\Result;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LatestResultStats extends Component
{
    #[Computed]
    public function latestResult(): ?Result
    {
        return Result::where('status', ResultStatus::Completed)
            ->latest()
            ->first();
    }

    public function render()
    {
        return view('livewire.latest-result-stats');
    }
}
