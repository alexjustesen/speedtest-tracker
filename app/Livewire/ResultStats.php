<?php

namespace App\Livewire;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ResultStats extends Component
{

    public function render()
    {
        $metrics = Result::query()
            ->select([
                DB::raw('AVG(download) as average_download'),
                DB::raw('AVG(upload) as average_upload'),
                DB::raw('AVG(ping) as average_ping'),
            ])
            ->where('status', '=', ResultStatus::Completed)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->first();

        $totals = Result::select([
            DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_count'),
            DB::raw('COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_count'),
            DB::raw('COUNT(*) as total_count')
        ])->first();

        return view('livewire.result-stats', [
            'totals' => $totals,
            'avgDownload' => Number::toBitRate($metrics['average_download'], 2),
            'avgUpload' => Number::toBitRate($metrics['average_upload'], 2),
            'avgPing' => round($metrics['average_ping'], 2),
            'successRate' => Number::calculatePercentage($totals['completed_count'], $totals['total_count'], formatOutput: true)
        ]);
    }
}
