<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $result = Result::latest()->first();

        $settings = new GeneralSettings();

        if (! $result || ! $result->successful) {
            return [
                Card::make('Latest download', '-')
                    ->icon('heroicon-o-download'),
                Card::make('Latest upload', '-')
                    ->icon('heroicon-o-upload'),
                Card::make('Latest ping', '-')
                    ->icon('heroicon-o-clock'),
            ];
        }

        $previous = $result->previous();

        if (! $previous || ! $previous->successful) {
            return [
                Card::make('Latest download', fn (): string => ! blank($result) ? toBits(convertSize($result->download), 2).' (Mbps)' : 'n/a')
                    ->icon('heroicon-o-download'),
                Card::make('Latest upload', fn (): string => ! blank($result) ? toBits(convertSize($result->upload), 2).' (Mbps)' : 'n/a')
                    ->icon('heroicon-o-upload'),
                Card::make('Latest ping', fn (): string => ! blank($result) ? number_format($result->ping, 2).' (ms)' : 'n/a')
                    ->icon('heroicon-o-clock'),
            ];
        }

        $downloadChange = percentChange($result->download, $previous->download, 2);
        $uploadChange = percentChange($result->upload, $previous->upload, 2);
        $pingChange = percentChange($result->ping, $previous->ping, 2);

        return [
            Card::make('Latest download', fn (): string => ! blank($result) ? toBits(convertSize($result->download), 2).' (Mbps)' : 'n/a')
                ->icon('heroicon-o-download')
                ->description($downloadChange > 0 ? $downloadChange.'% faster' : abs($downloadChange).'% slower')
                ->descriptionIcon($downloadChange > 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->color($downloadChange > 0 ? 'success' : 'danger'),
            Card::make('Latest upload', fn (): string => ! blank($result) ? toBits(convertSize($result->upload), 2).' (Mbps)' : 'n/a')
                ->icon('heroicon-o-upload')
                ->description($uploadChange > 0 ? $uploadChange.'% faster' : abs($uploadChange).'% slower')
                ->descriptionIcon($uploadChange > 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->color($uploadChange > 0 ? 'success' : 'danger'),
            Card::make('Latest ping', fn (): string => ! blank($result) ? number_format($result->ping, 2).' (ms)' : 'n/a')
                ->icon('heroicon-o-clock')
                ->description($pingChange > 0 ? $pingChange.'% slower' : abs($pingChange).'% faster')
                ->descriptionIcon($pingChange > 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->color($pingChange > 0 ? 'danger' : 'success'),
        ];
    }
}
