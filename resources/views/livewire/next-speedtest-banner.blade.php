<div wire:poll.60s>
    @if ($this->nextSpeedtest)
        <div class="rounded-md bg-blue-50 dark:bg-blue-500/10 p-4 outline outline-blue-500/20">
            <div class="flex">
                <div class="shrink-0">
                    <x-tabler-info-circle class="size-5 text-blue-400" />
                </div>

                <div class="ml-3 flex-1">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Next scheduled test at <span class="font-medium">{{ $this->nextSpeedtest->timezone(config('app.display_timezone'))->format('F jS, Y, g:i a') }}</span>.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
