<?php

namespace App\Livewire;

use App\Enums\ResultStatus;
use App\Helpers\Average;
use App\Helpers\Bitrate;
use App\Models\Result;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class AverageStats extends Component
{
    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    #[On('date-range-updated')]
    public function updateDateRange(array $data): void
    {
        $this->dateFrom = $data['dateFrom'];
        $this->dateTo = $data['dateTo'];

        unset($this->results);
    }

    #[Computed]
    public function results()
    {
        return Result::query()
            ->where('status', ResultStatus::Completed)
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->get();
    }

    #[Computed]
    public function averageDownload(): float
    {
        return Average::averageDownload($this->results);
    }

    #[Computed]
    public function averageUpload(): float
    {
        return Average::averageUpload($this->results);
    }

    #[Computed]
    public function averagePing(): float
    {
        return Average::averagePing($this->results);
    }

    #[Computed]
    public function formattedDownload(): array
    {
        $downloadBits = $this->averageDownload * 1000000;
        $download = Bitrate::formatBits($downloadBits);

        return explode(' ', $download);
    }

    #[Computed]
    public function formattedUpload(): array
    {
        $uploadBits = $this->averageUpload * 1000000;
        $upload = Bitrate::formatBits($uploadBits);

        return explode(' ', $upload);
    }

    public function render()
    {
        return view('livewire.average-stats');
    }
}
