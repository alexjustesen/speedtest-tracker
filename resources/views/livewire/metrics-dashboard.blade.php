<div class="flex h-full w-full flex-1 flex-col gap-6">
    <livewire:next-speedtest-banner />

    <div class="flex items-center justify-between">
        <flux:heading size="xl" class="flex items-center gap-2">
            <flux:icon.chart-no-axes-combined class="size-5" />
            Metrics Dashboard
        </flux:heading>

        <div>
            <flux:modal.trigger name="filterModal">
                <flux:button size="sm" icon="adjustments-horizontal" />
            </flux:modal.trigger>

            <!-- Filter Modal -->
            <flux:modal name="filterModal" class="md:w-md space-y-6">
                <div>
                    <flux:heading size="lg">Filter Results</flux:heading>
                    <flux:text>Select a date range to filter your speed test results</flux:text>
                </div>

                <flux:separator />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Start Date</flux:label>
                        <flux:input
                            wire:model="startDate"
                            type="date"
                            max="{{ now()->format('Y-m-d') }}"
                            placeholder="Select start date" />
                        <flux:error name="startDate" />
                    </flux:field>

                    <flux:field>
                        <flux:label>End Date</flux:label>
                        <flux:input
                            wire:model="endDate"
                            type="date"
                            max="{{ now()->format('Y-m-d') }}"
                            placeholder="Select end date" />
                        <flux:error name="endDate" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Scheduled Status</flux:label>
                    <flux:select wire:model="scheduledFilter">
                        <flux:select.option value="all">All</flux:select.option>
                        <flux:select.option value="scheduled">Only scheduled</flux:select.option>
                        <flux:select.option value="unscheduled">Only unscheduled</flux:select.option>
                    </flux:select>
                </flux:field>

                <div class="flex justify-between items-center gap-2">
                    <div class="flex gap-2">
                        <flux:button
                            wire:click="applyFilters"
                            variant="primary">
                            Apply
                        </flux:button>

                        <flux:button
                            wire:click="resetFilters"
                            variant="ghost">
                            Reset
                        </flux:button>
                    </div>

                    <flux:modal.close>
                        <flux:button variant="ghost">
                            Cancel
                        </flux:button>
                    </flux:modal.close>
                </div>
            </flux:modal>
        </div>
    </div>

    <!-- Data Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Download & Upload Speed Comparison -->
        <div class="col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
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
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[2/1] lg:aspect-[4/1] px-6 py-4"
            >
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Speed Stats -->
            <div class="border-t border-neutral-200 dark:border-neutral-700">
                <!-- Download Stats -->
                <div class="border-b border-neutral-200 dark:border-neutral-700">
                    <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-blue-600 dark:text-blue-400">
                        <flux:icon.download class="size-4" />
                        Download
                    </flux:heading>

                    <div class="divide-x divide-neutral-200 grid grid-cols-2 lg:grid-cols-6 dark:divide-neutral-700 border-t border-neutral-200 dark:border-neutral-700">
                        <div class="px-6 py-3">
                            <flux:heading class="flex items-center gap-1.5">
                                @if($chartData['downloadStats']['latestFailed'] && $chartData['downloadStats']['latestBenchmark'])
                                    <flux:tooltip content="Benchmark Failed: {{ $chartData['downloadStats']['latestBenchmark']['bar'] === 'min' ? 'Min' : 'Max' }} {{ $chartData['downloadStats']['latestBenchmark']['value'] }} {{ $chartData['downloadStats']['latestBenchmark']['unit'] }}">
                                        <flux:icon.triangle-alert class="size-4 text-amber-500" />
                                    </flux:tooltip>
                                @endif
                                Latest
                            </flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['downloadStats']['latest'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>Average</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['downloadStats']['average'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>P95</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['downloadStats']['p95'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>Maximum</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['downloadStats']['maximum'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>Minimum</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['downloadStats']['minimum'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>Healthy</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['downloadStats']['healthy'], 1) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Stats -->
                <div>
                    <flux:heading size="sm" class="flex items-center gap-2 px-6 py-2 text-amber-600 dark:text-amber-400">
                        <flux:icon.upload class="size-4" />
                        Upload
                    </flux:heading>

                    <div class="divide-x divide-neutral-200 grid grid-cols-2 lg:grid-cols-6 dark:divide-neutral-700 border-t border-neutral-200 dark:border-neutral-700">
                        <div class="px-6 py-3">
                            <flux:heading class="flex items-center gap-1.5">
                                @if($chartData['uploadStats']['latestFailed'] && $chartData['uploadStats']['latestBenchmark'])
                                    <flux:tooltip content="Benchmark Failed: {{ $chartData['uploadStats']['latestBenchmark']['bar'] === 'min' ? 'Min' : 'Max' }} {{ $chartData['uploadStats']['latestBenchmark']['value'] }} {{ $chartData['uploadStats']['latestBenchmark']['unit'] }}">
                                        <flux:icon.triangle-alert class="size-4 text-amber-500" />
                                    </flux:tooltip>
                                @endif
                                Latest
                            </flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['uploadStats']['latest'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>Average</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['uploadStats']['average'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>P95</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['uploadStats']['p95'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>Maximum</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['uploadStats']['maximum'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>Minimum</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['uploadStats']['minimum'], 2) }} Mbps
                            </div>
                        </div>
                        <div class="px-6 py-3">
                            <flux:heading>Healthy</flux:heading>
                            <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['uploadStats']['healthy'], 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ping Data -->
        <div class="col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
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
                    color: 'rgb(168, 85, 247)',
                    field: 'ping',
                    showPoints: true,
                    unit: 'ms'
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[3/1] lg:aspect-[5/1] px-6 py-4"
            >
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Ping Stats -->
            <div class="divide-x divide-neutral-200 grid grid-cols-2 lg:grid-cols-6 border-t border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                <div class="px-6 py-3">
                    <flux:heading class="flex items-center gap-1.5">
                        @if($chartData['pingStats']['latestFailed'] && $chartData['pingStats']['latestBenchmark'])
                            <flux:tooltip content="Benchmark Failed: {{ $chartData['pingStats']['latestBenchmark']['bar'] === 'min' ? 'Min' : 'Max' }} {{ $chartData['pingStats']['latestBenchmark']['value'] }} {{ $chartData['pingStats']['latestBenchmark']['unit'] }}">
                                <flux:icon.triangle-alert class="size-4 text-amber-500" />
                            </flux:tooltip>
                        @endif
                        Latest
                    </flux:heading>
                    <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($chartData['pingStats']['latest'], 2) }} ms
                    </div>
                </div>
                <div class="px-6 py-3">
                    <flux:heading>Average</flux:heading>
                    <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($chartData['pingStats']['average'], 2) }} ms
                    </div>
                </div>
                <div class="px-6 py-3">
                    <flux:heading>P95</flux:heading>
                    <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($chartData['pingStats']['p95'], 2) }} ms
                    </div>
                </div>
                <div class="px-6 py-3">
                    <flux:heading>Maximum</flux:heading>
                    <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($chartData['pingStats']['maximum'], 2) }} ms
                    </div>
                </div>
                <div class="px-6 py-3">
                    <flux:heading>Minimum</flux:heading>
                    <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($chartData['pingStats']['minimum'], 2) }} ms
                    </div>
                </div>
                <div class="px-6 py-3">
                    <flux:heading>Healthy</flux:heading>
                    <div class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ number_format($chartData['pingStats']['healthy'], 1) }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Latency Data -->
        <div class="col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                <flux:icon.activity class="size-5 text-neutral-600 dark:text-neutral-400" />
                Latency (IQM)
            </flux:heading>

            <!-- Latency Chart -->
            <div
                x-data="multiLineChartComponent({
                    labels: @js($chartData['labels']),
                    resultIds: @js($chartData['resultIds']),
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
                    unit: 'ms'
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[2/1] lg:aspect-[5/1] px-6 py-4"
            >
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Latency Stats -->
            <div class="divide-x divide-neutral-200 grid grid-cols-1 lg:grid-cols-2 border-t border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                <!-- Download Latency Stats -->
                <div class="px-6 py-4">
                    <flux:heading size="sm" class="mb-3 text-blue-600 dark:text-blue-400">Download Latency</flux:heading>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <flux:heading>Latest</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['latencyStats']['downloadLatest'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>Maximum</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['latencyStats']['downloadMaximum'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>Minimum</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['latencyStats']['downloadMinimum'], 2) }} ms
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Latency Stats -->
                <div class="px-6 py-4">
                    <flux:heading size="sm" class="mb-3 text-amber-600 dark:text-amber-400">Upload Latency</flux:heading>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <flux:heading>Latest</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['latencyStats']['uploadLatest'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>Maximum</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['latencyStats']['uploadMaximum'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>Minimum</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['latencyStats']['uploadMinimum'], 2) }} ms
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jitter Data -->
        <div class="col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                <flux:icon.coffee class="size-5 text-neutral-600 dark:text-neutral-400" />
                Jitter
            </flux:heading>

            <!-- Jitter Chart -->
            <div
                x-data="multiLineChartComponent({
                    labels: @js($chartData['labels']),
                    resultIds: @js($chartData['resultIds']),
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
                    unit: 'ms'
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[2/1] lg:aspect-[5/1] px-6 py-4"
            >
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Jitter Stats -->
            <div class="divide-x divide-neutral-200 grid grid-cols-1 lg:grid-cols-3 border-t border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                <!-- Download Jitter Stats -->
                <div class="px-6 py-4">
                    <flux:heading size="sm" class="mb-3 text-blue-600 dark:text-blue-400">Download Jitter</flux:heading>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <flux:heading>Latest</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['downloadLatest'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>Average</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['downloadAverage'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>P95</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['downloadP95'], 2) }} ms
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Jitter Stats -->
                <div class="px-6 py-4">
                    <flux:heading size="sm" class="mb-3 text-amber-600 dark:text-amber-400">Upload Jitter</flux:heading>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <flux:heading>Latest</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['uploadLatest'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>Average</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['uploadAverage'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>P95</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['uploadP95'], 2) }} ms
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ping Jitter Stats -->
                <div class="px-6 py-4">
                    <flux:heading size="sm" class="mb-3 text-purple-600 dark:text-purple-400">Ping Jitter</flux:heading>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <flux:heading>Latest</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['pingLatest'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>Average</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['pingAverage'], 2) }} ms
                            </div>
                        </div>
                        <div>
                            <flux:heading>P95</flux:heading>
                            <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ number_format($chartData['jitterStats']['pingP95'], 2) }} ms
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
            const amberColor = 'rgb(251, 191, 36)'; // Amber for failed benchmarks

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
            const pointColors = data.map((_, index) =>
                benchmarkFailed[index] ? amberColor : config.color
            );

            const pointRadii = data.map((_, index) =>
                benchmarkFailed[index] ? 5 : 0
            );

            // Plugin to create ping/ripple effect on failed benchmark points
            const self = this;
            const pulsingPointsPlugin = {
                id: 'pulsingPoints',
                afterDatasetsDraw: (chart) => {
                    const ctx = chart.ctx;
                    const meta = chart.getDatasetMeta(0);
                    const time = Date.now();

                    meta.data.forEach((point, index) => {
                        if (benchmarkFailed[index]) {
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

                    // Continue animation if there are failed benchmarks
                    if (benchmarkFailed.some(failed => failed)) {
                        self.animationFrame = requestAnimationFrame(() => {
                            chart.render();
                        });
                    }
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
                        pointHoverRadius: 7,
                        pointBackgroundColor: pointColors,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: pointColors,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3,
                    }]
                },
                plugins: [pulsingPointsPlugin],
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
                };
            });

            this.chart = new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: chartDatasets
                },
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
            // Determine which datasets to use based on current config
            let datasets;

            // Check if this is the latency chart (has downloadLatency)
            if (newData.downloadLatency !== undefined) {
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
            } else {
                // Jitter chart
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
            }

            this.createChart(newData.labels, datasets);
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
                config.downloadColor
            );
            const downloadPointRadii = downloadData.map((_, index) =>
                downloadBenchmarkFailed[index] ? 5 : 0
            );

            // Create point colors and radii for upload
            const uploadPointColors = uploadData.map((_, index) =>
                config.uploadColor
            );
            const uploadPointRadii = uploadData.map((_, index) =>
                uploadBenchmarkFailed[index] ? 5 : 0
            );

            // Plugin to create ping/ripple effect on failed benchmark points
            const self = this;
            const pulsingPointsPlugin = {
                id: 'pulsingPoints',
                afterDatasetsDraw: (chart) => {
                    const ctx = chart.ctx;
                    const time = Date.now();

                    // Process download dataset (index 0)
                    const downloadMeta = chart.getDatasetMeta(0);
                    downloadMeta.data.forEach((point, index) => {
                        if (downloadBenchmarkFailed[index]) {
                            const x = point.x;
                            const y = point.y;

                            // Create multiple ping rings at different stages
                            const pingDuration = 2000;
                            const maxRadius = 25;
                            const numberOfRings = 3;

                            for (let i = 0; i < numberOfRings; i++) {
                                const offset = (pingDuration / numberOfRings) * i;
                                const progress = ((time + offset) % pingDuration) / pingDuration;
                                const radius = progress * maxRadius;
                                const opacity = Math.max(0, 0.6 * (1 - progress));

                                if (opacity > 0.05) {
                                    ctx.save();
                                    ctx.beginPath();
                                    ctx.arc(x, y, radius, 0, 2 * Math.PI);
                                    const downloadRingColor = config.downloadColor.replace('rgb(', 'rgba(').replace(')', `, ${opacity})`);
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
                        if (uploadBenchmarkFailed[index]) {
                            const x = point.x;
                            const y = point.y;

                            // Create multiple ping rings at different stages
                            const pingDuration = 2000;
                            const maxRadius = 25;
                            const numberOfRings = 3;

                            for (let i = 0; i < numberOfRings; i++) {
                                const offset = (pingDuration / numberOfRings) * i;
                                const progress = ((time + offset) % pingDuration) / pingDuration;
                                const radius = progress * maxRadius;
                                const opacity = Math.max(0, 0.6 * (1 - progress));

                                if (opacity > 0.05) {
                                    ctx.save();
                                    ctx.beginPath();
                                    ctx.arc(x, y, radius, 0, 2 * Math.PI);
                                    const uploadRingColor = config.uploadColor.replace('rgb(', 'rgba(').replace(')', `, ${opacity})`);
                                    ctx.strokeStyle = uploadRingColor;
                                    ctx.lineWidth = 2.5;
                                    ctx.stroke();
                                    ctx.restore();
                                }
                            }
                        }
                    });

                    // Continue animation if there are failed benchmarks
                    if (downloadBenchmarkFailed.some(failed => failed) || uploadBenchmarkFailed.some(failed => failed)) {
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
                        }
                    ]
                },
                plugins: [pulsingPointsPlugin],
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

                                    // Determine which dataset (download or upload)
                                    const isDownload = datasetIndex === 0;
                                    const benchmark = isDownload
                                        ? (config.downloadBenchmarks && config.downloadBenchmarks[index])
                                        : (config.uploadBenchmarks && config.uploadBenchmarks[index]);

                                    // Main result line with specific label
                                    let resultLabel = isDownload ? '- Download: ' : '- Upload: ';
                                    if (context.parsed.y !== null) {
                                        resultLabel += context.parsed.y.toFixed(2) + ' Mbps';
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
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
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
                                display: true,
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

            this.createChart(newData.labels, newData.download, newData.upload);
        }
    }));
</script>
@endscript
