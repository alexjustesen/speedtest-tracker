<?php

namespace App\Livewire;

use App\Enums\ResultStatus;
use App\Models\Result;
use App\Services\DataUsageCalculator;
use Carbon\Carbon;
use Cron\CronExpression;
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
        $bandwidthLimit = null;
        $bandwidthUsed = DataUsageCalculator::calculate(now()->startOfMonth(), now());

        return [
            'total' => Number::format($totalResults),
            'completed' => Number::format($completedResults),
            'failed' => Number::format($failedResults),
            'bandwidth_limit' => $bandwidthLimit,
            'bandwidth_used' => [
                'download_bytes' => $bandwidthUsed['download_bytes'],
                'upload_bytes' => $bandwidthUsed['upload_bytes'],
                'total_bytes' => $bandwidthUsed['total_bytes'],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.platform-stats');
    }
}
