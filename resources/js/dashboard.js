import Alpine from 'alpinejs';
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    TimeScale,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';
import 'chartjs-adapter-date-fns';

// Register Chart.js components
Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    TimeScale,
    Title,
    Tooltip,
    Legend,
    Filler
);

// Make Alpine available globally
window.Alpine = Alpine;

// Define dashboard component
Alpine.data('dashboard', () => ({
    // State
    stats: null,
    servers: [],
    filters: {
        timeRange: '24h',
        server: '',
    },
    loading: false,
    error: null,

    // Metrics state
    downloadStats: null,
    downloadChart: null,
    uploadStats: null,
    uploadChart: null,
    pingStats: null,
    pingChart: null,

    // Health state
    health: null,

    // Initialization
    async init() {
        await this.loadServers();
        await this.loadStats();
        await this.loadMetrics();
        await this.loadHealth();
    },

    // Load all metrics
    async loadMetrics() {
        await this.loadDownloadMetrics();
        await this.loadUploadMetrics();
        await this.loadPingMetrics();
    },

    // Load download metrics
    async loadDownloadMetrics() {
        await this.loadStatistics('download');
        await this.loadChartData('download');
    },

    // Load upload metrics
    async loadUploadMetrics() {
        await this.loadStatistics('upload');
        await this.loadChartData('upload');
    },

    // Load ping metrics
    async loadPingMetrics() {
        await this.loadStatistics('ping');
        await this.loadChartData('ping');
    },

    // Load health data
    async loadHealth() {
        try {
            const params = new URLSearchParams({
                time_range: this.filters.timeRange,
            });

            if (this.filters.server) {
                params.append('server', this.filters.server);
            }

            const response = await fetch(`/api/public/health?${params}`);
            if (!response.ok) {
                throw new Error('Failed to load health data');
            }

            this.health = await response.json();
        } catch (error) {
            console.error('Failed to load health data:', error);
        }
    },

    // Load server list
    async loadServers() {
        try {
            const response = await fetch('/api/public/servers');
            if (!response.ok) {
                throw new Error('Failed to load servers');
            }
            this.servers = await response.json();
        } catch (error) {
            console.error('Failed to load servers:', error);
            // Don't show error to user for servers, just log it
        }
    },

    // Load latest stats
    async loadStats() {
        this.loading = true;
        this.error = null;

        try {
            const params = new URLSearchParams({
                time_range: this.filters.timeRange,
            });

            if (this.filters.server) {
                params.append('server', this.filters.server);
            }

            const response = await fetch(`/api/public/stats?${params}`);
            if (!response.ok) {
                throw new Error('Failed to load stats');
            }

            this.stats = await response.json();
        } catch (error) {
            this.error = 'Failed to load stats. Please try again.';
            console.error('Failed to load stats:', error);
        } finally {
            this.loading = false;
        }
    },

    // Load statistics for a metric
    async loadStatistics(metric) {
        try {
            const params = new URLSearchParams({
                time_range: this.filters.timeRange,
            });

            if (this.filters.server) {
                params.append('server', this.filters.server);
            }

            const response = await fetch(`/api/public/statistics/${metric}?${params}`);
            if (!response.ok) {
                throw new Error(`Failed to load ${metric} statistics`);
            }

            const data = await response.json();
            this[`${metric}Stats`] = data;
        } catch (error) {
            console.error(`Failed to load ${metric} statistics:`, error);
        }
    },

    // Load chart data for a metric
    async loadChartData(metric) {
        try {
            const params = new URLSearchParams({
                time_range: this.filters.timeRange,
            });

            if (this.filters.server) {
                params.append('server', this.filters.server);
            }

            const response = await fetch(`/api/public/chart-data/${metric}?${params}`);
            if (!response.ok) {
                throw new Error(`Failed to load ${metric} chart data`);
            }

            const data = await response.json();
            this.updateChart(metric, data);
        } catch (error) {
            console.error(`Failed to load ${metric} chart data:`, error);
        }
    },

    // Update chart with new data
    updateChart(metric, data) {
        const canvas = document.getElementById(`${metric}-chart`);
        if (!canvas) {
            console.warn(`Chart canvas #${metric}-chart not found`);
            return;
        }

        // Destroy existing chart if it exists
        if (this[`${metric}Chart`]) {
            this[`${metric}Chart`].destroy();
        }

        const ctx = canvas.getContext('2d');

        // Get theme colors
        const isDark = document.documentElement.classList.contains('dark');
        const lineColor = isDark ? 'rgb(59, 130, 246)' : 'rgb(37, 99, 235)';
        const averageColor = isDark ? 'rgb(239, 68, 68)' : 'rgb(220, 38, 38)';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const textColor = isDark ? 'rgb(229, 231, 235)' : 'rgb(31, 41, 55)';

        this[`${metric}Chart`] = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: metric.charAt(0).toUpperCase() + metric.slice(1),
                        data: data.data,
                        borderColor: lineColor,
                        tension: 0.4,
                        fill: false,
                    },
                    {
                        label: 'Average',
                        data: data.data.map((point) => ({ x: point.x, y: data.average })),
                        borderColor: averageColor,
                        borderDash: [5, 5],
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (metric === 'ping') {
                                    label += context.parsed.y.toFixed(2) + ' ms';
                                } else {
                                    label += this.formatSpeed(context.parsed.y);
                                }
                                return label;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: this.filters.timeRange === '24h' ? 'hour' : 'day',
                        },
                        grid: {
                            color: gridColor,
                        },
                        ticks: {
                            color: textColor,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor,
                        },
                        ticks: {
                            color: textColor,
                            callback: (value) => {
                                if (metric === 'ping') {
                                    return value.toFixed(0) + ' ms';
                                }
                                return this.formatSpeed(value);
                            },
                        },
                    },
                },
            },
        });
    },

    // Handle filter changes
    async onFilterChange() {
        await this.loadStats();
        await this.loadMetrics();
        await this.loadHealth();
    },

    // Reset filters to defaults
    async resetFilters() {
        this.filters.timeRange = '24h';
        this.filters.server = '';
        await this.loadStats();
        await this.loadMetrics();
        await this.loadHealth();
    },

    // Format speed in Mbps
    formatSpeed(bytes) {
        if (!bytes) return '0 Mbps';
        const mbps = (bytes * 8) / 1000000;
        return `${mbps.toFixed(2)} Mbps`;
    },

    // Format ping with ms suffix
    formatPing(ms) {
        if (!ms) return '0 ms';
        return `${parseFloat(ms).toFixed(2)} ms`;
    },
}));

// Start Alpine
Alpine.start();
