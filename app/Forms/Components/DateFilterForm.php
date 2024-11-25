<?php

namespace App\Forms\Components;

use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
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
                        DateTimePicker::make('startDate')
                            ->label('Start Date')
                            ->default($defaultStartDate->startOfDay())
                            ->reactive()
                            ->seconds(false)
                            ->native(false),
                        DateTimePicker::make('endDate')
                            ->label('End Date')
                            ->default($defaultEndDate->endOfDay())
                            ->reactive()
                            ->seconds(false)
                            ->native(false),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ]);
    }
}
