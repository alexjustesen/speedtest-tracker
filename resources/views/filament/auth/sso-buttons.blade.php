@php
    $sso = app(\App\Sso\SsoManager::class);
@endphp

@if ($sso->enabled())
    <div class="flex flex-col gap-y-4">
        <div class="flex items-center gap-x-3 text-sm text-gray-400 dark:text-gray-500">
            <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
            <span>{{ __('auth.sso.or') }}</span>
            <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
        </div>

        <x-filament::button
            tag="a"
            href="{{ route('sso.redirect') }}"
            color="gray"
            size="lg"
            icon="tabler-shield-lock"
            class="w-full justify-center"
        >
            {{ $sso->buttonLabel() }}
        </x-filament::button>
    </div>
@endif
