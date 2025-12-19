<div>
    @if ($this->hasDeprecatedChannels)
        <div class="rounded-md bg-amber-50 dark:bg-amber-500/10 p-4 outline outline-amber-500/20">
            <div class="flex">
                <div class="shrink-0">
                    <x-tabler-alert-triangle class="size-5 text-amber-400" />
                </div>

                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-amber-800 dark:text-amber-300">
                        Deprecated Notification Channels
                    </h3>
                    <div class="mt-2 text-sm text-amber-700 dark:text-amber-400">
                        <p>
                            You are currently using the following deprecated notification channels: <strong>{{ implode(', ', $this->deprecatedChannelsList) }}</strong>.
                        </p>
                        <p class="mt-1">
                            These channels will be removed at the end of January 2026. As of that moment you will no longer receive notifications. Please migrate to <a href="{{ url('/admin/notification') }}" class="font-medium underline hover:text-amber-900 dark:hover:text-amber-200">Apprise</a> which supports all these services and more.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
