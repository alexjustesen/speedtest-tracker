@php
    $ssoProviders = app(\App\Sso\SsoManager::class)->enabledProviders();
@endphp

@if (filled($ssoProviders))
    <div class="flex flex-col gap-y-4">
        <div class="flex items-center gap-x-3 text-sm text-gray-400 dark:text-gray-500">
            <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
            <span>{{ __('settings/sso.or') }}</span>
            <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
        </div>

        @foreach ($ssoProviders as $providerKey => $provider)
            <x-filament::button
                tag="a"
                href="{{ route('sso.redirect', $providerKey) }}"
                color="gray"
                size="lg"
                :icon="$provider['icon']"
                class="w-full justify-center"
            >
                {{ $provider['label'] }}
            </x-filament::button>
        @endforeach
    </div>
@endif
