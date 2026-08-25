<?php

namespace App\Livewire;

use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\Component;

class DateRangeFilter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(app(GeneralSettings::class)->chart_default_range)->toDateTimeString();
        $this->dateTo = now()->toDateTimeString();

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
                            ->label(__('general.start_date'))
                            ->seconds(false)
                            ->native(false)
                            ->maxDate(fn (Get $get) => $get('dateTo'))
                            ->live()
                            ->afterStateUpdated(function (?string $state) {
                                $this->dateFrom = $state;

                                if ($this->dateFrom && $this->dateTo) {
                                    $this->broadcastFilter();
                                }
                            }),
                        DateTimePicker::make('dateTo')
                            ->label(__('general.end_date'))
                            ->seconds(false)
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('dateFrom'))
                            ->live()
                            ->afterStateUpdated(function (?string $state) {
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
        $dateFrom = Carbon::parse($this->dateFrom);
        $dateTo = Carbon::parse($this->dateTo);

        if ($dateFrom->greaterThan($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $this->dispatch('date-range-updated', [
            'dateFrom' => $dateFrom->toDateTimeString(),
            'dateTo' => $dateTo->toDateTimeString(),
        ]);
    }

    public function render()
    {
        return view('livewire.date-range-filter');
    }
}
