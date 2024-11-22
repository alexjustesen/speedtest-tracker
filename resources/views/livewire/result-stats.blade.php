<div wire:poll.5s>
    <h3 class="text-base font-semibold text-gray-100">Latest</h3>


    <h3 class="mt-10 text-base font-semibold text-gray-100">Last 30 days</h3>
    <dl class="grid grid-cols-1 gap-5 mt-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="px-4 py-5 overflow-hidden bg-gray-900 rounded-lg shadow-sm sm:p-6 ring-1 ring-white/10">
            <dt class="text-sm font-medium text-gray-400 truncate">Total tests</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-white">{{ $totals['total_count'] }}</dd>
        </div>

        <div class="px-4 py-5 overflow-hidden bg-gray-900 rounded-lg shadow sm:p-6 ring-1 ring-white/10">
            <dt class="text-sm font-medium text-gray-400 truncate">Completed tests</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-white">{{ $totals['completed_count'] }}</dd>
        </div>

        <div class="px-4 py-5 overflow-hidden bg-gray-900 rounded-lg shadow sm:p-6 ring-1 ring-white/10">
            <dt class="text-sm font-medium text-gray-400 truncate">Failed tests</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-white">{{ $totals['failed_count'] }}</dd>
        </div>

        <div class="px-4 py-5 overflow-hidden bg-gray-900 rounded-lg shadow sm:p-6 ring-1 ring-white/10">
            <dt class="text-sm font-medium text-gray-400 truncate">Success Rate</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-white">{{ $successRate }}</dd>
        </div>

        <div class="px-4 py-5 overflow-hidden bg-gray-900 rounded-lg shadow sm:p-6 ring-1 ring-white/10">
            <dt class="text-sm font-medium text-gray-400 truncate">Avg. download</dt>
            <dd class="flex items-baseline mt-1 gap-x-2">
                <span class="text-3xl font-semibold tracking-tight text-white">{{ array_map('trim', explode(' ', $avgDownload))[0] }}</span>
                <span class="text-sm text-gray-400">{{ array_map('trim', explode(' ', $avgDownload))[1] }}</span>
            </dd>
        </div>

        <div class="px-4 py-5 overflow-hidden bg-gray-900 rounded-lg shadow sm:p-6 ring-1 ring-white/10">
            <dt class="text-sm font-medium text-gray-400 truncate">Avg. upload</dt>
            <dd class="flex items-baseline mt-1 gap-x-2">
                <span class="text-3xl font-semibold tracking-tight text-white">{{ array_map('trim', explode(' ', $avgUpload))[0] }}</span>
                <span class="text-sm text-gray-400">{{ array_map('trim', explode(' ', $avgUpload))[1] }}</span>
            </dd>
        </div>

        <div class="px-4 py-5 overflow-hidden bg-gray-900 rounded-lg shadow sm:p-6 ring-1 ring-white/10">
            <dt class="text-sm font-medium text-gray-400 truncate">Avg. ping</dt>
            <dd class="flex items-baseline mt-1 gap-x-2">
                <span class="text-3xl font-semibold tracking-tight text-white">{{ $avgPing }}</span>
                <span class="text-sm text-gray-400">ms</span>
            </dd>
        </div>
    </dl>
</div>
