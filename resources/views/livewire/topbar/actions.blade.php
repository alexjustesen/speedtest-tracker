<div class="py-3">
    <div class="flex items-center gap-4">
        {{ $this->speedtestAction }}

        @if ($showDashboard)
            {{ $this->dashboardAction }}
        @endif
    </div>

    <x-filament-actions::modals />
</div>
