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
            ExportColumn::make('service')
                ->state(function (Result $record) {
                    return $record->service->getLabel();
                }),
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
                })
                ->enabledByDefault(false),
            ExportColumn::make('upload_jitter')
                ->state(function (Result $record): ?string {
                    return $record->upload_jitter;
                })
                ->enabledByDefault(false),
            ExportColumn::make('ping_jitter')
                ->state(function (Result $record): ?string {
                    return $record->ping_jitter;
                })
                ->enabledByDefault(false),
            ExportColumn::make('upload_latency_high')
                ->state(function (Result $record): ?string {
                    return $record->upload_latency_high;
                })
                ->enabledByDefault(false),
            ExportColumn::make('upload_latency_low')
                ->state(function (Result $record): ?string {
                    return $record->upload_latency_low;
                })
                ->enabledByDefault(false),
            ExportColumn::make('upload_latency_avg')
                ->state(function (Result $record): ?string {
                    return $record->upload_latency_iqm;
                })
                ->enabledByDefault(false),
            ExportColumn::make('download_latency_high')
                ->state(function (Result $record): ?string {
                    return $record->download_latency_high;
                })
                ->enabledByDefault(false),
            ExportColumn::make('download_latency_low')
                ->state(function (Result $record): ?string {
                    return $record->download_latency_low;
                })
                ->enabledByDefault(false),
            ExportColumn::make('download_latency_avg')
                ->state(function (Result $record): ?string {
                    return $record->download_latency_iqm;
                })
                ->enabledByDefault(false),
            ExportColumn::make('downloaded_bytes')
                ->state(function (Result $record): ?string {
                    return $record->downloaded_bytes;
                })
                ->enabledByDefault(false),
            ExportColumn::make('uploaded_bytes')
                ->state(function (Result $record): ?string {
                    return $record->uploaded_bytes;
                })
                ->enabledByDefault(false),
            ExportColumn::make('result_url')
                ->state(function (Result $record) {
                    return $record->result_url;
                }),
            ExportColumn::make('comments')
                ->enabledByDefault(false),
            ExportColumn::make('status')
                ->state(function (Result $record) {
                    return $record->status->getLabel();
                }),
            ExportColumn::make('scheduled')
                ->state(function (Result $record): string {
                    return $record->scheduled ? 'Yes' : 'No';
                }),
            ExportColumn::make('healthy')
                ->state(function (Result $record): string {
                    return $record->healthy ? 'Yes' : 'No';
                })
                ->enabledByDefault(false),
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
