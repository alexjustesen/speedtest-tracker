<x-guest-layout :title="__('Server Error')">
    <div class="grid px-4 min-h-dvh place-content-center">
        <div class="text-center">
            <h1 class="font-black text-gray-200 dark:text-gray-800 text-9xl">500</h1>

            <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">{{ __('Oops, server error!') }}</p>

            <p class="mt-4 text-gray-500 dark:text-gray-300">There was an issue, check the logs or view the docs for help.</p>

            <a
                href="https://docs.speedtest-tracker.dev/help/faqs#im-getting-a-500-or-server-error-error"
                target="_blank"
                rel="nofollow"
                class="inline-block px-5 py-3 mt-6 text-sm font-medium text-white rounded bg-amber-400 hover:bg-amber-500 focus:outline-none focus:ring focus:ring-amber-400/80">
                {{ __('Documentation') }}
            </a>
        </div>
    </div>
</x-guest-layout>
