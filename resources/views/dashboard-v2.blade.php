<x-dashboard-layout title="Dashboard V2">
    <div x-data="dashboard()" x-init="init()" class="space-y-6">
        {{-- Filters Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row gap-4 sm:items-end">
                {{-- Time Range Filter --}}
                <div class="flex-1">
                    <label for="time-range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Time Range
                    </label>
                    <select
                        id="time-range"
                        x-model="filters.timeRange"
                        @change="onFilterChange"
                        class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="24h">Last 24 Hours</option>
                        <option value="7d">Last 7 Days</option>
                        <option value="30d">Last 30 Days</option>
                    </select>
                </div>

                {{-- Server Filter --}}
                <div class="flex-1">
                    <label for="server" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Server
                    </label>
                    <select
                        id="server"
                        x-model="filters.server"
                        @change="onFilterChange"
                        class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Servers</option>
                        <template x-for="server in servers" :key="server.server_id">
                            <option :value="server.server_id" x-text="`${server.server_name} (${server.test_count} tests)`"></option>
                        </template>
                    </select>
                </div>

                {{-- Reset Button --}}
                <div>
                    <button
                        @click="resetFilters"
                        class="w-full sm:w-auto px-4 py-2 text-sm border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>

        {{-- Latest Stats Card --}}
        <div x-show="stats" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
            <h2 class="text-lg font-semibold mb-4">Latest Result</h2>

            <div x-show="loading" class="text-gray-500 dark:text-gray-400">Loading...</div>

            <div x-show="!loading && stats" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Download --}}
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Download</div>
                    <div class="text-2xl font-bold mt-1" x-text="stats ? formatSpeed(stats.download) : '-'"></div>
                </div>

                {{-- Upload --}}
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Upload</div>
                    <div class="text-2xl font-bold mt-1" x-text="stats ? formatSpeed(stats.upload) : '-'"></div>
                </div>

                {{-- Ping --}}
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Ping</div>
                    <div class="text-2xl font-bold mt-1" x-text="stats ? formatPing(stats.ping) : '-'"></div>
                </div>
            </div>

            <div x-show="!loading && stats" class="mt-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                <span x-text="stats ? `Server: ${stats.server_name || 'Unknown'}` : ''"></span>
                <span x-show="stats" class="mx-2">•</span>
                <span x-text="stats ? new Date(stats.created_at).toLocaleString() : ''"></span>
            </div>

            <div x-show="error" class="text-red-600 dark:text-red-400" x-text="error"></div>
        </div>

        {{-- Metric Placeholders --}}
        <div class="grid grid-cols-1 gap-6">
            {{-- Download Placeholder --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
                <h3 class="text-lg font-semibold mb-2">Download</h3>
                <p class="text-gray-500 dark:text-gray-400">Coming in Phase 2</p>
            </div>

            {{-- Upload Placeholder --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
                <h3 class="text-lg font-semibold mb-2">Upload</h3>
                <p class="text-gray-500 dark:text-gray-400">Coming in Phase 3</p>
            </div>

            {{-- Ping Placeholder --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
                <h3 class="text-lg font-semibold mb-2">Ping</h3>
                <p class="text-gray-500 dark:text-gray-400">Coming in Phase 4</p>
            </div>
        </div>
    </div>
</x-dashboard-layout>
