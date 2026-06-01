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
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('service')->state(fn (Result $r) => $r->service->getLabel()),
            ExportColumn::make('status')->state(fn (Result $r) => $r->status->getLabel()),
            ExportColumn::make('scheduled')->state(fn (Result $r) => $r->scheduled ? 'Yes' : 'No'),
            ExportColumn::make('healthy')->state(fn (Result $r) => $r->healthy ? 'Yes' : 'No'),
            ExportColumn::make('ping'),
            ExportColumn::make('ping_jitter'),
            ExportColumn::make('ping_low'),
            ExportColumn::make('ping_high'),
            ExportColumn::make('download'),
            ExportColumn::make('download_bytes'),
            ExportColumn::make('download_jitter'),
            ExportColumn::make('download_latency_iqm'),
            ExportColumn::make('download_latency_low'),
            ExportColumn::make('download_latency_high'),
            ExportColumn::make('download_elapsed'),
            ExportColumn::make('upload'),
            ExportColumn::make('upload_bytes'),
            ExportColumn::make('upload_jitter'),
            ExportColumn::make('upload_latency_iqm'),
            ExportColumn::make('upload_latency_low'),
            ExportColumn::make('upload_latency_high'),
            ExportColumn::make('upload_elapsed'),
            ExportColumn::make('packet_loss'),
            ExportColumn::make('isp'),
            ExportColumn::make('ip_address'),
            ExportColumn::make('server_id'),
            ExportColumn::make('server_name'),
            ExportColumn::make('server_host'),
            ExportColumn::make('server_ip'),
            ExportColumn::make('server_country'),
            ExportColumn::make('server_location'),
            ExportColumn::make('result_url'),
            ExportColumn::make('error_message'),
            ExportColumn::make('comments'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
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
