import Alpine from 'alpinejs';

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

    // Initialization
    async init() {
        await this.loadServers();
        await this.loadStats();
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

    // Handle filter changes
    async onFilterChange() {
        await this.loadStats();
    },

    // Reset filters to defaults
    async resetFilters() {
        this.filters.timeRange = '24h';
        this.filters.server = '';
        await this.loadStats();
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
