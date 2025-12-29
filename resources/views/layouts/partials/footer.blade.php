<footer>
    <div class="flex items-center justify-end">
        <div class="flex justify-center gap-x-2">
            @if (\App\Services\GitHub\Repository::updateAvailable())
                <x-filament::badge>
                    {{ __('general.update_available') }}
                </x-filament::badge>
            @endif

            <a href="https://github.com/alexjustesen/speedtest-tracker/releases" class="underline text-sm text-gray-700 hover:text-gray-300" target="_blank" rel="noopener noreferrer">
                {{ config('speedtest.build_version') }}
            </a>
        </div>
    </div>
</footer>
