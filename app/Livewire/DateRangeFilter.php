<?php

namespace App\Livewire;

use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Livewire\Component;

class DateRangeFilter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public ?string $relativeRange = null;

    protected bool $isApplyingPreset = false;

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(app(GeneralSettings::class)->default_chart_range)->toDateTimeString();
        $this->dateTo = now()->toDateTimeString();

        $this->form->fill([
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'relativeRange' => $this->relativeRange,
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
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                $this->dateFrom = $state;

                                if (! $this->isApplyingPreset) {
                                    $this->relativeRange = null;
                                    $set('relativeRange', null);

                                    if ($this->dateFrom && $this->dateTo) {
                                        $this->broadcastFilter();
                                    }
                                }
                            }),
                        DateTimePicker::make('dateTo')
                            ->label(__('general.end_date'))
                            ->seconds(false)
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('dateFrom'))
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                $this->dateTo = $state;

                                if (! $this->isApplyingPreset) {
                                    $this->relativeRange = null;
                                    $set('relativeRange', null);

                                    if ($this->dateFrom && $this->dateTo) {
                                        $this->broadcastFilter();
                                    }
                                }
                            }),
                        ToggleButtons::make('relativeRange')
                            ->label(__('general.relative_range'))
                            ->native(false)
                            ->options([
                                '24h' => __('general.range_24h'),
                                '7d' => __('general.range_7d'),
                                '30d' => __('general.range_30d'),
                            ])
                            ->grouped()
                            ->live()
                            ->columnSpanFull()
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                if (blank($state)) {
                                    return;
                                }

                                [$dateFrom, $dateTo] = $this->presetRange($state);

                                $this->isApplyingPreset = true;

                                $this->dateFrom = $dateFrom->toDateTimeString();
                                $this->dateTo = $dateTo->toDateTimeString();

                                $set('dateFrom', $this->dateFrom);
                                $set('dateTo', $this->dateTo);

                                $this->isApplyingPreset = false;

                                $this->broadcastFilter();
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

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function presetRange(string $key): array
    {
        return match ($key) {
            '24h' => [now()->subHours(24), now()],
            '7d' => [now()->subDays(7), now()],
            '30d' => [now()->subDays(30), now()],
        };
    }

    public function render()
    {
        return view('livewire.date-range-filter');
    }
}
