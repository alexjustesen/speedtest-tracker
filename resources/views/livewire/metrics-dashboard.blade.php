<div class="flex h-full w-full flex-1 flex-col gap-6" wire:poll.5s="checkForNewResults">
    <livewire:next-speedtest-banner />

    <div class="flex items-center justify-between">
        <flux:heading size="xl" class="flex items-center gap-2">
            <flux:icon.chart-no-axes-combined class="size-5" />
            Metrics Dashboard
        </flux:heading>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <flux:input
                    wire:model.live="startDate"
                    :loading="false"
                    type="date"
                    size="sm"
                    max="{{ now()->format('Y-m-d') }}"
                    placeholder="Start date" />

                <flux:input
                    wire:model.live="endDate"
                    :loading="false"
                    type="date"
                    size="sm"
                    max="{{ now()->format('Y-m-d') }}"
                    placeholder="End date" />
            </div>

            <flux:separator vertical class="my-2" />

            <flux:button.group>
                <flux:button size="sm" wire:click="setLastDay">1D</flux:button>
                <flux:button size="sm" wire:click="setLastWeek">1W</flux:button>
                <flux:button size="sm" wire:click="setLastMonth">1M</flux:button>
                {{-- <flux:button size="sm" icon="calendar-search" /> --}}
            </flux:button.group>

            <flux:separator vertical class="my-2" />

            <div>
                <flux:modal.trigger name="displaySettingsModal">
                    <flux:button size="sm" icon="sliders-vertical" />
                </flux:modal.trigger>

                <!-- Display Settings Modal -->
                <flux:modal name="displaySettingsModal" flyout class="space-y-6">
                    <div>
                        <flux:heading size="sm">Manage Sections</flux:heading>
                        <flux:text size="sm">Drag to reorder, uncheck to hide sections</flux:text>
                    </div>

                    <div x-data="sectionManager()" x-init="init()">
                        <!-- Section Visibility List -->
                        <div class="flex flex-col">
                            <template x-for="(section, index) in sections" :key="section.id">
                                <div
                                    @dragover.prevent="dragOver(index, $event)"
                                    @drop="drop(index, $event)"
                                    @dragleave="dragLeave($event)"
                                    :class="{ 'opacity-50': draggingIndex === index, 'border-blue-500 dark:border-blue-400': dragOverIndex === index }"
                                    class="flex items-center gap-3 p-3 mb-2 last:mb-0 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 transition-all"
                                >
                                    <!-- Checkbox for visibility -->
                                    <flux:checkbox
                                        x-model="section.visible"
                                        @change="updateVisibility()"
                                        class="shrink-0"
                                    />

                                    <!-- Section Info -->
                                    <div class="flex items-center gap-2 flex-1">
                                        <span
                                            x-text="section.name"
                                            class="font-medium text-neutral-900 dark:text-neutral-100"
                                        ></span>
                                    </div>

                                    <!-- Drag Handle -->
                                    <div
                                        draggable="true"
                                        @dragstart="dragStart(index, $event)"
                                        @dragend="dragEnd($event)"
                                        class="shrink-0 text-neutral-400 dark:text-neutral-600 cursor-grab active:cursor-grabbing"
                                    >
                                        <flux:icon.grip-vertical class="size-5" />
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Reset to Default Button -->
                        <flux:button
                            @click="resetToDefaults()"
                            variant="ghost"
                            size="sm"
                            class="mt-4 w-full"
                        >
                            Reset to Defaults
                        </flux:button>
                    </div>
                </flux:modal>
            </div>
        </div>
    </div>

    @if($chartData['hasFailedResults'])
        <flux:callout variant="danger" icon="x-circle" inline>
            <flux:callout.heading>There are failed speed tests in this date range.</flux:callout.heading>

            <x-slot name="actions">
                <flux:button variant="ghost">View results</flux:button>
            </x-slot>
        </flux:callout>
    @endif

    <!-- Data Grid with Dynamic Section Ordering -->
    <div
        x-data="dashboardSections()"
        x-init="init()"
        class="grid grid-cols-1 lg:grid-cols-4 gap-6"
    >
        <template x-for="sectionId in visibleSections" :key="sectionId">
            <div class="col-span-full">
                <!-- Speed Section -->
                <template x-if="sectionId === 'speed'">
                    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900 overflow-hidden">
                        <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                            <flux:icon.chart-line class="size-5 text-neutral-600 dark:text-neutral-400" />
                            Speed
                        </flux:heading>

                        <!-- Speed Chart -->
                        <div
                            x-data="speedChartComponent({
                                labels: @js($chartData['labels']),
                                resultIds: @js($chartData['resultIds']),
                                downloadData: @js($chartData['download']),
                                uploadData: @js($chartData['upload']),
                                downloadColor: 'rgb(59, 130, 246)',
                                uploadColor: 'rgb(245, 158, 11)',
                                downloadBenchmarkFailed: @js($chartData['downloadBenchmarkFailed']),
                                uploadBenchmarkFailed: @js($chartData['uploadBenchmarkFailed']),
                                downloadBenchmarks: @js($chartData['downloadBenchmarks']),
                                uploadBenchmarks: @js($chartData['uploadBenchmarks']),
                                resultStatusFailed: @js($chartData['resultStatusFailed']),
                                daysDifference: @js($chartData['daysDifference']),
                            })"
                            @charts-updated.window="updateChart($event.detail.chartData)"
                            wire:ignore
                            class="aspect-[2/1] lg:aspect-[4/1] px-6 py-4"
                        >
                            <canvas x-ref="canvas"></canvas>
                        </div>

                        <!-- Speed Stats -->
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <!-- Download Stats -->
                            <div class="border-t border-neutral-200 dark:border-neutral-700">
                                <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-blue-600 dark:text-blue-400">
                                    <flux:icon.download class="size-4" />
                                    Download Speed
                                </flux:heading>

                                <div class="grid grid-cols-2 lg:grid-cols-3">
                                    <x-dashboard.stats-card heading="Latest">
                                        <x-slot name="heading">
                                            @if($chartData['downloadStats']['latestFailed'] && $chartData['downloadStats']['latestBenchmark'])
                                                <flux:tooltip content="Benchmark Failed: {{ $chartData['downloadStats']['latestBenchmark']['bar'] === 'min' ? 'Min' : 'Max' }} {{ $chartData['downloadStats']['latestBenchmark']['value'] }} {{ $chartData['downloadStats']['latestBenchmark']['unit'] }}" class="mr-2">
                                                    <flux:icon.triangle-alert class="size-4 text-amber-500" />
                                                </flux:tooltip>
                                            @endif
                                            Latest
                                        </x-slot>

                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['downloadStats']['latest'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Average">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['downloadStats']['average'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="P95">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['downloadStats']['p95'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Maximum">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['downloadStats']['maximum'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Minimum">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['downloadStats']['minimum'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Healthy">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['downloadStats']['healthy'], 1) }}%
                                        </flux:text>
                                    </x-dashboard.stats-card>
                                </div>
                            </div>

                            <!-- Upload Stats -->
                            <div class="border-t lg:border-l border-neutral-200 dark:border-neutral-700">
                                <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-amber-600 dark:text-amber-400">
                                    <flux:icon.upload class="size-4" />
                                    Upload Speed
                                </flux:heading>

                                <div class="grid grid-cols-2 lg:grid-cols-3 ">
                                    <x-dashboard.stats-card heading="Latest">
                                        <x-slot name="heading">
                                            @if($chartData['uploadStats']['latestFailed'] && $chartData['uploadStats']['latestBenchmark'])
                                                <flux:tooltip content="Benchmark Failed: {{ $chartData['uploadStats']['latestBenchmark']['bar'] === 'min' ? 'Min' : 'Max' }} {{ $chartData['uploadStats']['latestBenchmark']['value'] }} {{ $chartData['uploadStats']['latestBenchmark']['unit'] }}" class="mr-2">
                                                    <flux:icon.triangle-alert class="size-4 text-amber-500" />
                                                </flux:tooltip>
                                            @endif
                                            Latest
                                        </x-slot>

                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['uploadStats']['latest'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Average">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['uploadStats']['average'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="P95">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['uploadStats']['p95'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Maximum">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['uploadStats']['maximum'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Minimum">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['uploadStats']['minimum'], 2) }} Mbps
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Healthy">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['uploadStats']['healthy'], 1) }}%
                                        </flux:text>
                                    </x-dashboard.stats-card>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Ping Section -->
                <template x-if="sectionId === 'ping'">
                    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
                        <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                            <flux:icon.radio class="size-5 text-neutral-600 dark:text-neutral-400" />
                            {{ __('general.ping') }}
                        </flux:heading>

                        <!-- Ping Chart -->
                        <div
                            x-data="chartComponent({
                                type: 'line',
                                label: 'Ping (ms)',
                                labels: @js($chartData['labels']),
                                resultIds: @js($chartData['resultIds']),
                                data: @js($chartData['ping']),
                                benchmarkFailed: @js($chartData['pingBenchmarkFailed']),
                                benchmarks: @js($chartData['pingBenchmarks']),
                                resultStatusFailed: @js($chartData['resultStatusFailed']),
                                color: 'rgb(168, 85, 247)',
                                field: 'ping',
                                showPoints: true,
                                unit: 'ms',
                                daysDifference: @js($chartData['daysDifference']),
                            })"
                            @charts-updated.window="updateChart($event.detail.chartData)"
                            wire:ignore
                            class="aspect-[3/1] lg:aspect-[5/1] px-6 py-4"
                        >
                            <canvas x-ref="canvas"></canvas>
                        </div>

                        <!-- Ping Stats -->
                        <div class="grid grid-cols-2 lg:grid-cols-6 border-t border-neutral-200 dark:border-neutral-700">
                            <x-dashboard.stats-card heading="Latest">
                                <x-slot name="heading">
                                    @if($chartData['pingStats']['latestFailed'] && $chartData['pingStats']['latestBenchmark'])
                                        <flux:tooltip content="Benchmark Failed: {{ $chartData['pingStats']['latestBenchmark']['bar'] === 'min' ? 'Min' : 'Max' }} {{ $chartData['pingStats']['latestBenchmark']['value'] }} {{ $chartData['pingStats']['latestBenchmark']['unit'] }}" class="mr-2">
                                            <flux:icon.triangle-alert class="size-4 text-amber-500" />
                                        </flux:tooltip>
                                    @endif
                                    Latest
                                </x-slot>

                                <flux:text class="text-xl">
                                    {{ number_format($chartData['pingStats']['latest'], 2) }} ms
                                </flux:text>
                            </x-dashboard.stats-card>

                            <x-dashboard.stats-card heading="Average">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['pingStats']['average'], 2) }} ms
                                </flux:text>
                            </x-dashboard.stats-card>

                            <x-dashboard.stats-card heading="P95">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['pingStats']['p95'], 2) }} ms
                                </flux:text>
                            </x-dashboard.stats-card>

                            <x-dashboard.stats-card heading="Maximum">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['pingStats']['maximum'], 2) }} ms
                                </flux:text>
                            </x-dashboard.stats-card>

                            <x-dashboard.stats-card heading="Minimum">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['pingStats']['minimum'], 2) }} ms
                                </flux:text>
                            </x-dashboard.stats-card>

                            <x-dashboard.stats-card heading="Healthy">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['pingStats']['healthy'], 1) }}%
                                </flux:text>
                            </x-dashboard.stats-card>
                        </div>
                    </div>
                </template>

                <!-- Latency Section -->
                <template x-if="sectionId === 'latency'">
                    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
                        <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                            <flux:icon.activity class="size-5 text-neutral-600 dark:text-neutral-400" />
                            Latency (IQM)
                        </flux:heading>

                        <!-- Latency Chart -->
                        <div
                            x-data="multiLineChartComponent({
                                labels: @js($chartData['labels']),
                                resultIds: @js($chartData['resultIds']),
                                resultStatusFailed: @js($chartData['resultStatusFailed']),
                                datasets: [
                                    {
                                        label: 'Download Latency (ms)',
                                        data: @js($chartData['downloadLatency']),
                                        color: 'rgb(59, 130, 246)',
                                    },
                                    {
                                        label: 'Upload Latency (ms)',
                                        data: @js($chartData['uploadLatency']),
                                        color: 'rgb(245, 158, 11)',
                                    }
                                ],
                                unit: 'ms',
                                daysDifference: @js($chartData['daysDifference']),
                            })"
                            @charts-updated.window="updateChart($event.detail.chartData)"
                            wire:ignore
                            class="aspect-[2/1] lg:aspect-[5/1] px-6 py-4"
                        >
                            <canvas x-ref="canvas"></canvas>
                        </div>

                        <!-- Latency Stats -->
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <!-- Download Latency Stats -->
                            <div class="border-t border-neutral-200 dark:border-neutral-700">
                                <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-blue-600 dark:text-blue-400">
                                    <flux:icon.download class="size-4" />
                                    Download Latency
                                </flux:heading>

                                <div class="grid grid-cols-3">
                                    <x-dashboard.stats-card heading="Latest">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['latencyStats']['downloadLatest'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Maximum">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['latencyStats']['downloadMaximum'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Minimum">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['latencyStats']['downloadMinimum'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>
                                </div>
                            </div>

                            <!-- Upload Latency Stats -->
                            <div class="border-t lg:border-l border-neutral-200 dark:border-neutral-700">
                                <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-amber-600 dark:text-amber-400">
                                    <flux:icon.upload class="size-4" />
                                    Upload Latency
                                </flux:heading>

                                <div class="grid grid-cols-3">
                                    <x-dashboard.stats-card heading="Latest">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['latencyStats']['uploadLatest'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Maximum">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['latencyStats']['uploadMaximum'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Minimum">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['latencyStats']['uploadMinimum'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Jitter Section -->
                <template x-if="sectionId === 'jitter'">
                    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
                        <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                            <flux:icon.coffee class="size-5 text-neutral-600 dark:text-neutral-400" />
                            Jitter
                        </flux:heading>

                        <!-- Jitter Chart -->
                        <div
                            x-data="multiLineChartComponent({
                                labels: @js($chartData['labels']),
                                resultIds: @js($chartData['resultIds']),
                                resultStatusFailed: @js($chartData['resultStatusFailed']),
                                datasets: [
                                    {
                                        label: 'Download Jitter (ms)',
                                        data: @js($chartData['downloadJitter']),
                                        color: 'rgb(59, 130, 246)',
                                    },
                                    {
                                        label: 'Upload Jitter (ms)',
                                        data: @js($chartData['uploadJitter']),
                                        color: 'rgb(245, 158, 11)',
                                    },
                                    {
                                        label: 'Ping Jitter (ms)',
                                        data: @js($chartData['pingJitter']),
                                        color: 'rgb(168, 85, 247)',
                                    }
                                ],
                                unit: 'ms',
                                daysDifference: @js($chartData['daysDifference']),
                            })"
                            @charts-updated.window="updateChart($event.detail.chartData)"
                            wire:ignore
                            class="aspect-[2/1] lg:aspect-[5/1] px-6 py-4"
                        >
                            <canvas x-ref="canvas"></canvas>
                        </div>

                        <!-- Jitter Stats -->
                        <div class="grid grid-cols-1 lg:grid-cols-3">
                            <!-- Download Jitter Stats -->
                            <div class="border-t border-neutral-200 dark:border-neutral-700">
                                <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-blue-600 dark:text-blue-400">
                                    <flux:icon.download class="size-4" />
                                    Download Jitter
                                </flux:heading>

                                <div class="grid grid-cols-3">
                                    <x-dashboard.stats-card heading="Latest">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['downloadLatest'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Average">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['downloadAverage'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="P95">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['downloadP95'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>
                                </div>
                            </div>

                            <!-- Upload Jitter Stats -->
                            <div class="border-t lg:border-l border-neutral-200 dark:border-neutral-700">
                                <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-amber-600 dark:text-amber-400">
                                    <flux:icon.upload class="size-4" />
                                    Upload Jitter
                                </flux:heading>

                                <div class="grid grid-cols-3">
                                    <x-dashboard.stats-card heading="Latest">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['uploadLatest'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Average">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['uploadAverage'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="P95">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['uploadP95'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>
                                </div>
                            </div>

                            <!-- Ping Jitter Stats -->
                            <div class="border-t lg:border-l border-neutral-200 dark:border-neutral-700">
                                <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-purple-600 dark:text-purple-400">
                                    <flux:icon.radio class="size-4" />
                                    Ping Jitter
                                </flux:heading>

                                <div class="grid grid-cols-3">
                                    <x-dashboard.stats-card heading="Latest">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['pingLatest'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="Average">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['pingAverage'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>

                                    <x-dashboard.stats-card heading="P95">
                                        <flux:text class="text-xl">
                                            {{ number_format($chartData['jitterStats']['pingP95'], 2) }} ms
                                        </flux:text>
                                    </x-dashboard.stats-card>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Packet Loss Section -->
                <template x-if="sectionId === 'packetLoss'">
                    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
                        <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                            <flux:icon.package class="size-5 text-neutral-600 dark:text-neutral-400" />
                            Packet Loss
                        </flux:heading>

                        <!-- Packet Loss Chart -->
                        <div
                            x-data="scatterChartComponent({
                                labels: @js($chartData['labels']),
                                resultIds: @js($chartData['resultIds']),
                                data: @js($chartData['packetLoss']),
                                resultStatusFailed: @js($chartData['resultStatusFailed']),
                            })"
                            @charts-updated.window="updateChart($event.detail.chartData)"
                            wire:ignore
                            class="aspect-[3/1] lg:aspect-[5/1] px-6 py-4"
                        >
                            <canvas x-ref="canvas"></canvas>
                        </div>

                        <!-- Packet Loss Stats -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 border-t border-neutral-200 dark:border-neutral-700">
                            <x-dashboard.stats-card heading="Latest">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['packetLossStats']['latest'], 2) }}%
                                </flux:text>
                            </x-dashboard.stats-card>

                            <x-dashboard.stats-card heading="Average">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['packetLossStats']['average'], 2) }}%
                                </flux:text>
                            </x-dashboard.stats-card>

                            <x-dashboard.stats-card heading="Maximum">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['packetLossStats']['maximum'], 2) }}%
                                </flux:text>
                            </x-dashboard.stats-card>

                            <x-dashboard.stats-card heading="Minimum">
                                <flux:text class="text-xl">
                                    {{ number_format($chartData['packetLossStats']['minimum'], 2) }}%
                                </flux:text>
                            </x-dashboard.stats-card>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>

