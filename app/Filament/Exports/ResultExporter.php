<?php

namespace App\Filament\Exports;

use App\Models\Result;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ResultExporter extends Exporter
{
    protected static ?string $model = Result::class;

    public function getFormats(): array
    {
        return [
            ExportFormat::Csv,
        ];
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('ip_address')
                ->label('IP address')
                ->state(function (Result $record): ?string {
                    return $record->ip_address;
                })
                ->enabledByDefault(false),
            ExportColumn::make('isp')
                ->label('ISP')
                ->state(function (Result $record): ?string {
                    return $record->isp;
                })
                ->enabledByDefault(false),
            ExportColumn::make('server_location')
                ->label('Server Location')
                ->state(function (Result $record): ?string {
                    return $record->server_location;
                })
                ->enabledByDefault(false),
            ExportColumn::make('service'),
            ExportColumn::make('server_id')
                ->label('Server ID')
                ->state(function (Result $record): ?string {
                    return $record->server_id;
                })
                ->enabledByDefault(false),
            ExportColumn::make('server_name')
                ->state(function (Result $record): ?string {
                    return $record->server_name;
                })
                ->enabledByDefault(false),

            ExportColumn::make('download')
                ->state(function (Result $record): ?string {
                    return $record->download_bits;
                }),
            ExportColumn::make('upload')
                ->state(function (Result $record): ?string {
                    return $record->upload_bits;
                }),
            ExportColumn::make('ping'),
            ExportColumn::make('packet_loss'),
            ExportColumn::make('download_jitter')
                ->state(function (Result $record): ?string {
                    return $record->download_jitter;
                }),
            ExportColumn::make('upload_jitter')
                ->state(function (Result $record): ?string {
                    return $record->upload_jitter;
                }),
            ExportColumn::make('ping_jitter')
                ->state(function (Result $record): ?string {
                    return $record->ping_jitter;
                }),
            ExportColumn::make('upload_latency_high')
                ->state(function (Result $record): ?string {
                    return $record->upload_latency_high;
                }),
            ExportColumn::make('upload_latency_low')
                ->state(function (Result $record): ?string {
                    return $record->upload_latency_low;
                }),
            ExportColumn::make('upload_latency_avg')
                ->state(function (Result $record): ?string {
                    return $record->upload_latency_iqm;
                }),
            ExportColumn::make('download_latency_high')
                ->state(function (Result $record): ?string {
                    return $record->download_latency_high;
                }),
            ExportColumn::make('download_latency_low')
                ->state(function (Result $record): ?string {
                    return $record->download_latency_low;
                }),
            ExportColumn::make('download_latency_avg')
                ->state(function (Result $record): ?string {
                    return $record->download_latency_iqm;
                }),
            ExportColumn::make('comments')
                ->enabledByDefault(false),
            // ExportColumn::make('status'), // TODO: enable status when upgrading to PHP v8.3: https://php.watch/versions/8.3/dynamic-class-const-enum-member-syntax-support
            ExportColumn::make('scheduled')
                ->state(function (Result $record): string {
                    return $record->scheduled ? 'Yes' : 'No';
                }),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your result export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
