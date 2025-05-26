<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataIntegrationSettingResource\Pages;
use App\Jobs\Influxdb\v2\BulkWriteResults;
use App\Jobs\Influxdb\v2\TestConnectionJob;
use App\Models\DataIntegrationSetting;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DataIntegrationSettingResource extends Resource
{
    protected static ?string $model = DataIntegrationSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Data Integration';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    /**
     * Prevent anyone from creating a new settings record.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'lg' => 2,
                ])->schema([
                    Section::make('Details')
                        ->schema([
                            Toggle::make('enabled')
                                ->label('Enable integration')
                                ->helperText('Turn this on to start sending data to InfluxDB.')
                                ->required(),
                            TextInput::make('name')
                                ->label('Integration Name')
                                ->placeholder('Enter a name for this integration.')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                        ])
                        ->columnSpanFull(),
                    Section::make('InfluxDB Connection')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('url')
                                        ->label('URL')
                                        ->url()
                                        ->required()
                                        ->placeholder('http://your-influxdb-instance'),
                                    TextInput::make('org')
                                        ->label('Org')
                                        ->required(),
                                    TextInput::make('bucket')
                                        ->label('Bucket')
                                        ->required()
                                        ->placeholder('speedtest-tracker'),
                                    TextInput::make('token')
                                        ->label('Token')
                                        ->required()
                                        ->password(),
                                ]),
                            Checkbox::make('verify_ssl')
                                ->label('Verify SSL')
                                ->hidden(fn ($get) => ! $get('enabled')),
                            Actions::make([
                                Action::make('export')
                                    ->label('Export current results')
                                    ->icon('heroicon-o-cloud-arrow-up')
                                    ->action(fn () => BulkWriteResults::dispatch(Auth::user()))
                                    ->visible(fn (?DataIntegrationSetting $record, Get $get): bool => $record?->exists === true && $get('enabled')),
                                Action::make('test_connection')
                                    ->label('Test connection')
                                    ->icon('heroicon-o-check-circle')
                                    ->action(fn () => TestConnectionJob::dispatch(Auth::user()))
                                    ->visible(fn ($get) => $get('enabled')),
                            ])
                                ->columns(2)
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('type'),
                IconColumn::make('enabled')
                    ->boolean()
                    ->label('Enabled'),
                TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataIntegrationSettings::route('/'),
        ];
    }
}
