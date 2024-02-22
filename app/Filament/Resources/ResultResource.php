<?php

namespace App\Filament\Resources;

use App\Actions\MigrateBadJsonResults;
use App\Enums\ResultStatus;
use App\Filament\Exports\ResultExporter;
use App\Filament\Resources\ResultResource\Pages;
use App\Helpers\Number;
use App\Helpers\TimeZoneHelper;
use App\Models\Result;
use App\Settings\DataMigrationSettings;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $settings = new GeneralSettings();

        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 2,
                    'md' => 3,
                ])->schema([
                    Forms\Components\Grid::make([
                        'default' => 2,
                        'md' => 3,
                    ])
                        ->schema([
                            Forms\Components\TextInput::make('id')
                                ->label('ID'),
                            Forms\Components\TextInput::make('created_at')
                                ->label('Created')
                                ->afterStateHydrated(function (TextInput $component, $state) use ($settings) {
                                    $component->state(Carbon::parse($state)->format($settings->time_format ?? 'M j, Y G:i:s'));
                                })
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('download')
                                ->label('Download')
                                ->afterStateHydrated(function (TextInput $component, Result $record) {
                                    $component->state(! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : '');
                                }),
                            Forms\Components\TextInput::make('upload')
                                ->label('Upload')
                                ->afterStateHydrated(function (TextInput $component, Result $record) {
                                    $component->state(! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : '');
                                }),
                            Forms\Components\TextInput::make('ping')
                                ->label('Ping (ms)'),
                            Forms\Components\TextInput::make('data.download.latency.jitter')
                                ->label('Download Jitter (ms)'),
                            Forms\Components\TextInput::make('data.upload.latency.jitter')
                                ->label('Upload Jitter (ms)'),
                            Forms\Components\TextInput::make('data.ping.jitter')
                                ->label('Ping Jitter (ms)'),
                            Forms\Components\Textarea::make('data.message')
                                ->label('Error Message')
                                ->hint(new HtmlString('&#x1f517;<a href="https://docs.speedtest-tracker.dev/help/error-messages" target="_blank" rel="nofollow">Error Messages</a>'))
                                ->hidden(fn (Result $record): bool => $record->status !== ResultStatus::Failed)
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(2),
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('service')
                                ->content(fn (Result $result): string => $result->service),
                            Forms\Components\Placeholder::make('server_name')
                                ->content(fn (Result $result): ?string => $result->server_name),
                            Forms\Components\Placeholder::make('server_id')
                                ->label('Server ID')
                                ->content(fn (Result $result): ?string => $result->server_id),
                            Forms\Components\Placeholder::make('server_host')
                                ->content(fn (Result $result): ?string => $result->server_host),
                            Forms\Components\Checkbox::make('scheduled'),
                        ])
                        ->columns(1)
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $dataSettings = new DataMigrationSettings();

        $settings = new GeneralSettings();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP address')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                Tables\Columns\TextColumn::make('server_id')
                    ->label('Server ID')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('server_name')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('download')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->download) ? Number::toBitRate(bits: $record->download_bits, precision: 2) : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('upload')
                    ->getStateUsing(fn (Result $record): ?string => ! blank($record->upload) ? Number::toBitRate(bits: $record->upload_bits, precision: 2) : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('ping')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('download_jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                Tables\Columns\TextColumn::make('upload_jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ping_jitter')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('scheduled')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime($settings->time_format ?? 'M j, Y G:i:s')
                    ->timezone(TimeZoneHelper::displayTimeZone($settings))
                    ->toggleable()
                    ->sortable()
                    ->alignment(Alignment::End),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime($settings->time_format ?? 'M j, Y G:i:s')
                    ->timezone(TimeZoneHelper::displayTimeZone($settings))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->alignment(Alignment::End),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ip_address')
                    ->label('IP address')
                    ->multiple()
                    ->options(function (): array {
                        return Result::query()
                            ->select('data->interface->externalIp AS public_ip_address')
                            ->whereNotNull('data->interface->externalIp')
                            ->where('status', '=', ResultStatus::Completed)
                            ->distinct()
                            ->get()
                            ->mapWithKeys(function (Result $item, int $key) {
                                return [$item['public_ip_address'] => $item['public_ip_address']];
                            })
                            ->toArray();
                    })
                    ->attribute('data->interface->externalIp'),
                Tables\Filters\TernaryFilter::make('scheduled')
                    ->placeholder('-')
                    ->trueLabel('Only scheduled speedtests')
                    ->falseLabel('Only manual speedtests')
                    ->queries(
                        true: fn (Builder $query) => $query->where('scheduled', true),
                        false: fn (Builder $query) => $query->where('scheduled', false),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options(ResultStatus::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('view result')
                        ->label('View on Speedtest.net')
                        ->icon('heroicon-o-link')
                        ->url(fn (Result $record): ?string => $record->result_url)
                        ->hidden(fn (Result $record): bool => $record->status !== ResultStatus::Completed)
                        ->openUrlInNewTab(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('updateComments')
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->hidden(fn (): bool => ! auth()->user()->is_admin && ! auth()->user()->is_user)
                        ->mountUsing(fn (Forms\ComponentContainer $form, Result $record) => $form->fill([
                            'comments' => $record->comments,
                        ]))
                        ->action(function (Result $record, array $data): void {
                            $record->comments = $data['comments'];
                            $record->save();
                        })
                        ->form([
                            Forms\Components\Textarea::make('comments')
                                ->rows(6)
                                ->maxLength(500),
                        ])
                        ->modalButton('Save'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(ResultExporter::class)
                    ->fileName(fn (): string => 'results-'.now()->timestamp),
                Tables\Actions\Action::make('migrate')
                    ->action(function (): void {
                        Notification::make()
                            ->title('Starting data migration...')
                            ->body('This can take a little bit depending how much data you have.')
                            ->warning()
                            ->sendToDatabase(Auth::user());

                        MigrateBadJsonResults::dispatch(Auth::user());
                    })
                    ->hidden($dataSettings->bad_json_migrated)
                    ->requiresConfirmation()
                    ->modalHeading('Migrate History')
                    ->modalDescription(new HtmlString('<p>v0.16.0 archived the old <code>"results"</code> table, to migrate your history click the button below.</p><p>For more information read the <a href="#" target="_blank" rel="nofollow" class="underline">docs</a>.</p>'))
                    ->modalSubmitActionLabel('Yes, migrate it'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 15, 25, 50, 100])
            ->defaultPaginationPageOption(15);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
        ];
    }
}
