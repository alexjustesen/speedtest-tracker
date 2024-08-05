<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Models\Result;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    public ?Result $result = null;

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getCards(): array
    {
        $this->result = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'status', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->latest()
            ->first();

        if (blank($this->result)) {
            return [
                Stat::make('Latest download', '-')
                    ->icon('heroicon-o-arrow-down-tray'),
                Stat::make('Latest upload', '-')
                    ->icon('heroicon-o-arrow-up-tray'),
                Stat::make('Latest ping', '-')
                    ->icon('heroicon-o-clock'),
            ];
        }

        $previous = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'status', 'created_at'])
            ->where('id', '<', $this->result->id)
            ->where('status', '=', ResultStatus::Completed)
            ->latest()
            ->first();

        $downloadChange = percentChange($this->result->download, $previous->download, 2);
        $uploadChange = percentChange($this->result->upload, $previous->upload, 2);
        $pingChange = percentChange($this->result->ping, $previous->ping, 2);

        $last24hData = Result::query()
            ->select(['download', 'upload', 'ping'])
            ->where('status', '=', ResultStatus::Completed)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->get();

        $averageDownload24h = $last24hData->avg('download');
        $averageUpload24h = $last24hData->avg('upload');
        $averagePing24h = $last24hData->avg('ping');

        $last7dData = Result::query()
            ->select(['download', 'upload', 'ping'])
            ->where('status', '=', ResultStatus::Completed)
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->get();

        $averageDownload7d = $last7dData->avg('download');
        $averageUpload7d = $last7dData->avg('upload');
        $averagePing7d = $last7dData->avg('ping');

        $last1mData = Result::query()
            ->select(['download', 'upload', 'ping'])
            ->where('status', '=', ResultStatus::Completed)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->get();

        $averageDownload1m = $last1mData->avg('download');
        $averageUpload1m = $last1mData->avg('upload');
        $averagePing1m = $last1mData->avg('ping');

        return [
            Stat::make('Latest download', fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->download_bits, precision: 2) : 'n/a')
                ->icon('heroicon-o-arrow-down-tray')
                ->description($downloadChange > 0 ? $downloadChange.'% faster' : abs($downloadChange).'% slower')
                ->descriptionIcon($downloadChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($downloadChange > 0 ? 'success' : 'danger'),
            Stat::make('Latest upload', fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->upload_bits, precision: 2) : 'n/a')
                ->icon('heroicon-o-arrow-up-tray')
                ->description($uploadChange > 0 ? $uploadChange.'% faster' : abs($uploadChange).'% slower')
                ->descriptionIcon($uploadChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($uploadChange > 0 ? 'success' : 'danger'),
            Stat::make('Latest ping', fn (): string => ! blank($this->result) ? number_format($this->result->ping, 2).' ms' : 'n/a')
                ->icon('heroicon-o-clock')
                ->description($pingChange > 0 ? $pingChange.'% slower' : abs($pingChange).'% faster')
                ->descriptionIcon($pingChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pingChange > 0 ? 'danger' : 'success'),

            Stat::make('Average Download', '')
                ->icon('heroicon-o-arrow-down-tray')
                ->description(
                    '24 Hours: '.(blank($averageDownload24h) ? 'n/a' : Number::toBitRate(bits: $averageDownload24h * 8, precision: 2))."\n".
                    '7 Days: '.(blank($averageDownload7d) ? 'n/a' : Number::toBitRate(bits: $averageDownload7d * 8, precision: 2))."\n".
                    '1 Month: '.(blank($averageDownload1m) ? 'n/a' : Number::toBitRate(bits: $averageDownload1m * 8, precision: 2))
                ),
            Stat::make('Average Upload', '')
                ->icon('heroicon-o-arrow-down-tray')
                ->description(
                    '24 Hours: '.(blank($averageUpload24h) ? 'n/a' : Number::toBitRate(bits: $averageUpload24h * 8, precision: 2))."\n".
                    '7 Days: '.(blank($averageUpload7d) ? 'n/a' : Number::toBitRate(bits: $averageUpload7d * 8, precision: 2))."\n".
                    '1 Month: '.(blank($averageUpload1m) ? 'n/a' : Number::toBitRate(bits: $averageUpload1m * 8, precision: 2))
                ),
            Stat::make('Average Ping', '')
                ->icon('heroicon-o-arrow-down-tray')
                ->description(
                    '24 Hours: '.(blank($averagePing24h) ? 'n/a' : number_format($averagePing24h, 2).' ms')."\n".
                    '7 Days: '.(blank($averagePing7d) ? 'n/a' : number_format($averagePing7d, 2).' ms')."\n".
                    '1 Month: '.(blank($averagePing1m) ? 'n/a' : number_format($averagePing1m, 2).' ms')
                ),
        ];
    }
}
