<x-filament-panels::page>
    @filled($token)
        <div>
            {{ $this->tokenInfolist }}
        </div>
    @endfilled

    <div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
