<?php

namespace App\Livewire\Debug;

use App\Models\Result;
use App\Settings\GeneralSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.debug')]
class Timezone extends Component
{
    public $settings;

    public ?Result $latest;

    public function mount()
    {
        $settings = new GeneralSettings();

        $this->settings = $settings->toArray();

        $this->latest = Result::query()
            ->latest()
            ->first();
    }

    public function render()
    {
        return view('livewire.debug.timezone');
    }
}
