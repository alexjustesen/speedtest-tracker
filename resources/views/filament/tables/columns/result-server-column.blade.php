<div {{ $getExtraAttributeBag() }} class="flex items-center gap-x-1.5 fi-ta-text-item fi-ta-text">
    {{ $getServerName() }}

    @filled($getServerId())
        <span class="text-xs text-zinc-600 dark:text-zinc-400">(#{{ $getServerId() }})</span>
    @endfilled
</div>
