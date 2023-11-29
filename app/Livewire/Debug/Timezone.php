<?php

namespace App\Livewire\Debug;

use App\Settings\GeneralSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.debug')]
class Timezone extends Component
{
    public $settings;

    public function mount()
    {
        $settings = new GeneralSettings();

        $this->settings = $settings->toArray();
    }

    public function render()
    {
        return view('livewire.debug.timezone');
    }
}
