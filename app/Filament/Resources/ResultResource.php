<?php

namespace App\Filament\Resources;

use App\Enums\ResultStatus;
use App\Filament\Exports\ResultExporter;
use App\Filament\Resources\ResultResource\Pages;
use App\Helpers\Number;
use App\Jobs\TruncateResults;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public function getTitle(): string
    {
        return __('translations.results');
    }

    public static function getNavigationLabel(): string
    {
        return __('translations.results');
    }

    public static function getLabel(): string
    {
        return __('translations.results');
    }

    public static function getPluralLabel(): string
    {
        return __('translations.results');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(['default' => 2, 'md' => 3])->schema([
                Grid::make()->schema([
                    Section::make(__('translations.result_overview'))->schema([
                        TextInput::make('id')
                            ->label('ID'),
                        TextInput::make('created_at')
                            ->label(__('created_at'))
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                $component->state(Carbon::parse($state)
                                    ->timezone(app(GeneralSettings::class)->display_timezone)
                                    ->format(app(GeneralSettings::class)->datetime_format));
                            }),
                        TextInput::make('download')
                            ->label(__('translations.download'))
                            ->afterStateHydrated(fn ($component, Result $record) => $component->state(! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : '')),
                        TextInput::make('upload')
                            ->label(__('translations.upload'))
                            ->afterStateHydrated(fn ($component, Result $record) => $component->state(! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : '')),
                        TextInput::make('ping')
                            ->label(__('translations.ping'))
                            ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                        TextInput::make('data.packetLoss')
                            ->label(__('translations.packet_loss'))
                            ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', '').' %'),
                    ])->columns(2),

                    Section::make(__('translations.download_latency'))
                        ->schema([
                            TextInput::make('data.download.latency.jitter')
                                ->label(__('translations.jitter'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                            TextInput::make('data.download.latency.high')
                                ->label(__('translations.high'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                            TextInput::make('data.download.latency.low')
                                ->label(__('translations.low'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                            TextInput::make('data.download.latency.iqm')
                                ->label(__('translations.iqm'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                        ])
                        ->columns(2)
                        ->collapsed(),

                    Section::make(__('translations.upload_latency'))
                        ->schema([
                            TextInput::make('data.upload.latency.jitter')
                                ->label(__('translations.jitter'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                            TextInput::make('data.upload.latency.high')
                                ->label(__('translations.high'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                            TextInput::make('data.upload.latency.low')
                                ->label(__('translations.low'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                            TextInput::make('data.upload.latency.iqm')
                                ->label(__('translations.iqm'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                        ])
                        ->columns(2)
                        ->collapsed(),

                    Section::make(__('translations.ping_details'))
                        ->schema([
                            TextInput::make('data.ping.jitter')
                                ->label(__('translations.jitter'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                            TextInput::make('data.ping.high')
                                ->label(__('translations.high'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                            TextInput::make('data.ping.low')
                                ->label(__('translations.low'))
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', '').' '.__('translations.ms')),
                        ])
                        ->columns(2)
                        ->collapsed(),

                    Textarea::make('data.message')
                        ->label(__('translations.message'))
                        ->hint(new HtmlString('&#x1f517;<a href="https://docs.speedtest-tracker.dev/help/error-messages" target="_blank" rel="nofollow">'.__('translations.error_message').'</a>'))
                        ->columnSpanFull(),
                ])->columnSpan([
                    'default' => 2,
                    'md' => 2,
                ]),

                Section::make(__('translations.server_&_metadata'))->schema([
                    Placeholder::make('service')
                        ->label(__('translations.service'))
                        ->content(fn (Result $result): string => $result->service->getLabel()),
                    Placeholder::make('server_name')
                        ->label(__('translations.server_name'))
                        ->content(fn (Result $result): ?string => $result->server_name),
                    Placeholder::make('server_id')
                        ->label(__('translations.server_id'))
                        ->content(fn (Result $result): ?string => $result->server_id),
                    Placeholder::make('isp')
                        ->label(__('translations.isp'))
                        ->content(fn (Result $result): ?string => $result->isp),
                    Placeholder::make('server_location')
                        ->label(__('translations.server_location'))
                        ->content(fn (Result $result): ?string => $result->server_location),
                    Placeholder::make('server_host')
                        ->label(__('translations.server_host'))
                        ->content(fn (Result $result): ?string => $result->server_host),
                    Placeholder::make('comment')
                        ->label(__('translations.comment'))
                        ->content(fn (Result $result): ?string => $result->comments),
                    Checkbox::make('scheduled')
                        ->label(__('translations.scheduled')),
                    Checkbox::make('healthy')
                        ->label(__('translations.healthy')),
                ])->columns(1)->columnSpan([
                    'default' => 2,
                    'md' => 1,
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('data.interface.externalIp')
                    ->label(__('translations.ip_address'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->interface->externalIp', $direction);
                    }),
                TextColumn::make('service')
                    ->label(__('translations.service'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                TextColumn::make('data.server.id')
                    ->label(__('translations.server_id'))
                    ->toggleable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->id', $direction);
                    }),
                TextColumn::make('data.isp')
                    ->label(__('translations.isp'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->isp', $direction);
                    }),
                TextColumn::make('data.server.location')
                    ->label(__('translations.server_location'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->location', $direction);
                    }),
                TextColumn::make('data.server.name')
                    ->label(__('translations.server_name'))
                    ->toggleable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->server->name', $direction);
                    }),
                TextColumn::make('download')
                    ->label(__('translations.download'))
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : null)
                    ->sortable(),
                TextColumn::make('upload')
                    ->label(__('translations.upload'))
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : null)
                    ->sortable(),
                TextColumn::make('ping')
                    ->label(__('translations.ping'))
                    ->toggleable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.download.latency.jitter')
                    ->label(__('translations.download_latency_jitter'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.download.latency.high')
                    ->label(__('translations.download_latency_high'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.download.latency.low')
                    ->label(__('translations.download_latency_low'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.download.latency.iqm')
                    ->label(__('translations.download_latency_iqm'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->download->latency->iqm', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.upload.latency.jitter')
                    ->label(__('translations.upload_latency_jitter'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.upload.latency.high')
                    ->label(__('translations.upload_latency_high'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.upload.latency.low')
                    ->label(__('translations.upload_latency_low'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.upload.latency.iqm')
                    ->label(__('translations.upload_latency_iqm'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->upload->latency->iqm', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.ping.jitter')
                    ->label(__('translations.ping_jitter'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->ping->jitter', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.ping.low')
                    ->label(__('translations.ping_low'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->ping->low', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('data.ping.high')
                    ->label(__('translations.ping_high'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->ping->high', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 0, '.', '').' '.__('translations.ms');
                    }),
                TextColumn::make('packet_loss')
                    ->label(__('translations.packet_loss'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('data->packetLoss', $direction);
                    })
                    ->formatStateUsing(function ($state) {
                        return number_format((float) $state, 2, '.', '').' %';
                    }),
                TextColumn::make('status')
                    ->label(__('translations.status'))
                    ->badge()
                    ->toggleable()
                    ->sortable(),
                IconColumn::make('scheduled')
                    ->label(__('translations.scheduled'))
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->alignment(Alignment::Center),
                IconColumn::make('healthy')
                    ->label(__('translations.healthy'))
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignment(Alignment::Center),
                TextColumn::make('data.message')
                    ->label(__('translations.error_message'))
                    ->limit(15)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('translations.created_at'))
                    ->dateTime(app(GeneralSettings::class)->datetime_format)
                    ->timezone(app(GeneralSettings::class)->display_timezone)
                    ->toggleable()
                    ->sortable()
                    ->alignment(Alignment::End),
                TextColumn::make('updated_at')
                    ->label(__('translations.updated_at'))
                    ->dateTime(app(GeneralSettings::class)->datetime_format)
                    ->timezone(app(GeneralSettings::class)->display_timezone)
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignment(Alignment::End),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label(__('translations.created_at'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('translations.created_from')),
                        DatePicker::make('created_until')
                            ->label(__('translations.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('ip_address')
                    ->label(__('translations.ip_address'))
                    ->multiple()
                    ->options(function (): array {
                        return Result::query()
                            ->select('data->interface->externalIp AS public_ip_address')
                            ->whereNotNull('data->interface->externalIp')
                            ->where('status', '=', ResultStatus::Completed)
                            ->distinct()
                            ->orderBy('data->interface->externalIp')
                            ->get()
                            ->mapWithKeys(function (Result $item, int $key) {
                                return [$item['public_ip_address'] => $item['public_ip_address']];
                            })
                            ->toArray();
                    })
                    ->attribute('data->interface->externalIp'),
                SelectFilter::make('server_name')
                    ->label(__('translations.server_name'))
                    ->multiple()
                    ->options(function (): array {
                        return Result::query()
                            ->select('data->server->name AS data_server_name')
                            ->whereNotNull('data->server->name')
                            ->where('status', '=', ResultStatus::Completed)
                            ->distinct()
                            ->orderBy('data->server->name')
                            ->get()
                            ->mapWithKeys(function (Result $item, int $key) {
                                return [$item['data_server_name'] => $item['data_server_name']];
                            })
                            ->toArray();
                    })
                    ->attribute('data->server->name'),
                TernaryFilter::make('scheduled')
                    ->label(__('translations.scheduled'))
                    ->nullable()
                    ->trueLabel(__('translations.only_scheduled_speedtests'))
                    ->falseLabel(__('translations.only_manual_speedtests'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('scheduled', true),
                        false: fn (Builder $query) => $query->where('scheduled', false),
                        blank: fn (Builder $query) => $query,
                    ),
                SelectFilter::make('status')
                    ->label(__('translations.status'))
                    ->multiple()
                    ->options(ResultStatus::class),
                TernaryFilter::make('healthy')
                    ->label(__('translations.healthy'))
                    ->nullable()
                    ->trueLabel(__('translations.only_healthy_speedtests'))
                    ->falseLabel(__('translations.only_unhealthy_speedtests'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('healthy', true),
                        false: fn (Builder $query) => $query->where('healthy', false),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('view result')
                        ->label(__('translations.view_on_speedtest_net'))
                        ->icon('heroicon-o-link')
                        ->url(fn (Result $record): ?string => $record->result_url)
                        ->hidden(fn (Result $record): bool => $record->status !== ResultStatus::Completed)
                        ->openUrlInNewTab(),
                    ViewAction::make(),
                    Action::make('updateComments')
                        ->label(__('translations.update_comments'))
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->hidden(fn (): bool => ! (Auth::user()?->is_admin ?? false) && ! (Auth::user()?->is_user ?? false))
                        ->mountUsing(fn (Forms\ComponentContainer $form, Result $record) => $form->fill([
                            'comments' => $record->comments,
                        ]))
                        ->action(function (Result $record, array $data): void {
                            $record->comments = $data['comments'];
                            $record->save();
                        })
                        ->form([
                            Textarea::make('comments')
                                ->label(__('translations.comments'))
                                ->rows(6)
                                ->maxLength(500),
                        ]),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ResultExporter::class)
                    ->columnMapping(false)
                    ->modalHeading(__('translations.export_all_results'))
                    ->modalDescription(__('translations.export_all_results_description'))
                    ->fileName(fn (): string => 'results-'.now()->timestamp),
                ActionGroup::make([
                    Action::make('truncate')
                        ->label(__('translations.truncate'))
                        ->action(fn () => TruncateResults::dispatch(Auth::user()))
                        ->requiresConfirmation()
                        ->modalHeading(__('translations.truncate_results'))
                        ->modalDescription(__('translations.truncate_results_description'))
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->hidden(fn (): bool => ! Auth::user()->is_admin),
                ])->dropdownPlacement('bottom-end'),
            ])
            ->defaultSort('id', 'desc')
            ->deferLoading()
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
        ];
    }
}
