<div wire:poll.60s>
    @if ($this->nextSpeedtest)
        <flux:callout color="blue" icon="information-circle" heading="Next speedtest scheduled at {{ $this->nextSpeedtest->timezone(config('app.display_timezone'))->format(config('app.datetime_format')) }}" />
    @endif
</div>
