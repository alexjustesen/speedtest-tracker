<?php

namespace App\Forms\Components;

use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class DateFilterForm
{
    public static function make(Form $form): Form
    {
        // Retrieve the default number of days from the configuration
        $defaultRangeDays = config('app.chart_default_date_range');

        // Calculate the start and end dates based on the configuration value
        $defaultEndDate = now(); // Today
        $defaultStartDate = now()->subDays($defaultRangeDays); // Start date for the range

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Select::make('predefinedRange')
                            ->label('Time Range')
                            ->options([
                                '24_hours' => 'Last 24 Hours',
                                '1_week' => 'Last 1 Week',
                                '1_month' => 'Last 1 Month',
                                'custom' => 'Custom Range',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                switch ($state) {
                                    case '24_hours':
                                        $set('startDate', now()->subDay()->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '1_week':
                                        $set('startDate', now()->subWeek()->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case '1_month':
                                        $set('startDate', now()->subMonth()->toDateString());
                                        $set('endDate', now()->toDateString());
                                        break;
                                    case 'custom':
                                        break;
                                }
                            })
                            ->default('custom'),

                        DateTimePicker::make('startDate')
                            ->label('Start Date')
                            ->default($defaultStartDate->startOfDay()) // This preserves the time if needed
                            ->reactive()
                            ->native(false)
                            ->hidden(fn ($get) => $get('predefinedRange') !== 'custom'),

                        DateTimePicker::make('endDate')
                            ->label('End Date')
                            ->default($defaultEndDate->endOfDay()) // Preserving the full date-time
                            ->reactive()
                            ->native(false)
                            ->hidden(fn ($get) => $get('predefinedRange') !== 'custom'),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 3,
                    ]),
            ]);
    }
}
