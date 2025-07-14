<?php

namespace App\Filament\Resources\ApiTokens\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;

class ApiTokenForm
{
    public static function schema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->required(),
                    CheckboxList::make('abilities')
                        ->label('Abilities')
                        ->options([
                            'results:read' => 'Read results',
                            'speedtests:run' => 'Run speedtest',
                            'ookla:list-servers' => 'List servers',
                        ])
                        ->required()
                        ->bulkToggleable()
                        ->descriptions([
                            'results:read' => 'Allow this token to read results.',
                            'speedtests:run' => 'Allow this token to run speedtests.',
                            'ookla:list-servers' => 'Allow this token to list servers.',
                        ]),
                    DateTimePicker::make('expires_at')
                        ->label('Expires at')
                        ->nullable()
                        ->native(false)
                        ->helperText('Leave empty for no expiration'),
                ])
                ->columns([
                    'lg' => 1,
                ]),
        ];
    }
}