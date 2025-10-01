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
        $columns = [
            ExportColumn::make('id')
                ->label(__('translations.id')),
            ExportColumn::make('service')->state(fn (Result $r) => $r->service->getLabel()),
            ExportColumn::make('status')->state(fn (Result $r) => $r->status->getLabel()),
            ExportColumn::make('scheduled')->state(fn (Result $r) => $r->scheduled ? __('translations.yes') : __('translations.no')),
            ExportColumn::make('healthy')->state(fn (Result $r) => $r->healthy ? __('translations.yes') : __('translations.no')),
            ExportColumn::make('created_at')->label(__('translations.created_at')),
            ExportColumn::make('updated_at')->label(__('translations.updated_at')),
            ExportColumn::make('comments')->label(__('translations.comments')),
        ];

        $columns = array_merge($columns, self::generateDataColumns());

        return $columns;
    }

    protected static function generateDataColumns(): array
    {

        $sample = Result::query()->whereNotNull('data')->first()?->data ?? [];

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
        $body = __('translations.export_completed', [
            'count' => number_format($export->successful_rows),
            'rows' => trans_choice('translations.row', $export->successful_rows, [
                'count' => number_format($export->successful_rows),
            ]),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= __('translations.failed_export', [
                'count' => $failedRowsCount,
                'rows' => trans_choice('translations.row', $export->$failedRowsCount, [
                    'count' => number_format($export->$failedRowsCount),
                ]),
            ]);
        }

        return $body;
    }
}
