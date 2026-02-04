<div {{ $attributes->merge(['class' => 'px-6 py-3']) }}>
    @isset($heading)
        <flux:heading class="flex items-center">{{ $heading }}</flux:heading>
    @endisset

    {{ $slot }}
</div>
