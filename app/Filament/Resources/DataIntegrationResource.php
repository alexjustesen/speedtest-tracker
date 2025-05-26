<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataIntegrationResource\Pages;
use App\Jobs\Influxdb\v2\BulkWriteResults;
use App\Jobs\Influxdb\v2\TestConnectionJob;
use App\Models\DataIntegration;
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

class DataIntegrationResource extends Resource
{
    protected static ?string $model = DataIntegration::class;

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
                                ->helperText('When enabled, all new Speedtest results will also be sent to InfluxDB.')
                                ->required(),
                            TextInput::make('name')
                                ->label('Integration Name')
                                ->placeholder('Enter a name for this integration.')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                        ])
                        ->columnSpanFull(),
                    Section::make('Configuration')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('config.url')
                                        ->label('URL')
                                        ->placeholder('http://your-influxdb-instance')
                                        ->required(),
                                    TextInput::make('config.org')
                                        ->label('Org')
                                        ->required(),
                                    TextInput::make('config.bucket')
                                        ->label('Bucket')
                                        ->required(),
                                    TextInput::make('config.token')
                                        ->label('Token')
                                        ->password()
                                        ->required(),
                                    Checkbox::make('config.verify_ssl')
                                        ->label('Verify SSL')
                                        ->required(),
                                ]),
                            Actions::make([
                                Action::make('export')
                                    ->label('Export current results')
                                    ->icon('heroicon-o-cloud-arrow-up')
                                    ->action(fn () => BulkWriteResults::dispatch(Auth::user()))
                                    ->visible(fn (?DataIntegration $record, Get $get): bool => $record?->exists === true && $get('enabled')),
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
            'index' => Pages\ListDataIntegration::route('/'),
        ];
    }
}
