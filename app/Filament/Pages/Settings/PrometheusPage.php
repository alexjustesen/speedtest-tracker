<?php

namespace App\Filament\Pages\Settings;

use App\Settings\PrometheusSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class PrometheusPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Prometheus';

    protected static ?string $navigationLabel = 'Prometheus';

    protected static string $settings = PrometheusSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->is_admin;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                    ->schema([
                        Forms\Components\Section::make('Prometheus Settings')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')
                                    ->label('Enable Metrics Endpoint'),
                                Forms\Components\Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([
                                        // Add components here if needed for this inner grid
                                    ]),
                            ])
                            ->columnSpan(2), // Adjust the column span as needed
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\View::make('filament.forms.prometheus-helptext'),
                            ])
                            ->columnSpan(1), // Adjust the column span as needed
                    ]),
            ]);
    }
}
