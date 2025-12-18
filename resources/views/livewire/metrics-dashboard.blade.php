<div class="flex h-full w-full flex-1 flex-col gap-6">
    <livewire:next-speedtest-banner />

    <div class="flex items-center justify-between">
        <flux:heading size="xl" class="flex items-center gap-2">
            <x-tabler-chart-histogram class="size-5" />
            Metrics Dashboard
        </flux:heading>

        <div class="flex items-center gap-2">
            <flux:button
                wire:click="updateDateRange('today')"
                :variant="$dateRange === 'today' ? 'primary' : 'ghost'"
                size="sm"
                class="cursor-pointer">
                Last 24 Hours
            </flux:button>
            <flux:button
                wire:click="updateDateRange('week')"
                :variant="$dateRange === 'week' ? 'primary' : 'ghost'"
                size="sm"
                class="cursor-pointer">
                Last Week
            </flux:button>
            <flux:button
                wire:click="updateDateRange('month')"
                :variant="$dateRange === 'month' ? 'primary' : 'ghost'"
                size="sm"
                class="cursor-pointer">
                Last Month
            </flux:button>
        </div>
    </div>

    <!-- Data Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Download & Upload Speed Comparison -->
        <div class="col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                <x-tabler-chart-histogram class="size-5 text-neutral-600" />
                Speed
            </flux:heading>

            <!-- Speed Comparison Chart -->
            <div
                x-data="speedComparisonChartComponent({
                    labels: @js($chartData['labels']),
                    downloadData: @js($chartData['download']),
                    uploadData: @js($chartData['upload']),
                    downloadColor: 'rgb(59, 130, 246)',
                    uploadColor: 'rgb(168, 85, 247)',
                    downloadBenchmarkFailed: @js($chartData['downloadBenchmarkFailed']),
                    uploadBenchmarkFailed: @js($chartData['uploadBenchmarkFailed']),
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[2/1] lg:aspect-[4/1] px-6 py-4"
            >
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Speed Comparison Stats -->
            <div class="border-t border-neutral-200 dark:border-neutral-700">
                <!-- Download Stats -->
                <div class="border-b border-neutral-200 dark:border-neutral-700">
                    <flux:heading size="sm" class="px-6 pt-3 pb-2 text-blue-600 dark:text-blue-400">Download</flux:heading>
                    <div class="divide-x divide-neutral-200 grid grid-cols-2 lg:grid-cols-6 dark:divide-neutral-700 border-t border-neutral-200 dark:border-neutral-700">
                        <div class="px-6 py-3">
                            <flux:heading>Latest</flux:heading>
                            <div class="text-xl font-semibold {{ $chartData['downloadStats']['latestFailed'] ? 'text-amber-500 dark:text-amber-400' : 'text-neutral-900 dark:text-neutral-100' }}">
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
                    <flux:heading size="sm" class="px-6 pt-3 pb-2 text-purple-600 dark:text-purple-400">Upload</flux:heading>
                    <div class="divide-x divide-neutral-200 grid grid-cols-2 lg:grid-cols-6 dark:divide-neutral-700 border-t border-neutral-200 dark:border-neutral-700">
                        <div class="px-6 py-3">
                            <flux:heading>Latest</flux:heading>
                            <div class="text-xl font-semibold {{ $chartData['uploadStats']['latestFailed'] ? 'text-amber-500 dark:text-amber-400' : 'text-neutral-900 dark:text-neutral-100' }}">
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

        <!-- Download Data -->
        <div class="hidden col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                <x-tabler-download class="size-5 text-neutral-600" />
                {{ __('general.download') }}
            </flux:heading>

            <!-- Download Chart -->
            <div
                x-data="dualAxisChartComponent({
                    labels: @js($chartData['labels']),
                    primaryData: @js($chartData['download']),
                    secondaryData: @js($chartData['downloadLatency']),
                    primaryLabel: 'Download (Mbps)',
                    secondaryLabel: 'Latency (ms)',
                    primaryColor: 'rgb(59, 130, 246)',
                    secondaryColor: 'rgb(16, 185, 129)',
                    benchmarkFailed: @js($chartData['downloadBenchmarkFailed']),
                    benchmarks: @js($chartData['downloadBenchmarks']),
                    field: 'download',
                    primaryUnit: 'Mbps',
                    secondaryUnit: 'ms'
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[2/1] lg:aspect-[4/1] px-6 py-4"
            >
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Download Stats -->
            <div class="divide-x divide-neutral-200 grid grid-cols-2 lg:grid-cols-6 border-t border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                <div class="px-6 py-3">
                    <flux:heading>Latest</flux:heading>
                    <div class="text-xl font-semibold {{ $chartData['downloadStats']['latestFailed'] ? 'text-amber-500 dark:text-amber-400' : 'text-neutral-900 dark:text-neutral-100' }}">
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

        <!-- Upload Data -->
        <div class="hidden col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                <x-tabler-upload class="size-5 text-neutral-600" />
                {{ __('general.upload') }}
            </flux:heading>

            <!-- Upload Chart -->
            <div
                x-data="chartComponent({
                    type: 'line',
                    label: 'Upload (Mbps)',
                    labels: @js($chartData['labels']),
                    data: @js($chartData['upload']),
                    benchmarkFailed: @js($chartData['uploadBenchmarkFailed']),
                    benchmarks: @js($chartData['uploadBenchmarks']),
                    color: 'rgb(59, 130, 246)',
                    field: 'upload',
                    showPoints: true,
                    unit: 'Mbps'
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[2/1] lg:aspect-[4/1] px-6 py-4"
            >
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Upload Stats -->
            <div class="divide-x divide-neutral-200 grid grid-cols-2 lg:grid-cols-6 border-t border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                <div class="px-6 py-3">
                    <flux:heading>Latest</flux:heading>
                    <div class="text-xl font-semibold {{ $chartData['uploadStats']['latestFailed'] ? 'text-amber-500 dark:text-amber-400' : 'text-neutral-900 dark:text-neutral-100' }}">
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

        <!-- Ping Data -->
        <div class="col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                <x-tabler-antenna-bars-5 class="size-5 text-neutral-600" />
                {{ __('general.ping') }}
            </flux:heading>

            <!-- Ping Chart -->
            <div
                x-data="chartComponent({
                    type: 'line',
                    label: 'Ping (ms)',
                    labels: @js($chartData['labels']),
                    data: @js($chartData['ping']),
                    benchmarkFailed: @js($chartData['pingBenchmarkFailed']),
                    benchmarks: @js($chartData['pingBenchmarks']),
                    color: 'rgb(59, 130, 246)',
                    field: 'ping',
                    showPoints: true,
                    unit: 'ms'
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[3/1] lg:aspect-[4/1] px-6 py-4"
            >
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Ping Stats -->
            <div class="divide-x divide-neutral-200 grid grid-cols-2 lg:grid-cols-6 border-t border-neutral-200 dark:divide-neutral-700 dark:border-neutral-700">
                <div class="px-6 py-3">
                    <flux:heading>Latest</flux:heading>
                    <div class="text-xl font-semibold {{ $chartData['pingStats']['latestFailed'] ? 'text-amber-500 dark:text-amber-400' : 'text-neutral-900 dark:text-neutral-100' }}">
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

        <!-- Jitter Data -->
        <div class="col-span-full rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <flux:heading class="flex items-center gap-x-2 px-6 pt-4" size="lg">
                <x-tabler-graph class="size-5 text-neutral-600" />
                Jitter
            </flux:heading>

            <!-- Jitter Chart -->
            <div
                x-data="multiLineChartComponent({
                    labels: @js($chartData['labels']),
                    datasets: [
                        {
                            label: 'Download Jitter (ms)',
                            data: @js($chartData['downloadJitter']),
                            color: 'rgb(59, 130, 246)',
                        },
                        {
                            label: 'Upload Jitter (ms)',
                            data: @js($chartData['uploadJitter']),
                            color: 'rgb(16, 185, 129)',
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
                class="aspect-[2/1] lg:aspect-[4/1] px-6 py-4"
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
                    <flux:heading size="sm" class="mb-3 text-emerald-600 dark:text-emerald-400">Upload Jitter</flux:heading>
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
                        backgroundColor: getFillColor(config.color),
                        fill: isLine ? true : false,
                        tension: isLine ? 0.2 : 0.2,
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
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const benchmark = config.benchmarks && config.benchmarks[index];
                                    const labels = [];

                                    // Main result line
                                    let resultLabel = 'Result: ';
                                    if (context.parsed.y !== null) {
                                        resultLabel += context.parsed.y.toFixed(2) + ' ' + unit;
                                    }
                                    labels.push(resultLabel);

                                    // Benchmark info if available
                                    if (benchmark) {
                                        const thresholdType = benchmark.bar === 'min' ? 'Min' : 'Max';
                                        const benchmarkLabel = `Benchmark (${thresholdType}): ${benchmark.value} ${benchmark.unit}`;
                                        labels.push(benchmarkLabel);

                                        const statusLabel = benchmark.passed ? 'Status: ✅ Passed' : 'Status: ❌ Failed';
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
            // Update benchmark failed data for this field
            const benchmarkFailedField = config.field + 'BenchmarkFailed';
            config.benchmarkFailed = newData[benchmarkFailedField] || [];

            // Update full benchmark data for this field
            const benchmarksField = config.field + 'Benchmarks';
            config.benchmarks = newData[benchmarksField] || [];

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
                    fill: true,
                    tension: 0.2,
                    borderWidth: 2,
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
                                label: function(context) {
                                    let label = context.dataset.label || '';
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
            const datasets = [
                {
                    label: 'Download Jitter (ms)',
                    data: newData.downloadJitter || [],
                    color: 'rgb(59, 130, 246)',
                },
                {
                    label: 'Upload Jitter (ms)',
                    data: newData.uploadJitter || [],
                    color: 'rgb(16, 185, 129)',
                },
                {
                    label: 'Ping Jitter (ms)',
                    data: newData.pingJitter || [],
                    color: 'rgb(168, 85, 247)',
                }
            ];

            this.createChart(newData.labels, datasets);
        }
    }));

    Alpine.data('speedComparisonChartComponent', (config) => ({
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
            const amberColor = 'rgb(251, 191, 36)'; // Amber for failed benchmarks

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
                downloadBenchmarkFailed[index] ? amberColor : config.downloadColor
            );
            const downloadPointRadii = downloadData.map((_, index) =>
                downloadBenchmarkFailed[index] ? 5 : 0
            );

            // Create point colors and radii for upload
            const uploadPointColors = uploadData.map((_, index) =>
                uploadBenchmarkFailed[index] ? amberColor : config.uploadColor
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
                                    ctx.strokeStyle = `rgba(251, 191, 36, ${opacity})`;
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
                                    ctx.strokeStyle = `rgba(251, 191, 36, ${opacity})`;
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
                            fill: true,
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
                            fill: true,
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
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toFixed(2) + ' Mbps';
                                    }
                                    return label;
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
                                text: 'Download Speed (Mbps)',
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
                                text: 'Upload Speed (Mbps)',
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

            this.createChart(newData.labels, newData.download, newData.upload);
        }
    }));

    Alpine.data('dualAxisChartComponent', (config) => ({
        chart: null,
        animationFrame: null,
        currentLabels: config.labels,
        currentPrimaryData: config.primaryData,
        currentSecondaryData: config.secondaryData,

        init() {
            this.createChart(config.labels, config.primaryData, config.secondaryData);

            // Listen for theme changes and re-draw chart
            window.addEventListener('theme-changed', () => {
                // Small delay to allow DOM to update
                setTimeout(() => {
                    this.createChart(this.currentLabels, this.currentPrimaryData, this.currentSecondaryData);
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

        createChart(labels, primaryData, secondaryData) {
            // Store current data for theme changes
            this.currentLabels = labels;
            this.currentPrimaryData = primaryData;
            this.currentSecondaryData = secondaryData;

            if (this.chart) {
                this.chart.destroy();
            }

            const primaryUnit = config.primaryUnit || 'Mbps';
            const secondaryUnit = config.secondaryUnit || 'ms';
            const benchmarkFailed = config.benchmarkFailed || [];
            const amberColor = 'rgb(251, 191, 36)'; // Amber for failed benchmarks

            // Detect dark mode for text colors
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? 'rgb(228, 228, 231)' : 'rgb(39, 39, 42)';
            const gridColor = isDarkMode ? 'rgba(228, 228, 231, 0.1)' : 'rgba(0, 0, 0, 0.1)';

            // Convert rgb() to rgba() with opacity for fill
            const getFillColor = (color, opacity = 0.3) => {
                return color.replace('rgb(', 'rgba(').replace(')', `, ${opacity})`);
            };

            // Create point color and radius arrays based on benchmark failures
            const pointColors = primaryData.map((_, index) =>
                benchmarkFailed[index] ? amberColor : config.primaryColor
            );

            const pointRadii = primaryData.map((_, index) =>
                benchmarkFailed[index] ? 5 : 0
            );

            // Calculate max value for secondary data and set axis max to keep data in bottom 1/2
            const secondaryMaxValue = Math.max(...secondaryData.filter(v => v !== null && !isNaN(v)));
            const secondaryAxisMax = secondaryMaxValue > 0 ? secondaryMaxValue * 2 : 10;

            // Calculate flexible step size to keep axis clean (aim for 4-6 ticks)
            const targetTicks = 5;
            const rawStep = secondaryAxisMax / targetTicks;
            const stepSize = Math.ceil(rawStep / 10) * 10; // Round up to nearest 10

            // Plugin to create ping/ripple effect on failed benchmark points
            const self = this;
            const pulsingPointsPlugin = {
                id: 'pulsingPoints',
                afterDatasetsDraw: (chart) => {
                    const ctx = chart.ctx;
                    const meta = chart.getDatasetMeta(0); // Primary dataset
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
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: config.primaryLabel,
                            data: primaryData,
                            borderColor: config.primaryColor,
                            backgroundColor: getFillColor(config.primaryColor),
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: pointRadii,
                            pointHoverRadius: 7,
                            pointBackgroundColor: pointColors,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: pointColors,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3,
                            yAxisID: 'y',
                        },
                        {
                            label: config.secondaryLabel,
                            data: secondaryData,
                            borderColor: config.secondaryColor,
                            backgroundColor: getFillColor(config.secondaryColor, 0.1),
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointBackgroundColor: config.secondaryColor,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: config.secondaryColor,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                            yAxisID: 'y1',
                        }
                    ]
                },
                plugins: [pulsingPointsPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                                label: function(context) {
                                    const datasetIndex = context.datasetIndex;
                                    const index = context.dataIndex;
                                    const labels = [];

                                    if (datasetIndex === 0) {
                                        // Primary dataset (Download)
                                        const benchmark = config.benchmarks && config.benchmarks[index];

                                        // Main result line
                                        let resultLabel = config.primaryLabel + ': ';
                                        if (context.parsed.y !== null) {
                                            resultLabel += context.parsed.y.toFixed(2) + ' ' + primaryUnit;
                                        }
                                        labels.push(resultLabel);

                                        // Benchmark info if available
                                        if (benchmark) {
                                            const thresholdType = benchmark.bar === 'min' ? 'Min' : 'Max';
                                            const benchmarkLabel = `Benchmark (${thresholdType}): ${benchmark.value} ${benchmark.unit}`;
                                            labels.push(benchmarkLabel);

                                            const statusLabel = benchmark.passed ? 'Status: ✅ Passed' : 'Status: ❌ Failed';
                                            labels.push(statusLabel);
                                        }

                                        return labels;
                                    } else {
                                        // Secondary dataset (Latency)
                                        let label = config.secondaryLabel + ': ';
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(2) + ' ' + secondaryUnit;
                                        }
                                        return label;
                                    }
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
                                text: config.primaryLabel,
                                color: textColor,
                            },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value + ' ' + primaryUnit;
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
                            max: secondaryAxisMax,
                            title: {
                                display: true,
                                text: config.secondaryLabel,
                                color: textColor,
                            },
                            ticks: {
                                color: textColor,
                                stepSize: stepSize,
                                callback: function(value) {
                                    if (Number.isInteger(value)) {
                                        return value + ' ' + secondaryUnit;
                                    }
                                    return null;
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
            // Update benchmark failed data for this field
            const benchmarkFailedField = config.field + 'BenchmarkFailed';
            config.benchmarkFailed = newData[benchmarkFailedField] || [];

            // Update full benchmark data for this field
            const benchmarksField = config.field + 'Benchmarks';
            config.benchmarks = newData[benchmarksField] || [];

            this.createChart(newData.labels, newData[config.field], newData[config.field + 'Latency']);
        }
    }));
</script>
@endscript
