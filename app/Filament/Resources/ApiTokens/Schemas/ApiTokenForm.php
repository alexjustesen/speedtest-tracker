<?php

namespace App\Filament\Resources\ApiTokens\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

class ApiTokenForm
{
    public static function schema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('name')
                        ->label(__('general.name'))
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->required(),
                    CheckboxList::make('abilities')
                        ->label(__('api_tokens.abilities'))
                        ->options([
                            'results:read' => __('api_tokens.read_results'),
                            'speedtests:run' => __('general.run_speedtest'),
                            'ookla:list-servers' => __('general.list_servers'),
                        ])
                        ->required()
                        ->bulkToggleable()
                        ->descriptions([
                            'results:read' => __('api_tokens.read_results_description'),
                            'speedtests:run' => __('api_tokens.run_speedtest_description'),
                            'ookla:list-servers' => __('api_tokens.list_servers_description'),
                        ]),
                    DateTimePicker::make('expires_at')
                        ->label(__('api_tokens.expires_at'))
                        ->nullable()
                        ->native(false)
                        ->helperText(__('api_tokens.expires_at_helper_text')),
                ])
                ->columns([
                    'lg' => 1,
                ]),
        ];
    }
}
