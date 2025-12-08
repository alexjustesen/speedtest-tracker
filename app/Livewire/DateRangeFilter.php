<?php

namespace App\Livewire;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Component;

class DateRangeFilter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function mount(): void
    {
        $defaultRange = config('speedtest.default_chart_range', '24h');

        match ($defaultRange) {
            '24h' => $this->dateFrom = now()->subDay()->startOfDay()->toDateTimeString(),
            'week' => $this->dateFrom = now()->subWeek()->startOfDay()->toDateTimeString(),
            'month' => $this->dateFrom = now()->subMonth()->startOfDay()->toDateTimeString(),
            default => $this->dateFrom = now()->subDay()->startOfDay()->toDateTimeString(),
        };

        $this->dateTo = now()->endOfDay()->toDateTimeString();

        $this->form->fill([
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ]);

        $this->broadcastFilter();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DateTimePicker::make('dateFrom')
                            ->label(__('From'))
                            ->seconds(false)
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->dateFrom = $state;
                                if ($this->dateFrom && $this->dateTo) {
                                    $this->broadcastFilter();
                                }
                            }),
                        DateTimePicker::make('dateTo')
                            ->label(__('To'))
                            ->seconds(false)
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->dateTo = $state;
                                if ($this->dateFrom && $this->dateTo) {
                                    $this->broadcastFilter();
                                }
                            }),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ]);
    }

    public function broadcastFilter(): void
    {
        $this->dispatch('date-range-updated', [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ]);
    }

    public function render()
    {
        return view('livewire.date-range-filter');
    }
}
