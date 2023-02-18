<?php

namespace App\Filament\Resources;

use App\Exports\ResultsSelectedBulkExport;
use App\Filament\Resources\ResultResource\Pages;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-table';

    protected static ?string $navigationLabel = 'Results';

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
                            Forms\Components\TextInput::make('server_id')
                                ->label('Server ID'),
                            Forms\Components\TextInput::make('server_name')
                                ->label('Server name')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('server_host')
                                ->label('Server host')
                                ->columnSpan([
                                    'default' => 2,
                                    'md' => 3,
                                ]),
                            Forms\Components\TextInput::make('download')
                                ->label('Download (Mbps)')
                                ->afterStateHydrated(function (TextInput $component, $state) {
                                    $component->state(! blank($state) ? formatBits(formatBytestoBits($state), 3, false) : '');
                                }),
                            Forms\Components\TextInput::make('upload')
                                ->label('Upload (Mbps)')
                                ->afterStateHydrated(function (TextInput $component, $state) {
                                    $component->state(! blank($state) ? formatBits(formatBytestoBits($state), 3, false) : '');
                                }),
                            Forms\Components\TextInput::make('ping')
                                ->label('Ping (Ms)'),
                        ])
                        ->columnSpan(2),
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Checkbox::make('successful'),
                            Forms\Components\Checkbox::make('scheduled'),
                        ])
                        ->columns(1)
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),
                ]),
                Forms\Components\Textarea::make('data')
                    ->rows(10)
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $settings = new GeneralSettings();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('server')
                    ->getStateUsing(fn (Result $record): string|null => ! blank($record->server_id) ? $record->server_id.' ('.$record->server_name.')' : null)
                    ->toggleable(),
                IconColumn::make('successful')
                    ->boolean()
                    ->toggleable(),
                IconColumn::make('scheduled')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('download')
                    ->label('Download (Mbps)')
                    ->getStateUsing(fn (Result $record): string|null => ! blank($record->download) ? formatBits(formatBytestoBits($record->download), 3, false) : null),
                TextColumn::make('upload')
                    ->label('Upload (Mbps)')
                    ->getStateUsing(fn (Result $record): string|null => ! blank($record->upload) ? formatBits(formatBytestoBits($record->upload), 3, false) : null),
                TextColumn::make('ping')
                    ->label('Ping (Ms)')
                    ->toggleable(),
                TextColumn::make('download_jitter')
                    ->getStateUsing(fn (Result $record): string|null => json_decode($record->data, true)['download']['latency']['jitter'] ?? null)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('upload_jitter')
                    ->getStateUsing(fn (Result $record): string|null => json_decode($record->data, true)['upload']['latency']['jitter'] ?? null)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('ping_jitter')
                    ->getStateUsing(fn (Result $record): string|null => json_decode($record->data, true)['ping']['jitter'] ?? null)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime($settings->time_format ?? 'M j, Y G:i:s')
                    ->timezone($settings->timezone ?? 'UTC')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('scheduled')
                    ->placeholder('-')
                    ->trueLabel('Only scheduled speedtests')
                    ->falseLabel('Only manual speedtests')
                    ->queries(
                        true: fn (Builder $query) => $query->where('scheduled', true),
                        false: fn (Builder $query) => $query->where('scheduled', false),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\TernaryFilter::make('successful')
                    ->placeholder('-')
                    ->trueLabel('Only successful speedtests')
                    ->falseLabel('Only failed speedtests')
                    ->queries(
                        true: fn (Builder $query) => $query->where('successful', true),
                        false: fn (Builder $query) => $query->where('successful', false),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('view result')
                        ->label('View on Speedtest.net')
                        ->icon('heroicon-o-link')
                        ->url(fn (Result $record): string|null => optional($record)->url)
                        ->hidden(fn (Result $record): bool => ! $record->is_successful)
                        ->openUrlInNewTab(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('updateComments')
                        ->icon('heroicon-o-annotation')
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
                Tables\Actions\BulkAction::make('export')
                    ->label('Export selected')
                    ->icon('heroicon-o-download')
                    ->action(function (Collection $records) {
                        $export = new ResultsSelectedBulkExport($records->toArray());

                        return Excel::download($export, 'results_'.now()->timestamp.'.csv', \Maatwebsite\Excel\Excel::CSV);
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
        ];
    }
}
