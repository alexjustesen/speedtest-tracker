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
        // Basic known columns
        $columns = [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('service')->state(fn (Result $r) => $r->service->getLabel()),
            ExportColumn::make('status')->state(fn (Result $r) => $r->status->getLabel()),
            ExportColumn::make('scheduled')->state(fn (Result $r) => $r->scheduled ? 'Yes' : 'No'),
            ExportColumn::make('healthy')->state(fn (Result $r) => $r->healthy ? 'Yes' : 'No'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('comments'),
        ];

        // Append all flattened `data.*` keys
        $columns = array_merge($columns, self::generateDataColumns());

        return $columns;
    }

    protected static function generateDataColumns(): array
    {
        // Get first Result with usable `data`
        $sample = Result::query()->whereNotNull('data')->first()?->data ?? [];

        // Flatten without the "data_" prefix
        $flattened = self::flatten($sample);

        $columns = [];

        foreach ($flattened as $key => $default) {
            $columns[] = ExportColumn::make($key)
                ->label(str_replace('_', ' ', ucfirst($key)))
                ->state(function (Result $r) use ($key) {
                    $flattened = self::flatten($r->data ?? []);

                    return $flattened[$key] ?? null;
                });
        }

        return $columns;
    }

    protected static function flatten(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}_{$key}" : $key;

            if (is_array($value)) {
                $result += self::flatten($value, $newKey);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your result export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failed = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failed).' '.str('row')->plural($failed).' failed to export.';
        }

        return $body;
    }
}
