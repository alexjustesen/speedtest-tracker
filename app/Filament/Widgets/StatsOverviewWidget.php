<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    public ?Result $result = null;

    protected static ?string $pollingInterval = '60s';

    protected function getCards(): array
    {
        $this->result = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'status', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->latest()
            ->first();

        if (blank($this->result)) {
            return [
                Stat::make(__('translations.latest_download'), '-')
                    ->icon('heroicon-o-arrow-down-tray'),
                Stat::make(__('translations.latest_upload'), '-')
                    ->icon('heroicon-o-arrow-up-tray'),
                Stat::make(__('translations.latest_ping'), '-')
                    ->icon('heroicon-o-clock'),
            ];
        }

        $previous = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'status', 'created_at'])
            ->where('id', '<', $this->result->id)
            ->where('status', '=', ResultStatus::Completed)
            ->latest()
            ->first();

        if (! $previous) {
            return [
                Stat::make(__('translations.latest_download'), fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->download_bits, precision: 2) : 'n/a')
                    ->icon('heroicon-o-arrow-down-tray'),
                Stat::make(__('translations.latest_upload'), fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->upload_bits, precision: 2) : 'n/a')
                    ->icon('heroicon-o-arrow-up-tray'),
                Stat::make(__('translations.latest_ping'), fn (): string => ! blank($this->result) ? number_format($this->result->ping, 2).' ms' : 'n/a')
                    ->icon('heroicon-o-clock'),
            ];
        }

        $downloadChange = percentChange($this->result->download, $previous->download, 2);
        $uploadChange = percentChange($this->result->upload, $previous->upload, 2);
        $pingChange = percentChange($this->result->ping, $previous->ping, 2);

        return [
            Stat::make(__('translations.latest_download'), fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->download_bits, precision: 2) : 'n/a')
                ->icon('heroicon-o-arrow-down-tray')
                ->description($downloadChange > 0 ? $downloadChange.'% '.__('translations.faster') : abs($downloadChange).'% '.__('translations.slower'))
                ->descriptionIcon($downloadChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($downloadChange > 0 ? 'success' : 'danger'),
            Stat::make(__('translations.latest_upload'), fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->upload_bits, precision: 2) : 'n/a')
                ->icon('heroicon-o-arrow-up-tray')
                ->description($uploadChange > 0 ? $uploadChange.'% '.__('translations.faster') : abs($uploadChange).'% '.__('translations.slower'))
                ->descriptionIcon($uploadChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($uploadChange > 0 ? 'success' : 'danger'),
            Stat::make(__('translations.latest_ping'), fn (): string => ! blank($this->result) ? number_format($this->result->ping, 2).' ms' : 'n/a')
                ->icon('heroicon-o-clock')
                ->description($pingChange > 0 ? $pingChange.'% '.__('translations.slower') : abs($pingChange).'% '.__('translations.faster'))
                ->descriptionIcon($pingChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pingChange > 0 ? 'danger' : 'success'),
        ];
    }
}