@script
<script>
    Alpine.data('chartComponent', (config) => ({
        chart: null,
        animationFrame: null,
        currentLabels: config.labels,
        currentData: config.data,

        init() {
            this.createChart(config.labels, config.data);

            // Listen for theme changes and re-draw chart
            window.addEventListener('theme-changed', () => {
                // Small delay to allow DOM to update
                setTimeout(() => {
                    this.createChart(this.currentLabels, this.currentData);
                }, 100);
            });
        },

        destroy() {
            if (this.animationFrame) {
                cancelAnimationFrame(this.animationFrame);
            }
            if (this.chart) {
                this.chart.destroy();
            }
        },

        createChart(labels, data) {
            // Store current data for theme changes
            this.currentLabels = labels;
            this.currentData = data;

            if (this.chart) {
                this.chart.destroy();
            }

            const isLine = config.type === 'line';
            const showPoints = config.showPoints || false;
            const unit = config.unit || 'Mbps';
            const benchmarkFailed = config.benchmarkFailed || [];
            const resultStatusFailed = config.resultStatusFailed || [];
            const amberColor = 'rgb(251, 191, 36)'; // Amber for failed benchmarks
            const daysDifference = config.daysDifference || 0;
            const showRingingPoints = daysDifference <= 8;

            // Detect dark mode for text colors
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? 'rgb(228, 228, 231)' : 'rgb(39, 39, 42)';
            const gridColor = isDarkMode ? 'rgba(228, 228, 231, 0.1)' : 'rgba(0, 0, 0, 0.1)';

            // Convert rgb() to rgba() with opacity for fill
            const getFillColor = (color) => {
                if (config.type === 'bar') return color;
                return color.replace('rgb(', 'rgba(').replace(')', ', 0.3)');
            };

            // Create point color and radius arrays based on benchmark failures
            // Failed status results use the same color as the line (not red)
            const pointColors = data.map((_, index) => {
                if (benchmarkFailed[index] && !resultStatusFailed[index]) return amberColor;
                return config.color;
            });

            // Show points for benchmark failures only if date range <= 8 days
            const pointRadii = data.map((_, index) => {
                if (!showRingingPoints) return 0; // Hide all points if date range > 8 days
                if (benchmarkFailed[index] && !resultStatusFailed[index]) return 5;
                return 0; // Hide by default for failed status and normal points
            });

            // Show points on hover for failed status and benchmark failures
            const pointHoverRadii = data.map((_, index) =>
                (benchmarkFailed[index] || resultStatusFailed[index]) ? 7 : 5
            );

            // Plugin to create ping/ripple effect on failed benchmark points (NOT for failed status)
            const self = this;
            const pulsingPointsPlugin = {
                id: 'pulsingPoints',
                afterDatasetsDraw: (chart) => {
                    // Only show ringing animation if date range is 8 days or less
                    if (!showRingingPoints) {
                        return;
                    }

                    const ctx = chart.ctx;
                    const meta = chart.getDatasetMeta(0);
                    const time = Date.now();

                    meta.data.forEach((point, index) => {
                        // Only show ringing for benchmark failures, not failed status results
                        if (benchmarkFailed[index] && !resultStatusFailed[index]) {
                            const x = point.x;
                            const y = point.y;

                            // Create multiple ping rings at different stages
                            const pingDuration = 2000; // Duration of one ping cycle in ms
                            const maxRadius = 25;
                            const numberOfRings = 3;

                            for (let i = 0; i < numberOfRings; i++) {
                                // Offset each ring by a fraction of the duration
                                const offset = (pingDuration / numberOfRings) * i;
                                const progress = ((time + offset) % pingDuration) / pingDuration;

                                // Radius expands from 0 to maxRadius
                                const radius = progress * maxRadius;

                                // Opacity fades out as ring expands
                                const opacity = Math.max(0, 0.6 * (1 - progress));

                                // Draw ping ring
                                if (opacity > 0.05) { // Only draw if visible
                                    ctx.save();
                                    ctx.beginPath();
                                    ctx.arc(x, y, radius, 0, 2 * Math.PI);
                                    ctx.strokeStyle = `rgba(251, 191, 36, ${opacity})`;
                                    ctx.lineWidth = 2.5;
                                    ctx.stroke();
                                    ctx.restore();
                                }
                            }
                        }
                    });

                    // Continue animation if there are benchmark failures (excluding failed status)
                    const hasBenchmarkFailures = benchmarkFailed.some((failed, index) =>
                        failed && !resultStatusFailed[index]
                    );
                    if (hasBenchmarkFailures) {
                        self.animationFrame = requestAnimationFrame(() => {
                            chart.render();
                        });
                    }
                }
            };

            // Plugin to draw vertical bands behind failed results
            const verticalBandsPlugin = {
                id: 'verticalBands',
                beforeDatasetsDraw: (chart) => {
                    const ctx = chart.ctx;
                    const chartArea = chart.chartArea;
                    const meta = chart.getDatasetMeta(0); // Use first dataset for positioning

                    // Only draw bands if we have failed results
                    const hasFailedResults = resultStatusFailed.some(failed => failed);
                    if (!hasFailedResults || !meta.data.length) {
                        return;
                    }

                    ctx.save();

                    // Set the fill color based on dark mode
                    const bandColor = isDarkMode ? 'rgba(239, 68, 68, 0.08)' : 'rgba(239, 68, 68, 0.06)';

                    meta.data.forEach((point, index) => {
                        if (resultStatusFailed[index]) {
                            const x = point.x;

                            // Calculate band width based on spacing between points
                            let bandWidth = 20; // Default width
                            if (meta.data.length > 1) {
                                if (index < meta.data.length - 1) {
                                    // Use spacing to next point
                                    const nextX = meta.data[index + 1].x;
                                    bandWidth = Math.abs(nextX - x) / 2;
                                } else if (index > 0) {
                                    // For last point, use spacing from previous point
                                    const prevX = meta.data[index - 1].x;
                                    bandWidth = Math.abs(x - prevX) / 2;
                                }
                            }

                            // Draw vertical band
                            ctx.fillStyle = bandColor;
                            ctx.fillRect(
                                x - bandWidth / 2,
                                chartArea.top,
                                bandWidth,
                                chartArea.bottom - chartArea.top
                            );
                        }
                    });

                    ctx.restore();
                }
            };

            this.chart = new Chart(this.$refs.canvas, {
                type: config.type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: config.label,
                        data: data,
                        borderColor: config.color,
                        backgroundColor: config.type === 'bar' ? config.color : getFillColor(config.color),
                        fill: false,
                        tension: 0.4,
                        borderWidth: isLine ? 3 : 1,
                        borderRadius: config.type === 'bar' ? 4 : 0,
                        barPercentage: 0.6,
                        categoryPercentage: 0.7,
                        pointRadius: pointRadii,
                        pointHoverRadius: pointHoverRadii,
                        pointBackgroundColor: pointColors,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: pointColors,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3,
                        segment: {
                            borderColor: ctx => {
                                // Skip drawing line if either point is a failed result
                                const index = ctx.p0DataIndex;
                                if (resultStatusFailed[index] || resultStatusFailed[index + 1]) {
                                    return 'rgba(0, 0, 0, 0)'; // Transparent - no line
                                }
                                return undefined; // Use default color
                            },
                            borderWidth: ctx => {
                                // Skip drawing line if either point is a failed result
                                const index = ctx.p0DataIndex;
                                if (resultStatusFailed[index] || resultStatusFailed[index + 1]) {
                                    return 0;
                                }
                                return undefined; // Use default width
                            }
                        }
                    }]
                },
                plugins: [verticalBandsPlugin, pulsingPointsPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            window.open('/admin/results', '_blank');
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const resultId = config.resultIds && config.resultIds[index];
                                    return resultId ? `Result #${resultId}` : '';
                                },
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const benchmark = config.benchmarks && config.benchmarks[index];
                                    const labels = [];

                                    // Check if this result has failed status
                                    const hasFailed = resultStatusFailed[index];

                                    // Show failed result indicator
                                    if (hasFailed) {
                                        labels.push('- Result Status: ❌ Failed');
                                        return labels;
                                    }

                                    // Extract metric name from config.label (e.g., "Ping (ms)" -> "Ping")
                                    const metricName = config.label.split('(')[0].trim();

                                    // Main result line with metric name
                                    let resultLabel = `- ${metricName}: `;
                                    if (context.parsed.y !== null) {
                                        resultLabel += context.parsed.y.toFixed(2) + ' ' + unit;
                                    }
                                    labels.push(resultLabel);

                                    // Benchmark info if available
                                    if (benchmark) {
                                        const thresholdType = benchmark.bar === 'min' ? 'Min' : 'Max';
                                        const benchmarkLabel = `- Benchmark (${thresholdType}): ${benchmark.value} ${benchmark.unit}`;
                                        labels.push(benchmarkLabel);

                                        const statusLabel = benchmark.passed ? '- Benchmark Status: ✅ Passed' : '- Benchmark Status: ❌ Failed';
                                        labels.push(statusLabel);
                                    }

                                    return labels;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: config.yAxisMin !== undefined ? config.yAxisMin : undefined,
                            max: config.yAxisMax !== undefined ? config.yAxisMax : undefined,
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value + ' ' + unit;
                                }
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                display: true,
                                autoSkip: true,
                                maxTicksLimit: 8,
                                maxRotation: 0,
                                minRotation: 0
                            },
                            grid: {
                                color: gridColor
                            }
                        }
                    }
                }
            });
        },

        updateChart(newData) {
            // Update benchmark failed data for this field (only if not packet loss)
            if (config.field !== 'packetLoss') {
                const benchmarkFailedField = config.field + 'BenchmarkFailed';
                config.benchmarkFailed = newData[benchmarkFailedField] || [];

                // Update full benchmark data for this field
                const benchmarksField = config.field + 'Benchmarks';
                config.benchmarks = newData[benchmarksField] || [];
            }

            // Update failed status data
            config.resultStatusFailed = newData.resultStatusFailed || [];

            // Update days difference
            config.daysDifference = newData.daysDifference || 0;

            this.createChart(newData.labels, newData[config.field]);
        }
    }));

    Alpine.data('multiLineChartComponent', (config) => ({
        chart: null,
        currentLabels: config.labels,
        currentDatasets: config.datasets,

        init() {
            this.createChart(config.labels, config.datasets);

            // Listen for theme changes and re-draw chart
            window.addEventListener('theme-changed', () => {
                // Small delay to allow DOM to update
                setTimeout(() => {
                    this.createChart(this.currentLabels, this.currentDatasets);
                }, 100);
            });
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
            }
        },

        createChart(labels, datasets) {
            // Store current data for theme changes
            this.currentLabels = labels;
            this.currentDatasets = datasets;

            if (this.chart) {
                this.chart.destroy();
            }

            const unit = config.unit || 'ms';
            const resultStatusFailed = config.resultStatusFailed || [];

            // Detect dark mode for text colors
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? 'rgb(228, 228, 231)' : 'rgb(39, 39, 42)';
            const gridColor = isDarkMode ? 'rgba(228, 228, 231, 0.1)' : 'rgba(0, 0, 0, 0.1)';

            // Convert dataset configs to Chart.js format
            const chartDatasets = datasets.map(dataset => {
                const color = dataset.color || 'rgb(59, 130, 246)';
                const fillColor = color.replace('rgb(', 'rgba(').replace(')', ', 0.1)');

                return {
                    label: dataset.label,
                    data: dataset.data,
                    borderColor: color,
                    backgroundColor: fillColor,
                    fill: false,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointBackgroundColor: color,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: color,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    segment: {
                        borderColor: ctx => {
                            // Skip drawing line if either point is a failed result
                            const index = ctx.p0DataIndex;
                            if (resultStatusFailed[index] || resultStatusFailed[index + 1]) {
                                return 'rgba(0, 0, 0, 0)'; // Transparent - no line
                            }
                            return undefined; // Use default color
                        },
                        borderWidth: ctx => {
                            // Skip drawing line if either point is a failed result
                            const index = ctx.p0DataIndex;
                            if (resultStatusFailed[index] || resultStatusFailed[index + 1]) {
                                return 0;
                            }
                            return undefined; // Use default width
                        }
                    }
                };
            });

            // Plugin to draw vertical bands behind failed results
            const verticalBandsPlugin = {
                id: 'verticalBands',
                beforeDatasetsDraw: (chart) => {
                    const ctx = chart.ctx;
                    const chartArea = chart.chartArea;
                    const meta = chart.getDatasetMeta(0); // Use first dataset for positioning

                    // Only draw bands if we have failed results
                    const hasFailedResults = resultStatusFailed.some(failed => failed);
                    if (!hasFailedResults || !meta.data.length) {
                        return;
                    }

                    ctx.save();

                    // Set the fill color based on dark mode
                    const bandColor = isDarkMode ? 'rgba(239, 68, 68, 0.08)' : 'rgba(239, 68, 68, 0.06)';

                    meta.data.forEach((point, index) => {
                        if (resultStatusFailed[index]) {
                            const x = point.x;

                            // Calculate band width based on spacing between points
                            let bandWidth = 20; // Default width
                            if (meta.data.length > 1) {
                                if (index < meta.data.length - 1) {
                                    // Use spacing to next point
                                    const nextX = meta.data[index + 1].x;
                                    bandWidth = Math.abs(nextX - x) / 2;
                                } else if (index > 0) {
                                    // For last point, use spacing from previous point
                                    const prevX = meta.data[index - 1].x;
                                    bandWidth = Math.abs(x - prevX) / 2;
                                }
                            }

                            // Draw vertical band
                            ctx.fillStyle = bandColor;
                            ctx.fillRect(
                                x - bandWidth / 2,
                                chartArea.top,
                                bandWidth,
                                chartArea.bottom - chartArea.top
                            );
                        }
                    });

                    ctx.restore();
                }
            };

            this.chart = new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: chartDatasets
                },
                plugins: [verticalBandsPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            window.open('/admin/results', '_blank');
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            displayColors: true,
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const resultId = config.resultIds && config.resultIds[index];
                                    return resultId ? `Result #${resultId}` : '';
                                },
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const hasFailed = resultStatusFailed[index];

                                    // Show failed result indicator
                                    if (hasFailed) {
                                        return '- Result Status: ❌ Failed';
                                    }

                                    let label = '- ' + (context.dataset.label || '');
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toFixed(2) + ' ' + unit;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value + ' ' + unit;
                                }
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                display: true,
                                autoSkip: true,
                                maxTicksLimit: 8,
                                maxRotation: 0,
                                minRotation: 0
                            },
                            grid: {
                                color: gridColor
                            }
                        }
                    }
                }
            });
        },

        updateChart(newData) {
            // Determine which datasets to use based on the original config
            // Check the number of datasets in the original config to identify chart type
            let datasets;

            // Check if this is the jitter chart (has 3 datasets) or latency chart (has 2 datasets)
            if (config.datasets.length === 3) {
                // Jitter chart - 3 datasets
                datasets = [
                    {
                        label: 'Download Jitter (ms)',
                        data: newData.downloadJitter || [],
                        color: 'rgb(59, 130, 246)',
                    },
                    {
                        label: 'Upload Jitter (ms)',
                        data: newData.uploadJitter || [],
                        color: 'rgb(245, 158, 11)',
                    },
                    {
                        label: 'Ping Jitter (ms)',
                        data: newData.pingJitter || [],
                        color: 'rgb(168, 85, 247)',
                    }
                ];
            } else {
                // Latency chart - 2 datasets
                datasets = [
                    {
                        label: 'Download Latency (ms)',
                        data: newData.downloadLatency || [],
                        color: 'rgb(59, 130, 246)',
                    },
                    {
                        label: 'Upload Latency (ms)',
                        data: newData.uploadLatency || [],
                        color: 'rgb(245, 158, 11)',
                    }
                ];
            }

            // Update failed status data
            config.resultStatusFailed = newData.resultStatusFailed || [];

            // Update days difference
            config.daysDifference = newData.daysDifference || 0;

            this.createChart(newData.labels, datasets);
        }
    }));

    Alpine.data('scatterChartComponent', (config) => ({
        chart: null,
        currentLabels: config.labels,
        currentData: config.data,

        init() {
            this.createChart(config.labels, config.data);

            // Listen for theme changes and re-draw chart
            window.addEventListener('theme-changed', () => {
                // Small delay to allow DOM to update
                setTimeout(() => {
                    this.createChart(this.currentLabels, this.currentData);
                }, 100);
            });
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
            }
        },

        createChart(labels, data) {
            // Store current data for theme changes
            this.currentLabels = labels;
            this.currentData = data;

            if (this.chart) {
                this.chart.destroy();
            }

            const resultStatusFailed = config.resultStatusFailed || [];

            // Detect dark mode for text colors
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? 'rgb(228, 228, 231)' : 'rgb(39, 39, 42)';
            const gridColor = isDarkMode ? 'rgba(228, 228, 231, 0.1)' : 'rgba(0, 0, 0, 0.1)';

            // Color mapping based on packet loss severity
            const getPointColor = (value) => {
                if (value === 0) return 'rgb(34, 197, 94)'; // Green - excellent
                if (value <= 1) return 'rgb(234, 179, 8)'; // Yellow - fair
                if (value <= 5) return 'rgb(249, 115, 22)'; // Orange - poor
                return 'rgb(239, 68, 68)'; // Red - critical
            };

            // Create point colors based on packet loss values
            const pointColors = data.map((value, index) => {
                if (resultStatusFailed[index]) return 'rgb(156, 163, 175)'; // Gray for failed results
                return getPointColor(value);
            });

            // All points are visible in scatter plot
            const pointRadii = data.map(() => 3);
            const pointHoverRadii = data.map(() => 5);

            // Plugin to draw vertical bands behind failed results
            const verticalBandsPlugin = {
                id: 'verticalBands',
                beforeDatasetsDraw: (chart) => {
                    const ctx = chart.ctx;
                    const chartArea = chart.chartArea;
                    const meta = chart.getDatasetMeta(0);

                    // Only draw bands if we have failed results
                    const hasFailedResults = resultStatusFailed.some(failed => failed);
                    if (!hasFailedResults || !meta.data.length) {
                        return;
                    }

                    ctx.save();

                    // Set the fill color based on dark mode
                    const bandColor = isDarkMode ? 'rgba(239, 68, 68, 0.08)' : 'rgba(239, 68, 68, 0.06)';

                    meta.data.forEach((point, index) => {
                        if (resultStatusFailed[index]) {
                            const x = point.x;

                            // Calculate band width based on spacing between points
                            let bandWidth = 20; // Default width
                            if (meta.data.length > 1) {
                                if (index < meta.data.length - 1) {
                                    const nextX = meta.data[index + 1].x;
                                    bandWidth = Math.abs(nextX - x) / 2;
                                } else if (index > 0) {
                                    const prevX = meta.data[index - 1].x;
                                    bandWidth = Math.abs(x - prevX) / 2;
                                }
                            }

                            // Draw vertical band
                            ctx.fillStyle = bandColor;
                            ctx.fillRect(
                                x - bandWidth / 2,
                                chartArea.top,
                                bandWidth,
                                chartArea.bottom - chartArea.top
                            );
                        }
                    });

                    ctx.restore();
                }
            };

            this.chart = new Chart(this.$refs.canvas, {
                type: 'scatter',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Packet Loss (%)',
                        data: data.map((value, index) => ({ x: index, y: value })),
                        backgroundColor: pointColors,
                        borderColor: pointColors.map(color => color.replace('rgb(', 'rgba(').replace(')', ', 0.8)')),
                        borderWidth: 2,
                        pointRadius: pointRadii,
                        pointHoverRadius: pointHoverRadii,
                        pointHoverBorderWidth: 3,
                        pointHoverBorderColor: '#fff',
                    }]
                },
                plugins: [verticalBandsPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            window.open('/admin/results', '_blank');
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].parsed.x;
                                    const resultId = config.resultIds && config.resultIds[index];
                                    return resultId ? `Result #${resultId}` : '';
                                },
                                label: function(context) {
                                    const index = context.parsed.x;
                                    const labels = [];

                                    // Check if this result has failed status
                                    const hasFailed = resultStatusFailed[index];

                                    // Show failed result indicator
                                    if (hasFailed) {
                                        labels.push('- Result Status: ❌ Failed');
                                        return labels;
                                    }

                                    // Main result line
                                    let resultLabel = '- Packet Loss: ';
                                    if (context.parsed.y !== null) {
                                        resultLabel += context.parsed.y.toFixed(2) + '%';
                                    }
                                    labels.push(resultLabel);

                                    return labels;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            min: 0,
                            max: Math.max(0, data.length - 1),
                            ticks: {
                                color: textColor,
                                display: true,
                                stepSize: 1,
                                callback: function(value) {
                                    // Show the corresponding label from labels array
                                    const index = Math.round(value);
                                    if (labels[index]) {
                                        return labels[index];
                                    }
                                    return '';
                                },
                                autoSkip: true,
                                maxTicksLimit: 8,
                                maxRotation: 0,
                                minRotation: 0
                            },
                            grid: {
                                color: gridColor
                            }
                        }
                    }
                }
            });
        },

        updateChart(newData) {
            // Update failed status data
            config.resultStatusFailed = newData.resultStatusFailed || [];
            config.resultIds = newData.resultIds || [];

            this.createChart(newData.labels, newData.packetLoss);
        }
    }));

    Alpine.data('speedChartComponent', (config) => ({
        chart: null,
        animationFrame: null,
        currentLabels: config.labels,
        currentDownloadData: config.downloadData,
        currentUploadData: config.uploadData,

        init() {
            this.createChart(config.labels, config.downloadData, config.uploadData);

            // Listen for theme changes and re-draw chart
            window.addEventListener('theme-changed', () => {
                // Small delay to allow DOM to update
                setTimeout(() => {
                    this.createChart(this.currentLabels, this.currentDownloadData, this.currentUploadData);
                }, 100);
            });
        },

        destroy() {
            if (this.animationFrame) {
                cancelAnimationFrame(this.animationFrame);
            }
            if (this.chart) {
                this.chart.destroy();
            }
        },

        createChart(labels, downloadData, uploadData) {
            // Store current data for theme changes
            this.currentLabels = labels;
            this.currentDownloadData = downloadData;
            this.currentUploadData = uploadData;

            if (this.chart) {
                this.chart.destroy();
            }

            const downloadBenchmarkFailed = config.downloadBenchmarkFailed || [];
            const uploadBenchmarkFailed = config.uploadBenchmarkFailed || [];
            const resultStatusFailed = config.resultStatusFailed || [];
            const failedColor = 'rgb(239, 68, 68)'; // Red color for failed results
            const daysDifference = config.daysDifference || 0;
            const showRingingPoints = daysDifference <= 8;

            // Detect dark mode for text colors
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? 'rgb(228, 228, 231)' : 'rgb(39, 39, 42)';
            const gridColor = isDarkMode ? 'rgba(228, 228, 231, 0.1)' : 'rgba(0, 0, 0, 0.1)';

            // Convert rgb() to rgba() with opacity for fill
            const getFillColor = (color, opacity = 0.2) => {
                return color.replace('rgb(', 'rgba(').replace(')', `, ${opacity})`);
            };

            // Create point colors and radii for download
            const downloadPointColors = downloadData.map((_, index) =>
                resultStatusFailed[index] ? failedColor : config.downloadColor
            );
            const downloadPointRadii = downloadData.map((_, index) => {
                if (!showRingingPoints) return 0; // Hide all points if date range > 8 days
                if (resultStatusFailed[index]) return 5;
                if (downloadBenchmarkFailed[index]) return 5;
                return 0;
            });

            // Create point colors and radii for upload
            const uploadPointColors = uploadData.map((_, index) =>
                resultStatusFailed[index] ? failedColor : config.uploadColor
            );
            const uploadPointRadii = uploadData.map((_, index) => {
                if (!showRingingPoints) return 0; // Hide all points if date range > 8 days
                if (resultStatusFailed[index]) return 5;
                if (uploadBenchmarkFailed[index]) return 5;
                return 0;
            });

            // Plugin to draw vertical bands behind failed results
            const verticalBandsPlugin = {
                id: 'verticalBands',
                beforeDatasetsDraw: (chart) => {
                    const ctx = chart.ctx;
                    const chartArea = chart.chartArea;
                    const meta = chart.getDatasetMeta(0); // Use first dataset for positioning

                    // Only draw bands if we have failed results
                    const hasFailedResults = resultStatusFailed.some(failed => failed);
                    if (!hasFailedResults || !meta.data.length) {
                        return;
                    }

                    ctx.save();

                    // Set the fill color based on dark mode
                    const bandColor = isDarkMode ? 'rgba(239, 68, 68, 0.08)' : 'rgba(239, 68, 68, 0.06)';

                    meta.data.forEach((point, index) => {
                        if (resultStatusFailed[index]) {
                            const x = point.x;

                            // Calculate band width based on spacing between points
                            let bandWidth = 20; // Default width
                            if (meta.data.length > 1) {
                                if (index < meta.data.length - 1) {
                                    // Use spacing to next point
                                    const nextX = meta.data[index + 1].x;
                                    bandWidth = Math.abs(nextX - x) / 2;
                                } else if (index > 0) {
                                    // For last point, use spacing from previous point
                                    const prevX = meta.data[index - 1].x;
                                    bandWidth = Math.abs(x - prevX) / 2;
                                }
                            }

                            // Draw vertical band
                            ctx.fillStyle = bandColor;
                            ctx.fillRect(
                                x - bandWidth / 2,
                                chartArea.top,
                                bandWidth,
                                chartArea.bottom - chartArea.top
                            );
                        }
                    });

                    ctx.restore();
                }
            };

            // Plugin to create ping/ripple effect on failed benchmark points and failed status results
            const self = this;
            const pulsingPointsPlugin = {
                id: 'pulsingPoints',
                afterDatasetsDraw: (chart) => {
                    // Only show ringing animation if date range is 8 days or less
                    if (!showRingingPoints) {
                        return;
                    }

                    const ctx = chart.ctx;
                    const time = Date.now();

                    // Process download dataset (index 0)
                    const downloadMeta = chart.getDatasetMeta(0);
                    downloadMeta.data.forEach((point, index) => {
                        // Show ringing for both failed status and benchmark failures
                        const shouldShowRinging = resultStatusFailed[index] || downloadBenchmarkFailed[index];

                        if (shouldShowRinging) {
                            const x = point.x;
                            const y = point.y;

                            // Create multiple ping rings at different stages
                            const pingDuration = 2000;
                            const maxRadius = 25;
                            const numberOfRings = 3;

                            // Use red color for failed status, download color for failed benchmark
                            const ringBaseColor = resultStatusFailed[index] ? failedColor : config.downloadColor;

                            for (let i = 0; i < numberOfRings; i++) {
                                const offset = (pingDuration / numberOfRings) * i;
                                const progress = ((time + offset) % pingDuration) / pingDuration;
                                const radius = progress * maxRadius;
                                const opacity = Math.max(0, 0.6 * (1 - progress));

                                if (opacity > 0.05) {
                                    ctx.save();
                                    ctx.beginPath();
                                    ctx.arc(x, y, radius, 0, 2 * Math.PI);
                                    const downloadRingColor = ringBaseColor.replace('rgb(', 'rgba(').replace(')', `, ${opacity})`);
                                    ctx.strokeStyle = downloadRingColor;
                                    ctx.lineWidth = 2.5;
                                    ctx.stroke();
                                    ctx.restore();
                                }
                            }
                        }
                    });

                    // Process upload dataset (index 1)
                    const uploadMeta = chart.getDatasetMeta(1);
                    uploadMeta.data.forEach((point, index) => {
                        // Show ringing for both failed status and benchmark failures
                        const shouldShowRinging = resultStatusFailed[index] || uploadBenchmarkFailed[index];

                        if (shouldShowRinging) {
                            const x = point.x;
                            const y = point.y;

                            // Create multiple ping rings at different stages
                            const pingDuration = 2000;
                            const maxRadius = 25;
                            const numberOfRings = 3;

                            // Use red color for failed status, upload color for failed benchmark
                            const ringBaseColor = resultStatusFailed[index] ? failedColor : config.uploadColor;

                            for (let i = 0; i < numberOfRings; i++) {
                                const offset = (pingDuration / numberOfRings) * i;
                                const progress = ((time + offset) % pingDuration) / pingDuration;
                                const radius = progress * maxRadius;
                                const opacity = Math.max(0, 0.6 * (1 - progress));

                                if (opacity > 0.05) {
                                    ctx.save();
                                    ctx.beginPath();
                                    ctx.arc(x, y, radius, 0, 2 * Math.PI);
                                    const uploadRingColor = ringBaseColor.replace('rgb(', 'rgba(').replace(')', `, ${opacity})`);
                                    ctx.strokeStyle = uploadRingColor;
                                    ctx.lineWidth = 2.5;
                                    ctx.stroke();
                                    ctx.restore();
                                }
                            }
                        }
                    });

                    // Continue animation if there are any failures (already checked showRingingPoints at the start)
                    const hasFailures = resultStatusFailed.some(failed => failed) ||
                        downloadBenchmarkFailed.some(failed => failed) ||
                        uploadBenchmarkFailed.some(failed => failed);

                    if (hasFailures) {
                        self.animationFrame = requestAnimationFrame(() => {
                            chart.render();
                        });
                    }
                }
            };

            this.chart = new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Download (Mbps)',
                            data: downloadData,
                            borderColor: config.downloadColor,
                            backgroundColor: getFillColor(config.downloadColor),
                            fill: false,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: downloadPointRadii,
                            pointHoverRadius: 7,
                            pointBackgroundColor: downloadPointColors,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: downloadPointColors,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3,
                            yAxisID: 'y',
                            segment: {
                                borderColor: ctx => {
                                    // Skip drawing line if either point is a failed result
                                    const index = ctx.p0DataIndex;
                                    if (resultStatusFailed[index] || resultStatusFailed[index + 1]) {
                                        return 'rgba(0, 0, 0, 0)'; // Transparent - no line
                                    }
                                    return undefined; // Use default color
                                },
                                borderWidth: ctx => {
                                    // Skip drawing line if either point is a failed result
                                    const index = ctx.p0DataIndex;
                                    if (resultStatusFailed[index] || resultStatusFailed[index + 1]) {
                                        return 0;
                                    }
                                    return undefined; // Use default width
                                }
                            }
                        },
                        {
                            label: 'Upload (Mbps)',
                            data: uploadData,
                            borderColor: config.uploadColor,
                            backgroundColor: getFillColor(config.uploadColor),
                            fill: false,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: uploadPointRadii,
                            pointHoverRadius: 7,
                            pointBackgroundColor: uploadPointColors,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: uploadPointColors,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3,
                            yAxisID: 'y1',
                            segment: {
                                borderColor: ctx => {
                                    // Skip drawing line if either point is a failed result
                                    const index = ctx.p0DataIndex;
                                    if (resultStatusFailed[index] || resultStatusFailed[index + 1]) {
                                        return 'rgba(0, 0, 0, 0)'; // Transparent - no line
                                    }
                                    return undefined; // Use default color
                                },
                                borderWidth: ctx => {
                                    // Skip drawing line if either point is a failed result
                                    const index = ctx.p0DataIndex;
                                    if (resultStatusFailed[index] || resultStatusFailed[index + 1]) {
                                        return 0;
                                    }
                                    return undefined; // Use default width
                                }
                            }
                        }
                    ]
                },
                plugins: [verticalBandsPlugin, pulsingPointsPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            window.open('/admin/results', '_blank');
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            displayColors: true,
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const resultId = config.resultIds && config.resultIds[index];
                                    return resultId ? `Result #${resultId}` : '';
                                },
                                label: function(context) {
                                    const datasetIndex = context.datasetIndex;
                                    const index = context.dataIndex;
                                    const labels = [];

                                    // Check if this result has failed status
                                    const hasFailed = resultStatusFailed[index];

                                    // Show failed result indicator
                                    if (hasFailed) {
                                        labels.push('- Result Status: ❌ Failed');
                                    }

                                    // Determine which dataset (download or upload)
                                    const isDownload = datasetIndex === 0;
                                    const benchmark = isDownload
                                        ? (config.downloadBenchmarks && config.downloadBenchmarks[index])
                                        : (config.uploadBenchmarks && config.uploadBenchmarks[index]);

                                    // Main result line with specific label (skip if failed)
                                    if (!hasFailed) {
                                        let resultLabel = isDownload ? '- Download: ' : '- Upload: ';
                                        if (context.parsed.y !== null) {
                                            resultLabel += context.parsed.y.toFixed(2) + ' Mbps';
                                        }
                                        labels.push(resultLabel);
                                    }

                                    // Benchmark info if available (skip if failed)
                                    if (!hasFailed && benchmark) {
                                        const thresholdType = benchmark.bar === 'min' ? 'Min' : 'Max';
                                        const benchmarkLabel = `- Benchmark (${thresholdType}): ${benchmark.value} ${benchmark.unit}`;
                                        labels.push(benchmarkLabel);

                                        const statusLabel = benchmark.passed ? '- Benchmark Status: ✅ Passed' : '- Benchmark Status: ❌ Failed';
                                        labels.push(statusLabel);
                                    }

                                    return labels;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: false,
                                text: 'Download',
                                color: config.downloadColor,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value + ' Mbps';
                                }
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: false,
                                text: 'Upload',
                                color: config.uploadColor,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value + ' Mbps';
                                }
                            },
                            grid: {
                                drawOnChartArea: false, // Only want the grid lines for one axis to show up
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                display: true,
                                autoSkip: true,
                                maxTicksLimit: 8,
                                maxRotation: 0,
                                minRotation: 0
                            },
                            grid: {
                                color: gridColor
                            }
                        }
                    }
                }
            });
        },

        updateChart(newData) {
            // Update benchmark failed data
            config.downloadBenchmarkFailed = newData.downloadBenchmarkFailed || [];
            config.uploadBenchmarkFailed = newData.uploadBenchmarkFailed || [];

            // Update full benchmark data
            config.downloadBenchmarks = newData.downloadBenchmarks || [];
            config.uploadBenchmarks = newData.uploadBenchmarks || [];

            // Update failed status data
            config.resultStatusFailed = newData.resultStatusFailed || [];

            // Update days difference
            config.daysDifference = newData.daysDifference || 0;

            this.createChart(newData.labels, newData.download, newData.upload);
        }
    }));

    // Section Manager for Filter Modal
    Alpine.data('sectionManager', () => ({
        sections: [],
        draggingIndex: null,
        dragOverIndex: null,

        init() {
            this.loadSections();

            window.addEventListener('sections-reset', () => {
                this.loadSections();
            });
        },

        loadSections() {
            const prefs = this.getPreferences();
            const sectionDefinitions = [
                { id: 'speed', name: 'Speed' },
                { id: 'ping', name: 'Ping' },
                { id: 'latency', name: 'Latency (IQM)' },
                { id: 'jitter', name: 'Jitter' },
                { id: 'packetLoss', name: 'Packet Loss' }
            ];

            // Apply saved order if it exists
            let orderedDefinitions = sectionDefinitions;
            if (prefs.sectionOrder && Array.isArray(prefs.sectionOrder)) {
                orderedDefinitions = prefs.sectionOrder
                    .map(id => sectionDefinitions.find(def => def.id === id))
                    .filter(def => def !== undefined);

                // Add any new sections that aren't in the saved order
                const missingDefs = sectionDefinitions.filter(
                    def => !prefs.sectionOrder.includes(def.id)
                );
                orderedDefinitions = [...orderedDefinitions, ...missingDefs];
            }

            this.sections = orderedDefinitions.map(def => ({
                ...def,
                visible: !prefs.hiddenSections.includes(def.id)
            }));
        },

        getPreferences() {
            const defaultPrefs = {
                hiddenSections: [],
                sectionOrder: ['speed', 'ping', 'latency', 'jitter', 'packetLoss'],
                version: 2
            };

            try {
                const stored = localStorage.getItem('metrics-dashboard-preferences');
                if (!stored) return defaultPrefs;

                const parsed = JSON.parse(stored);

                // Validate structure
                if (!Array.isArray(parsed.hiddenSections)) {
                    console.warn('Invalid dashboard preferences structure, using defaults');
                    return defaultPrefs;
                }

                // Migrate from v1 to v2 if needed
                if (!parsed.sectionOrder) {
                    parsed.sectionOrder = defaultPrefs.sectionOrder;
                    parsed.version = 2;
                }

                return parsed;
            } catch (e) {
                console.error('Error loading dashboard preferences:', e);
                return defaultPrefs;
            }
        },

        savePreferences(prefs) {
            try {
                localStorage.setItem('metrics-dashboard-preferences', JSON.stringify(prefs));
                // Dispatch event to update dashboard
                window.dispatchEvent(new CustomEvent('dashboard-preferences-changed', {
                    detail: prefs
                }));
            } catch (e) {
                console.error('Error saving dashboard preferences:', e);
            }
        },

        updateVisibility() {
            const prefs = this.getPreferences();
            prefs.hiddenSections = this.sections
                .filter(s => !s.visible)
                .map(s => s.id);
            prefs.sectionOrder = this.sections.map(s => s.id);
            this.savePreferences(prefs);
        },

        updateOrder() {
            const prefs = this.getPreferences();
            prefs.sectionOrder = this.sections.map(s => s.id);
            this.savePreferences(prefs);
        },

        dragStart(index, event) {
            this.draggingIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', event.target);
        },

        dragEnd(event) {
            this.draggingIndex = null;
            this.dragOverIndex = null;
        },

        dragOver(index, event) {
            if (this.draggingIndex !== null && this.draggingIndex !== index) {
                this.dragOverIndex = index;
            }
        },

        dragLeave(event) {
            this.dragOverIndex = null;
        },

        drop(index, event) {
            event.preventDefault();

            if (this.draggingIndex === null || this.draggingIndex === index) {
                this.draggingIndex = null;
                this.dragOverIndex = null;
                return;
            }

            // Reorder the sections array
            const draggedSection = this.sections[this.draggingIndex];
            const newSections = [...this.sections];
            newSections.splice(this.draggingIndex, 1);
            newSections.splice(index, 0, draggedSection);

            this.sections = newSections;
            this.updateOrder();

            this.draggingIndex = null;
            this.dragOverIndex = null;
        },

        resetToDefaults() {
            const defaultPrefs = {
                hiddenSections: [],
                sectionOrder: ['speed', 'ping', 'latency', 'jitter', 'packetLoss'],
                version: 2
            };

            this.savePreferences(defaultPrefs);
            this.loadSections();

            // Dispatch reset event
            window.dispatchEvent(new CustomEvent('sections-reset'));
        }
    }));

    // Dashboard Sections Component
    Alpine.data('dashboardSections', () => ({
        visibleSections: [],
        preferences: null,

        init() {
            this.loadPreferences();

            // Listen for reset events
            window.addEventListener('sections-reset', () => {
                this.loadPreferences();
            });

            // Listen for preference changes
            window.addEventListener('dashboard-preferences-changed', () => {
                this.loadPreferences();
            });
        },

        loadPreferences() {
            const defaultPrefs = {
                hiddenSections: [],
                sectionOrder: ['speed', 'ping', 'latency', 'jitter', 'packetLoss'],
                version: 2
            };

            try {
                const stored = localStorage.getItem('metrics-dashboard-preferences');
                this.preferences = stored ? JSON.parse(stored) : defaultPrefs;

                // Validate and fix if needed
                if (!Array.isArray(this.preferences.hiddenSections)) {
                    this.preferences = defaultPrefs;
                }

                // Migrate from v1 to v2 if needed
                if (!this.preferences.sectionOrder) {
                    this.preferences.sectionOrder = defaultPrefs.sectionOrder;
                    this.preferences.version = 2;
                }

                this.updateVisibleSections();
            } catch (e) {
                console.error('Error loading preferences:', e);
                this.preferences = defaultPrefs;
                this.updateVisibleSections();
            }
        },

        updateVisibleSections() {
            // Use the saved order and filter out hidden sections
            this.visibleSections = this.preferences.sectionOrder.filter(
                id => !this.preferences.hiddenSections.includes(id)
            );
        }
    }));
</script>
@endscript
