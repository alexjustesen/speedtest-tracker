<?php

namespace App\Livewire;

use App\Enums\ResultStatus;
use App\Models\Result;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PlatformStats extends Component
{
    #[Computed]
    public function platformStats(): array
    {
        $totalResults = Result::count();
        $completedResults = Result::where('status', ResultStatus::Completed)->count();
        $failedResults = Result::where('status', ResultStatus::Failed)->count();

        return [
            'total' => Number::format($totalResults),
            'completed' => Number::format($completedResults),
            'failed' => Number::format($failedResults),
        ];
    }

    public function render()
    {
        return view('livewire.platform-stats');
    }
}
