# PRD: Dashboard V2 - Modern Public Dashboard

## Executive Summary

Create a new "Dashboard V2" as an alternative to the existing Filament-based public dashboard. This new dashboard will use **Alpine.js (standalone) + ChartJS** with API-driven data fetching to improve performance and enable additional features including:

**Technology Stack**:
- **Frontend**: Alpine.js v3 (installed via npm, NOT from Livewire)
- **Charts**: Chart.js v4
- **Backend**: Laravel API endpoints (no Livewire/Filament)
- **Styling**: Tailwind CSS with shadcn design reference (https://ui.shadcn.com/)
- **Build**: Vite

**Important**: shadcn is used as a **design reference only** for UI patterns, component styles, spacing, and color schemes. Do NOT install shadcn as a dependency - we'll implement the styles manually using Tailwind CSS.

**Key Features**:
- **Enhanced Statistics**: Display Latest, Average, Lowest, and Highest values for Download, Upload, and Ping metrics
- **Advanced Filtering**: Time range selection plus server-specific filtering with reset capability
- **Filter Persistence**: Remember user preferences using browser localStorage
- **Health Monitoring**: Visual tracking of test success rates and latest test status
- **Streamlined UI**: Card-based grid layout focusing on core metrics (Download, Upload, Ping)
- **Improved Performance**: Client-side rendering with aggressive API caching
- **Complete Independence**: Zero dependency on Livewire or Filament

The V2 dashboard will be accessible at the `/v2` route and gated behind an environment variable (`ENABLE_DASHBOARD_V2`), allowing for testing and gradual rollout. The existing V1 dashboard will remain at the root `/` route unchanged.

## Problem Statement

### Current Issues
1. **Performance Bottleneck**: Filament widgets render on the server-side, causing heavy load on each request
2. **Polling Overhead**: Every widget polls every 60 seconds, multiplying server load (7 widgets × polling = significant overhead)
3. **Limited Features**: Server-side rendering limits interactive features like real-time updates, advanced filtering, and chart customization
4. **Tight Coupling**: Public dashboard is tightly coupled with Filament/Livewire infrastructure
5. **Scale Limitations**: As data grows, rendering all chart data on each request becomes increasingly expensive
6. **Missing Analytics**: No visibility into test health trends or statistical ranges (min/max/average)
7. **Limited Filtering**: Cannot filter by specific servers or reset filters easily

### Current Architecture
- **Dashboard View**: `resources/views/dashboard.blade.php`
- **Controller**: `app/Http/Controllers/HomeController.php`
- **Widgets** (7 total):
  - `StatsOverviewWidget` - Latest stats with trend indicators
  - `RecentDownloadChartWidget` - Download speed over time
  - `RecentUploadChartWidget` - Upload speed over time
  - `RecentPingChartWidget` - Ping latency over time
  - `RecentJitterChartWidget` - Jitter over time
  - `RecentDownloadLatencyChartWidget` - Download latency over time
  - `RecentUploadLatencyChartWidget` - Upload latency over time
- **Features**:
  - Time range filters (24h, 7 days, 30 days)
  - Auto-refresh every 60 seconds
  - Average line overlay on charts
  - Dark mode support

### Desired New Features (From Wireframe)
1. **Enhanced Filtering**:
   - Server selection dropdown (filter results by specific speedtest server)
   - Reset filters button to clear all applied filters
   - **Filter state persistence**: Remember filter selections using browser's localStorage
     - Restore time range and server selection when user returns to the page
     - Persist across browser sessions until explicitly reset
2. **Test Health Monitoring**:
   - Latest test health status indicator (Healthy/Failed)
   - Test health over time visualization (percentage of successful tests)
3. **Statistical Insights**:
   - Latest, Average, Lowest, and Highest values for each metric
   - Comprehensive statistical overview for Download, Upload, and Ping
4. **Streamlined Interface**:
   - Focus on core metrics (Download, Upload, Ping)
   - Remove less commonly used charts (Jitter, Download/Upload Latency)
   - Card-based grid layout for better organization
5. **Optional Authentication**:
   - Login capability for access control (optional feature)

## Proposed Solution

### High-Level Approach
Create a new dashboard (Dashboard V2) at the `/v2` route alongside the existing dashboard at `/`, using ChartJS charts powered by dedicated public API endpoints, with Alpine.js (standalone installation) for interactivity and state management. Access to V2 is controlled by the `ENABLE_DASHBOARD_V2` environment variable.

**Important**: Dashboard V2 is completely independent of Livewire and Filament. While the application uses Livewire for the V1 dashboard, V2 uses Alpine.js as a standalone dependency with its own build process.

**Key Improvements:**
- Enhanced statistics: Latest, Average, Lowest, and Highest values for all metrics
- Advanced filtering: Time range selection + server filtering with reset capability
- Filter persistence: Restore user's filter preferences using browser localStorage
- Health monitoring: Test success rate tracking and visualization
- Streamlined UI: Focus on core metrics (Download, Upload, Ping) with card-based layout
- Optional authentication: Login capability for access control

**Benefits:**
- Environment-gated testing and rollout
- Easy rollback by disabling the env variable
- Performance comparison without UI complexity
- V1 remains completely unchanged
- Lower risk deployment
- Better insights into network performance trends
- Improved user experience with comprehensive filtering

## Implementation Options

### Option 1: Alpine.js + ChartJS (SELECTED)

**Description**: Build a modern, API-driven dashboard using Alpine.js (standalone) for state management and ChartJS for visualization. Dashboard V2 is completely independent of Livewire/Filament.

**Architecture**:
```
┌─────────────────────────────────────────┐
│    Dashboard V2 View (New)              │
│  (Blade template with Alpine.js)        │
│  Route: /v2 (env-gated)                 │
│  NO Livewire Components                  │
└─────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│     Alpine.js Components                 │
│  (Standalone - NOT from Livewire)        │
│  - Chart state management                │
│  - Filter handling (range + server)     │
│  - localStorage persistence              │
│  - API calls (fetch)                     │
│  - Auto-refresh logic                    │
│  - Statistics display                    │
└─────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│     Public API Endpoints                 │
│  (Optional authentication)               │
│  - GET /api/public/stats                 │
│  - GET /api/public/statistics/{metric}   │
│  - GET /api/public/charts/{metric}       │
│  - GET /api/public/health                │
│  - GET /api/public/servers               │
└─────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│     Result Model & Database              │
└─────────────────────────────────────────┘
```

**Technology Stack**:
- **Frontend Framework**: Alpine.js v3 (standalone npm package)
- **Charting Library**: Chart.js v4
- **Build Tool**: Vite (existing Laravel setup)
- **Styling**: Tailwind CSS (existing) with shadcn design reference
- **Backend**: Laravel API endpoints (no Livewire)

**Note**: shadcn (https://ui.shadcn.com/) is used as a design system reference only - NOT installed as a dependency. We manually implement its design patterns using Tailwind CSS.

**Pros**:
- ✅ Best performance - data fetched on-demand
- ✅ Complete independence from Livewire/Filament
- ✅ No new PHP dependencies
- ✅ Clean separation of concerns
- ✅ Easy to add new features (real-time updates, advanced filters)
- ✅ Reduced server load
- ✅ Better caching opportunities
- ✅ Modern, maintainable architecture
- ✅ Smaller bundle size (no Livewire overhead)
- ✅ Standard JavaScript patterns (fetch API, localStorage)

**Cons**:
- ⚠️ Moderate development effort
- ⚠️ Need to reimplement filter UI
- ⚠️ Requires creating new API endpoints
- ⚠️ Alpine.js added as explicit dependency

**Estimated Effort**: Medium (5-8 days across 3 phases)

---

### Option 2: Vanilla JS + ChartJS

**Description**: Pure JavaScript implementation without any framework.

**Pros**:
- ✅ Smallest possible bundle size
- ✅ No framework dependencies
- ✅ Maximum performance

**Cons**:
- ⚠️ More boilerplate code
- ⚠️ Harder to maintain
- ⚠️ Need to manually handle state
- ⚠️ More testing required

**Estimated Effort**: Medium-High (4-6 days)

---

### Option 3: Livewire + ChartJS (Hybrid)

**Description**: Keep Livewire components but replace Filament chart rendering with ChartJS.

**Pros**:
- ✅ Minimal changes to existing structure
- ✅ Leverage existing Livewire infrastructure
- ✅ Familiar patterns

**Cons**:
- ⚠️ Doesn't solve performance issues
- ⚠️ Still server-side rendering overhead
- ⚠️ Still polling multiple widgets
- ⚠️ Half-measure solution

**Estimated Effort**: Low (1-2 days)

---

### Option 4: Full SPA Framework (Vue/React)

**Description**: Complete rewrite using a full frontend framework.

**Pros**:
- ✅ Most feature-rich
- ✅ Best developer experience
- ✅ Industry standard

**Cons**:
- ❌ Significant new dependencies
- ❌ Build complexity increases
- ❌ Overkill for this use case
- ❌ Requires team expertise

**Estimated Effort**: High (7-10 days)

---

## Selected Option: Option 1 (Alpine.js + ChartJS)

**Rationale**:
1. **Standalone Alpine.js**: Installed as explicit npm dependency, completely independent of Livewire
2. **Best Performance**: Client-side rendering with API-driven data fetching
3. **Clean Separation**: Zero dependency on Livewire/Filament - pure Alpine.js + vanilla JavaScript
4. **shadcn Design System**: Modern, accessible design patterns (NOT Filament) - manual implementation with Tailwind CSS
5. **Maintainability**: Standard JavaScript patterns (fetch API, localStorage, Alpine directives)
6. **Smaller Bundle**: No Livewire overhead for Dashboard V2
7. **Flexibility**: Enables future enhancements (WebSockets, advanced filtering, etc.)
8. **Team Skills**: Alpine.js is lightweight and easy to learn for any JavaScript developer
9. **Independent Updates**: Dashboard V2 can be updated without affecting V1 (Livewire-based)

## Phased Implementation Plan

Dashboard V2 will be implemented in three phases to allow for incremental development and testing:

### Phase Overview

| Phase | Focus | Deliverables | Est. Time |
|-------|-------|--------------|-----------|
| **Phase 1** | Core infrastructure, filters, health monitoring, and Download metrics | Functional dashboard with 1 metric | 3-5 days |
| **Phase 2** | Upload metrics | Add second metric to existing dashboard | 1-2 days |
| **Phase 3** | Ping metrics | Complete all metrics | 1-2 days |

### Why Phased Approach?

1. **Incremental Value**: Dashboard is functional after Phase 1 with Download metrics
2. **Reduced Risk**: Each phase can be tested and validated independently
3. **Faster Feedback**: Get user feedback on Phase 1 before building subsequent phases
4. **Easier Debugging**: Isolate issues to specific phases
5. **Flexible Delivery**: Can deploy Phase 1 to production while developing Phase 2/3
6. **Team Collaboration**: Different team members can work on different phases

### Phase Dependencies

- **Phase 1** is standalone and creates all infrastructure
- **Phase 2** depends on Phase 1 completion
- **Phase 3** depends on Phase 2 completion
- Each phase adds metric-specific functionality without modifying core infrastructure

Each phase can be developed, tested, and deployed independently. The dashboard will be functional after Phase 1, with subsequent phases adding additional metrics.

---

## Phase 1: Core Infrastructure + Download Metrics

**Goal**: Establish the foundation of Dashboard V2 with filtering, health monitoring, and complete Download metric visualization.

**Deliverables**:
- Dashboard V2 route and layout
- All API endpoints (with download metric fully implemented)
- Filter system with persistence
- Health monitoring
- Download chart and statistics
- Complete test coverage for Phase 1 features

### Phase 1 Checklist

#### 1.1 Setup & Infrastructure
- [ ] Install Alpine.js and ChartJS dependencies
  ```bash
  npm install alpinejs chart.js chartjs-adapter-date-fns date-fns
  ```
  **Note**: Alpine.js is installed as a standalone dependency, NOT relying on Livewire's bundled version.
- [ ] Create Dashboard V2 route in `routes/web.php`
  - [ ] Add `GET /v2` route
  - [ ] Apply middleware: `['getting-started', 'public-dashboard']`
  - [ ] Name route: `dashboard.v2`
- [ ] Create `app/Http/Controllers/DashboardV2Controller.php`
  - [ ] Check `ENABLE_DASHBOARD_V2` config
  - [ ] Redirect to home if disabled
  - [ ] Return `dashboard-v2` view
- [ ] Create public API routes file `routes/api/public.php`
- [ ] Register API routes in `bootstrap/app.php`
- [ ] Add public API middleware group

#### 1.2 API Controllers & Resources
- [ ] Create `app/Http/Controllers/Api/Public/StatsController.php`
  - [ ] Implement stats endpoint with server filtering
  - [ ] Add cache layer (1 minute TTL)
- [ ] Create `app/Http/Controllers/Api/Public/StatisticsController.php`
  - [ ] Implement statistics endpoint for download metric
  - [ ] Support time range and server filtering
  - [ ] Calculate latest, average, lowest, highest
  - [ ] Add cache layer
- [ ] Create `app/Http/Controllers/Api/Public/ChartDataController.php`
  - [ ] Implement chart data endpoint for download metric
  - [ ] Support time range and server filtering
  - [ ] Calculate average line data
  - [ ] Add cache layer
- [ ] Create `app/Http/Controllers/Api/Public/HealthController.php`
  - [ ] Query results by status
  - [ ] Calculate health percentage
  - [ ] Group health data by time buckets
  - [ ] Add cache layer
- [ ] Create `app/Http/Controllers/Api/Public/ServersController.php`
  - [ ] Query unique servers from results
  - [ ] Include test count per server
  - [ ] Order by most frequently used
  - [ ] Add cache layer
- [ ] Create API Resources
  - [ ] `app/Http/Resources/Public/StatResource.php`
  - [ ] `app/Http/Resources/Public/StatisticsResource.php`
  - [ ] `app/Http/Resources/Public/ChartDataResource.php`
  - [ ] `app/Http/Resources/Public/HealthResource.php`
  - [ ] `app/Http/Resources/Public/ServerResource.php`

#### 1.3 Frontend - Dashboard Layout
- [ ] Create `resources/views/dashboard-v2.blade.php`
  - [ ] Use standard Blade template (NO Livewire components)
  - [ ] **Design System**: Use shadcn design patterns (NOT Filament styling)
  - [ ] Add `x-data="dashboard()"` to root element for Alpine.js
  - [ ] Add header with login button (use `.btn-outline` from shadcn patterns)
  - [ ] Add filters section (time range, server, reset) with `.filter-select` styles
  - [ ] Add latest test status card (use `.stat-card` from shadcn patterns)
  - [ ] Add test health over time widget (spans 9 columns, use `.health-bar` component)
  - [ ] Add Download section with chart (2 columns) + stats cards (1 column, use `.stat-card`)
  - [ ] Add placeholders for Upload section (hidden/disabled)
  - [ ] Add placeholders for Ping section (hidden/disabled)
  - [ ] Use `<canvas>` elements for charts (NO Filament chart components)
  - [ ] Use Alpine directives (`x-model`, `x-text`, `x-show`, `@click`, etc.)
  - [ ] NO `wire:` directives anywhere
  - [ ] NO Filament components or styling
  - [ ] NO Livewire components (`<livewire:component-name />`)

#### 1.4 Frontend - Alpine.js Component
- [ ] Create `resources/js/dashboard.js`
  - [ ] Initialize state variables
  - [ ] Implement `loadFilterState()` - Load from localStorage
  - [ ] Implement `saveFilterState()` - Save to localStorage with error handling
  - [ ] Implement `loadServers()` - Fetch server list
  - [ ] Implement `loadStats()` - Fetch latest stats
  - [ ] Implement `loadHealth()` - Fetch health data
  - [ ] Implement `loadStatistics('download')` - Fetch download statistics
  - [ ] Implement `loadChartData('download')` - Fetch download chart data
  - [ ] Implement `initChart('download')` - Initialize download chart with ChartJS
  - [ ] Implement `updateChart('download')` - Update download chart
  - [ ] Implement `onFilterChange()` - Save state and reload data
  - [ ] Implement `resetFilters()` - Clear filters and localStorage
  - [ ] Implement `startAutoRefresh()` - Refresh every 60 seconds
  - [ ] Implement utility methods: `formatSpeed()`, `formatPing()`, `formatTimestamp()`
- [ ] Register dashboard component in `resources/js/app.js`

#### 1.5 Styling (Reference shadcn Design System)
- [ ] Add Dashboard V2 styles to `resources/css/app.css` following shadcn patterns
  - [ ] Chart container styles
  - [ ] Filter select styles (shadcn Select component pattern)
  - [ ] Stat card styles (shadcn Card component pattern)
  - [ ] Health bar styles (custom with shadcn colors)
  - [ ] Button styles (shadcn Button variants: outline, primary, ghost, destructive)
  - [ ] Loading skeleton styles (shadcn Skeleton pattern)
  - [ ] Error message styles (shadcn Alert destructive variant)
  - [ ] Success message styles (shadcn Alert success variant)
  - [ ] Empty state styles
- [ ] Verify shadcn design principles applied:
  - [ ] Subtle borders (`border-gray-200 dark:border-gray-800`)
  - [ ] Soft shadows (`shadow-sm`)
  - [ ] Consistent border radius (`rounded-lg` cards, `rounded-md` inputs)
  - [ ] Proper focus states with rings
  - [ ] Smooth transitions (`duration-200` or `duration-300`)
  - [ ] Semantic color usage for states

#### 1.6 Configuration
- [ ] Update `config/speedtest.php`
  - [ ] Add `dashboard_v2.enabled` config
  - [ ] Add `public_api` cache TTL configs
  - [ ] Add `public_api.rate_limit` config
- [ ] Add environment variables to `.env.example`
  - [ ] `ENABLE_DASHBOARD_V2=false`
  - [ ] Cache TTL variables
  - [ ] Rate limit variables

#### 1.7 Testing - API Tests
- [ ] Create `tests/Feature/Api/Public/StatsControllerTest.php`
  - [ ] Test returns latest stats
  - [ ] Test filters by server
  - [ ] Test returns null when no results
- [ ] Create `tests/Feature/Api/Public/StatisticsControllerTest.php`
  - [ ] Test returns download statistics
  - [ ] Test calculates correct statistics
  - [ ] Test filters by server and range
  - [ ] Test validates metric parameter
- [ ] Create `tests/Feature/Api/Public/ChartDataControllerTest.php`
  - [ ] Test returns download chart data
  - [ ] Test filters by time range
  - [ ] Test filters by server
  - [ ] Test validates metric and range parameters
- [ ] Create `tests/Feature/Api/Public/HealthControllerTest.php`
  - [ ] Test returns health data
  - [ ] Test calculates correct health percentage
  - [ ] Test filters by server and range
- [ ] Create `tests/Feature/Api/Public/ServersControllerTest.php`
  - [ ] Test returns server list
  - [ ] Test orders by test count
- [ ] Create `tests/Feature/DashboardV2Test.php`
  - [ ] Test dashboard loads when enabled
  - [ ] Test redirects to home when disabled

#### 1.8 Testing - Frontend Tests
- [ ] Test download chart renders correctly
- [ ] Test time range filter changes
- [ ] Test server filter changes
- [ ] Test reset filters functionality
- [ ] Test filter state persists in localStorage
- [ ] Test filter state is restored on page reload
- [ ] Test health bar visualization displays correct percentage
- [ ] Test download statistics cards display correct values
- [ ] Test auto-refresh updates data
- [ ] Test loading states
- [ ] Test error handling
- [ ] Test dark mode compatibility

#### 1.9 Performance & Optimization
- [ ] Implement caching for all API endpoints
- [ ] Add cache tags for easy invalidation
- [ ] Optimize database queries with proper indexes
- [ ] Test API response times meet targets
- [ ] Test cache hit rates

#### 1.10 Documentation
- [ ] Document Phase 1 API endpoints
- [ ] Add comments to Alpine component
- [ ] Update README with Phase 1 features
- [ ] Document environment variables

#### 1.11 Code Quality
- [ ] Run `vendor/bin/pint --dirty` to format PHP code
- [ ] Run `npm run build` to compile assets
  - [ ] Verify Alpine.js is included in compiled bundle
  - [ ] Verify Chart.js is included in compiled bundle
  - [ ] Check bundle size is reasonable (< 500KB for Dashboard V2 assets)
- [ ] Run `php artisan test --filter=Public` to verify all tests pass
- [ ] Verify no console errors in browser
- [ ] Verify no Livewire-related assets are loaded on Dashboard V2 page
- [ ] Test on multiple browsers

#### 1.12 Verification Checklist
- [ ] **Alpine.js Independence Verification**
  - [ ] Inspect page source: NO Livewire JavaScript files loaded
  - [ ] Inspect page source: NO `wire:id` attributes in HTML
  - [ ] Browser DevTools Network tab: NO requests to Livewire endpoints
  - [ ] Browser console: Alpine.js version logged (should be standalone v3.13+)
  - [ ] `window.Alpine` is defined and working
  - [ ] `window.Livewire` should NOT be defined on Dashboard V2 page
- [ ] **Design System Verification (shadcn, NOT Filament)**
  - [ ] All buttons use shadcn button classes (`.btn-outline`, `.btn-primary`, etc.)
  - [ ] All cards use `.stat-card` with shadcn card styling
  - [ ] All form inputs use `.filter-select` with shadcn select styling
  - [ ] NO Filament CSS classes or components
  - [ ] NO Filament-specific styling patterns
  - [ ] Visual inspection: UI follows shadcn design (subtle borders, soft shadows, consistent spacing)

### Phase 1 Completion Criteria
- [ ] Dashboard V2 accessible at `/v2` when `ENABLE_DASHBOARD_V2=true`
- [ ] **Alpine.js standalone verified** - No Livewire dependencies or assets
- [ ] All filters (time range, server, reset) working correctly
- [ ] Filter state persists across page reloads
- [ ] Health monitoring displays test success rate
- [ ] Download chart displays data with average line
- [ ] Download statistics show latest, average, lowest, highest
- [ ] All API endpoints functional and cached
- [ ] Test coverage > 95% for Phase 1 code
- [ ] All Phase 1 tests passing
- [ ] Dark mode working correctly
- [ ] Mobile responsive
- [ ] **No Livewire JavaScript loaded** on Dashboard V2 page
- [ ] **No wire: attributes** in Dashboard V2 HTML
- [ ] Bundle size reasonable (< 500KB for V2-specific assets)

---

## Phase 2: Upload Metrics

**Goal**: Add complete Upload metric visualization to Dashboard V2.

**Deliverables**:
- Upload statistics endpoint fully implemented
- Upload chart endpoint fully implemented
- Upload chart and statistics cards
- Complete test coverage for Upload features

### Phase 2 Checklist

#### 2.1 Backend - API Implementation
- [ ] Update `app/Http/Controllers/Api/Public/StatisticsController.php`
  - [ ] Add support for `upload` metric
  - [ ] Calculate latest, average, lowest, highest for upload
- [ ] Update `app/Http/Controllers/Api/Public/ChartDataController.php`
  - [ ] Add support for `upload` metric
  - [ ] Calculate average line data for upload

#### 2.2 Frontend - Layout
- [ ] Update `resources/views/dashboard-v2.blade.php`
  - [ ] Enable Upload section
  - [ ] Add Upload chart (2 columns)
  - [ ] Add Upload statistics cards (1 column): Latest, Average, Lowest, Highest

#### 2.3 Frontend - Alpine.js Component
- [ ] Update `resources/js/dashboard.js`
  - [ ] Add `loadStatistics('upload')` to initialization
  - [ ] Add `initChart('upload')` to initialization
  - [ ] Ensure `updateAllCharts()` includes upload
  - [ ] Ensure auto-refresh updates upload data

#### 2.4 Testing - API Tests
- [ ] Update `tests/Feature/Api/Public/StatisticsControllerTest.php`
  - [ ] Add tests for upload metric
  - [ ] Test calculates correct upload statistics
  - [ ] Test filters work for upload
- [ ] Update `tests/Feature/Api/Public/ChartDataControllerTest.php`
  - [ ] Add tests for upload chart data
  - [ ] Test filters work for upload

#### 2.5 Testing - Frontend Tests
- [ ] Test upload chart renders correctly
- [ ] Test upload statistics cards display correct values
- [ ] Test upload data updates on filter change
- [ ] Test upload data updates on auto-refresh

#### 2.6 Code Quality
- [ ] Run `vendor/bin/pint --dirty`
- [ ] Run `npm run build`
- [ ] Run `php artisan test --filter=Public`
- [ ] Verify no console errors

### Phase 2 Completion Criteria
- [ ] Upload chart displays data with average line
- [ ] Upload statistics show latest, average, lowest, highest
- [ ] Upload data respects time range and server filters
- [ ] Upload data updates on auto-refresh
- [ ] All Phase 2 tests passing
- [ ] Test coverage maintained > 95%

---

## Phase 3: Ping Metrics

**Goal**: Add complete Ping metric visualization to Dashboard V2.

**Deliverables**:
- Ping statistics endpoint fully implemented
- Ping chart endpoint fully implemented
- Ping chart and statistics cards
- Complete test coverage for Ping features
- Full Dashboard V2 completion

### Phase 3 Checklist

#### 3.1 Backend - API Implementation
- [ ] Update `app/Http/Controllers/Api/Public/StatisticsController.php`
  - [ ] Add support for `ping` metric
  - [ ] Calculate latest, average, lowest, highest for ping
- [ ] Update `app/Http/Controllers/Api/Public/ChartDataController.php`
  - [ ] Add support for `ping` metric
  - [ ] Calculate average line data for ping

#### 3.2 Frontend - Layout
- [ ] Update `resources/views/dashboard-v2.blade.php`
  - [ ] Enable Ping section
  - [ ] Add Ping chart (2 columns)
  - [ ] Add Ping statistics cards (1 column): Latest, Average, Lowest, Highest

#### 3.3 Frontend - Alpine.js Component
- [ ] Update `resources/js/dashboard.js`
  - [ ] Add `loadStatistics('ping')` to initialization
  - [ ] Add `initChart('ping')` to initialization
  - [ ] Ensure `updateAllCharts()` includes ping
  - [ ] Ensure auto-refresh updates ping data

#### 3.4 Testing - API Tests
- [ ] Update `tests/Feature/Api/Public/StatisticsControllerTest.php`
  - [ ] Add tests for ping metric
  - [ ] Test calculates correct ping statistics
  - [ ] Test filters work for ping
- [ ] Update `tests/Feature/Api/Public/ChartDataControllerTest.php`
  - [ ] Add tests for ping chart data
  - [ ] Test filters work for ping

#### 3.5 Testing - Frontend Tests
- [ ] Test ping chart renders correctly
- [ ] Test ping statistics cards display correct values
- [ ] Test ping data updates on filter change
- [ ] Test ping data updates on auto-refresh

#### 3.6 Code Quality
- [ ] Run `vendor/bin/pint --dirty`
- [ ] Run `npm run build`
- [ ] Run `php artisan test --filter=Public`
- [ ] Verify no console errors

#### 3.7 Final Integration Testing
- [ ] Test all three metrics (download, upload, ping) working together
- [ ] Test filters apply to all metrics
- [ ] Test auto-refresh updates all metrics
- [ ] Test filter persistence works for all metrics
- [ ] Test health monitoring with all metrics
- [ ] Performance test with all metrics loaded
- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)
- [ ] Mobile responsiveness testing
- [ ] Dark mode testing for all sections

#### 3.8 Documentation
- [ ] Update README with complete Dashboard V2 features
- [ ] Document all API endpoints
- [ ] Create user guide for Dashboard V2
- [ ] Document differences between V1 and V2

### Phase 3 Completion Criteria
- [ ] Ping chart displays data with average line
- [ ] Ping statistics show latest, average, lowest, highest
- [ ] Ping data respects time range and server filters
- [ ] Ping data updates on auto-refresh
- [ ] All three metrics (download, upload, ping) working correctly
- [ ] All tests passing (Phase 1, 2, and 3)
- [ ] Test coverage > 95% overall
- [ ] Performance targets met
- [ ] Dashboard V2 feature complete and ready for production testing

---

## Original Detailed Implementation Plan (Reference)

### Phase 1: Setup & Infrastructure (Day 1)

#### 1.1 Install ChartJS
```bash
npm install chart.js chartjs-adapter-date-fns date-fns
```

#### 1.2 Create Dashboard V2 Route
- [ ] Add route `GET /v2` to `routes/web.php`
- [ ] Create `app/Http/Controllers/DashboardV2Controller.php`
- [ ] Apply same middleware as existing dashboard: `['getting-started', 'public-dashboard']`

#### 1.3 Create Public API Routes
- [ ] Create `routes/api/public.php` for unauthenticated endpoints
- [ ] Register routes in `bootstrap/app.php`
- [ ] Add public API middleware group

#### 1.4 Create API Controllers
- [ ] `app/Http/Controllers/Api/Public/StatsController.php`
- [ ] `app/Http/Controllers/Api/Public/StatisticsController.php`
- [ ] `app/Http/Controllers/Api/Public/ChartDataController.php`
- [ ] `app/Http/Controllers/Api/Public/HealthController.php`
- [ ] `app/Http/Controllers/Api/Public/ServersController.php`

#### 1.5 Create API Resources
- [ ] `app/Http/Resources/Public/StatResource.php`
- [ ] `app/Http/Resources/Public/StatisticsResource.php`
- [ ] `app/Http/Resources/Public/ChartDataResource.php`
- [ ] `app/Http/Resources/Public/HealthResource.php`
- [ ] `app/Http/Resources/Public/ServerResource.php`

### Phase 2: API Endpoints (Day 1-2)

#### 2.1 Stats Endpoint
**Endpoint**: `GET /api/public/stats`

**Query Parameters**:
- `server_id`: (optional) Filter by specific speedtest server

**Response**:
```json
{
  "latest": {
    "download": 250.5,
    "upload": 125.3,
    "ping": 12.5,
    "status": "completed",
    "healthy": true,
    "server_id": 12345,
    "server_name": "Speedtest.net Server",
    "created_at": "2025-11-26T10:30:00Z"
  }
}
```

**Implementation**:
- [ ] Query latest result (filtered by server_id if provided)
- [ ] Include test health status
- [ ] Include server information
- [ ] Format response using resource
- [ ] Add cache layer (1 minute TTL)

#### 2.2 Statistics Endpoint (NEW)
**Endpoint**: `GET /api/public/statistics/{metric}`

**Supported Metrics**:
- `download`
- `upload`
- `ping`

**Query Parameters**:
- `range`: `24h` (default), `week`, `month`
- `server_id`: (optional) Filter by specific speedtest server

**Response**:
```json
{
  "metric": "download",
  "range": "24h",
  "statistics": {
    "latest": 176,
    "average": 162,
    "lowest": 120,
    "highest": 185
  },
  "unit": "Mbps"
}
```

**Implementation**:
- [ ] Validate metric parameter
- [ ] Validate range parameter
- [ ] Query results based on time range and optional server filter
- [ ] Calculate latest, average, min, max values
- [ ] Format response using resource
- [ ] Add cache layer (1 minute TTL for 24h, 5 minutes for week/month)

#### 2.3 Chart Data Endpoint
**Endpoint**: `GET /api/public/charts/{metric}`

**Supported Metrics**:
- `download`
- `upload`
- `ping`

**Query Parameters**:
- `range`: `24h` (default), `week`, `month`
- `server_id`: (optional) Filter by specific speedtest server

**Response**:
```json
{
  "metric": "download",
  "range": "24h",
  "data": [
    {
      "value": 250.5,
      "timestamp": "2025-11-26T10:00:00Z"
    },
    {
      "value": 248.3,
      "timestamp": "2025-11-26T11:00:00Z"
    }
  ],
  "average": 249.4,
  "unit": "Mbps"
}
```

**Implementation**:
- [ ] Validate metric parameter
- [ ] Validate range parameter
- [ ] Query results based on time range and optional server filter
- [ ] Calculate average
- [ ] Format data for ChartJS consumption
- [ ] Add cache layer (1 minute TTL for 24h, 5 minutes for week/month)

#### 2.4 Health Endpoint (NEW)
**Endpoint**: `GET /api/public/health`

**Query Parameters**:
- `range`: `24h` (default), `week`, `month`
- `server_id`: (optional) Filter by specific speedtest server

**Response**:
```json
{
  "range": "24h",
  "latest_status": "completed",
  "health_percentage": 90,
  "total_tests": 100,
  "successful_tests": 90,
  "failed_tests": 10,
  "health_over_time": [
    {
      "timestamp": "2025-11-26T00:00:00Z",
      "successful": 8,
      "failed": 2,
      "percentage": 80
    },
    {
      "timestamp": "2025-11-26T01:00:00Z",
      "successful": 9,
      "failed": 1,
      "percentage": 90
    }
  ]
}
```

**Implementation**:
- [ ] Query results based on time range and optional server filter
- [ ] Count successful vs. failed tests
- [ ] Calculate health percentage
- [ ] Group health data by time buckets for visualization
- [ ] Format response using resource
- [ ] Add cache layer (1 minute TTL)

#### 2.5 Servers Endpoint (NEW)
**Endpoint**: `GET /api/public/servers`

**Query Parameters**: None

**Response**:
```json
{
  "servers": [
    {
      "id": 12345,
      "name": "Speedtest.net Server 1",
      "host": "server1.speedtest.net",
      "location": "New York, NY",
      "test_count": 150
    },
    {
      "id": 12346,
      "name": "Speedtest.net Server 2",
      "host": "server2.speedtest.net",
      "location": "Los Angeles, CA",
      "test_count": 75
    }
  ]
}
```

**Implementation**:
- [ ] Query unique servers from results table
- [ ] Include test count for each server
- [ ] Order by most frequently used
- [ ] Format response using resource
- [ ] Add cache layer (10 minute TTL)

### Phase 3: Frontend Components (Day 2-3)

#### 3.1 Dashboard V2 Layout
**File**: `resources/views/dashboard-v2.blade.php`

**Design System**: All Dashboard V2 components and styles should follow **shadcn design patterns** (https://ui.shadcn.com/), NOT Filament. While we use the existing `<x-app-layout>` wrapper for navigation/header consistency, all internal dashboard components (cards, buttons, inputs, alerts) must use shadcn-inspired styles defined in the styling guide.

**Structure** (Based on Wireframe):
```blade
{{-- Dashboard V2: Pure Alpine.js + Blade (NO Livewire) --}}
{{-- Design: shadcn patterns, NOT Filament --}}
<x-app-layout title="Dashboard V2">
    {{-- Alpine.js component initialization - NO wire: directives --}}
    <div x-data="dashboard()" class="space-y-6">
        {{-- Header with Login Button --}}
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Dashboard</h1>
            @guest
                <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
            @endguest
        </div>

        {{-- Filters Section --}}
        <div class="flex gap-4 items-center">
            {{-- Time Range Dropdown --}}
            <select x-model="range" @change="onFilterChange" class="filter-select">
                <option value="24h">Last 24hrs</option>
                <option value="week">Last 7 days</option>
                <option value="month">Last 30 days</option>
            </select>

            {{-- Server Filter Dropdown --}}
            <select x-model="selectedServer" @change="onFilterChange" class="filter-select">
                <option value="">All servers</option>
                <template x-for="server in servers" :key="server.id">
                    <option :value="server.id" x-text="server.name"></option>
                </template>
            </select>

            {{-- Reset Filters Button --}}
            <button @click="resetFilters" class="btn btn-outline">Reset filters</button>
        </div>

        {{-- Top Row: Latest Test Status, Test Health --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            {{-- Latest Test Health Status --}}
            <div class="stat-card lg:col-span-3">
                <h3 class="stat-label">Latest test</h3>
                <p class="stat-value" :class="stats.latest?.healthy ? 'text-green-600' : 'text-red-600'"
                   x-text="stats.latest?.healthy ? 'Healthy' : 'Failed'"></p>
            </div>

            {{-- Test Health Over Time --}}
            <div class="stat-card lg:col-span-9">
                <h3 class="stat-label mb-2">Test health over time</h3>
                <div class="relative">
                    <div class="health-bar">
                        <div class="health-bar-fill bg-green-500"
                             :style="`width: ${health.health_percentage}%`"></div>
                        <div class="health-bar-fill bg-red-500"
                             :style="`width: ${100 - health.health_percentage}%`"></div>
                    </div>
                    <p class="text-right text-sm mt-1" x-text="`${health.health_percentage}%`"></p>
                </div>
            </div>
        </div>

        {{-- Download Section: Chart + Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Download Chart --}}
            <div class="stat-card lg:col-span-2">
                <h3 class="stat-label mb-4">Download over time</h3>
                <div class="chart-container">
                    <canvas x-ref="downloadChart"></canvas>
                </div>
            </div>

            {{-- Download Statistics --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="stat-card">
                    <h3 class="stat-label">Latest download</h3>
                    <p class="stat-value" x-text="formatSpeed(downloadStats.latest)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Average download</h3>
                    <p class="stat-value" x-text="formatSpeed(downloadStats.average)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Lowest download</h3>
                    <p class="stat-value" x-text="formatSpeed(downloadStats.lowest)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Highest download</h3>
                    <p class="stat-value" x-text="formatSpeed(downloadStats.highest)"></p>
                </div>
            </div>
        </div>

        {{-- Upload Section: Stats + Chart --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Upload Statistics --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="stat-card">
                    <h3 class="stat-label">Latest upload</h3>
                    <p class="stat-value" x-text="formatSpeed(uploadStats.latest)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Average upload</h3>
                    <p class="stat-value" x-text="formatSpeed(uploadStats.average)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Lowest upload</h3>
                    <p class="stat-value" x-text="formatSpeed(uploadStats.lowest)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Highest upload</h3>
                    <p class="stat-value" x-text="formatSpeed(uploadStats.highest)"></p>
                </div>
            </div>

            {{-- Upload Chart --}}
            <div class="stat-card lg:col-span-2">
                <h3 class="stat-label mb-4">Upload over time</h3>
                <div class="chart-container">
                    <canvas x-ref="uploadChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Ping Section: Chart + Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Ping Chart --}}
            <div class="stat-card lg:col-span-2">
                <h3 class="stat-label mb-4">Ping over time</h3>
                <div class="chart-container">
                    <canvas x-ref="pingChart"></canvas>
                </div>
            </div>

            {{-- Ping Statistics --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="stat-card">
                    <h3 class="stat-label">Latest ping</h3>
                    <p class="stat-value" x-text="formatPing(pingStats.latest)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Average ping</h3>
                    <p class="stat-value" x-text="formatPing(pingStats.average)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Lowest ping</h3>
                    <p class="stat-value" x-text="formatPing(pingStats.lowest)"></p>
                </div>
                <div class="stat-card">
                    <h3 class="stat-label">Highest ping</h3>
                    <p class="stat-value" x-text="formatPing(pingStats.highest)"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Include compiled JavaScript (Alpine.js + Chart.js) --}}
    @vite(['resources/js/app.js'])
</x-app-layout>
```

**Key Points**:
- Uses Alpine.js directives: `x-data`, `x-model`, `x-text`, `x-show`, `@click`, `:class`, `x-ref`
- NO Livewire directives: No `wire:model`, `wire:click`, `wire:loading`, etc.
- NO Livewire components: No `<livewire:component-name />`
- Standard `<canvas>` elements for Chart.js (not Filament chart components)
- Pure Blade + Alpine.js implementation

#### 3.2 Dashboard V2 Controller
**File**: `app/Http/Controllers/DashboardV2Controller.php`

**Implementation**:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardV2Controller extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Check if Dashboard V2 is enabled
        if (!config('speedtest.dashboard_v2.enabled')) {
            return redirect()->route('home');
        }

        return view('dashboard-v2');
    }
}
```

**Route Registration** (`routes/web.php`):
```php
// Dashboard V2 route - only accessible when ENABLE_DASHBOARD_V2=true
Route::get('/v2', DashboardV2Controller::class)
    ->middleware(['getting-started', 'public-dashboard'])
    ->name('dashboard.v2');

// Dashboard V1 remains at root - unchanged
// Route::get('/', HomeController::class)
//     ->middleware(['getting-started', 'public-dashboard'])
//     ->name('home');
```

#### 3.3 Alpine.js Dashboard Component
**File**: `resources/js/dashboard.js`

**Implementation**:
```javascript
import { Chart } from 'chart.js/auto';
import 'chartjs-adapter-date-fns';

// Alpine.js component (standalone - NOT Livewire)
// This component is registered in app.js with Alpine.data('dashboard', dashboard)
export function dashboard() {
    return {
        // State
        stats: null,
        statsLoaded: false,
        charts: {},
        range: '24h',
        selectedServer: '',
        servers: [],
        health: {
            health_percentage: 0,
            total_tests: 0,
            successful_tests: 0,
            failed_tests: 0
        },
        downloadStats: { latest: 0, average: 0, lowest: 0, highest: 0 },
        uploadStats: { latest: 0, average: 0, lowest: 0, highest: 0 },
        pingStats: { latest: 0, average: 0, lowest: 0, highest: 0 },
        refreshInterval: null,

        // Initialization
        init() {
            this.loadFilterState();
            this.loadServers();
            this.loadAllData();
            this.startAutoRefresh();
        },

        // Load filter state from localStorage or use defaults
        loadFilterState() {
            const savedState = localStorage.getItem('dashboardV2Filters');

            if (savedState) {
                try {
                    const filters = JSON.parse(savedState);
                    this.range = filters.range || window.defaultChartRange || '24h';
                    this.selectedServer = filters.selectedServer || '';
                } catch (error) {
                    console.error('Failed to parse saved filters:', error);
                    this.range = window.defaultChartRange || '24h';
                }
            } else {
                this.range = window.defaultChartRange || '24h';
            }
        },

        // Save filter state to localStorage
        saveFilterState() {
            try {
                const filters = {
                    range: this.range,
                    selectedServer: this.selectedServer
                };
                localStorage.setItem('dashboardV2Filters', JSON.stringify(filters));
            } catch (error) {
                // localStorage might not be available (incognito mode, privacy settings, etc.)
                console.warn('Failed to save filter state:', error);
            }
        },

        // API Calls
        async loadServers() {
            try {
                const response = await fetch('/api/public/servers');
                const data = await response.json();
                this.servers = data.servers;
            } catch (error) {
                console.error('Failed to load servers:', error);
            }
        },

        async loadStats() {
            try {
                const params = new URLSearchParams();
                if (this.selectedServer) params.append('server_id', this.selectedServer);

                const response = await fetch(`/api/public/stats?${params}`);
                this.stats = await response.json();
                this.statsLoaded = true;
            } catch (error) {
                console.error('Failed to load stats:', error);
            }
        },

        async loadHealth() {
            try {
                const params = new URLSearchParams({ range: this.range });
                if (this.selectedServer) params.append('server_id', this.selectedServer);

                const response = await fetch(`/api/public/health?${params}`);
                this.health = await response.json();
            } catch (error) {
                console.error('Failed to load health:', error);
            }
        },

        async loadStatistics(metric) {
            try {
                const params = new URLSearchParams({ range: this.range });
                if (this.selectedServer) params.append('server_id', this.selectedServer);

                const response = await fetch(`/api/public/statistics/${metric}?${params}`);
                const data = await response.json();

                switch(metric) {
                    case 'download':
                        this.downloadStats = data.statistics;
                        break;
                    case 'upload':
                        this.uploadStats = data.statistics;
                        break;
                    case 'ping':
                        this.pingStats = data.statistics;
                        break;
                }
            } catch (error) {
                console.error(`Failed to load ${metric} statistics:`, error);
            }
        },

        async loadChartData(metric) {
            try {
                const params = new URLSearchParams({ range: this.range });
                if (this.selectedServer) params.append('server_id', this.selectedServer);

                const response = await fetch(`/api/public/charts/${metric}?${params}`);
                return await response.json();
            } catch (error) {
                console.error(`Failed to load ${metric} chart:`, error);
                return null;
            }
        },

        async loadAllData() {
            await Promise.all([
                this.loadStats(),
                this.loadHealth(),
                this.loadStatistics('download'),
                this.loadStatistics('upload'),
                this.loadStatistics('ping')
            ]);
            this.initCharts();
        },

        // Filter Management
        async onFilterChange() {
            this.saveFilterState();
            await this.loadAllData();
            await this.updateAllCharts();
        },

        async resetFilters() {
            this.range = '24h';
            this.selectedServer = '';
            this.saveFilterState();
            await this.onFilterChange();
        },

        // Chart Management
        initCharts() {
            this.initChart('download', this.$refs.downloadChart);
            this.initChart('upload', this.$refs.uploadChart);
            this.initChart('ping', this.$refs.pingChart);
        },

        async initChart(metric, element) {
            const data = await this.loadChartData(metric);
            if (!data) return;

            const ctx = element.getContext('2d');
            this.charts[metric] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.data.map(d => d.timestamp),
                    datasets: [
                        {
                            label: this.getMetricLabel(metric),
                            data: data.data.map(d => d.value),
                            borderColor: this.getMetricColor(metric),
                            backgroundColor: this.getMetricColor(metric, 0.1),
                            fill: true,
                            tension: 0.4,
                            pointRadius: data.data.length <= 24 ? 3 : 0,
                        },
                        {
                            label: 'Average',
                            data: Array(data.data.length).fill(data.average),
                            borderColor: 'rgb(243, 7, 6)',
                            fill: false,
                            tension: 0.4,
                            pointRadius: 0,
                        }
                    ]
                },
                options: this.getChartOptions(metric, data.unit)
            });
        },

        async updateChart(metric) {
            const data = await this.loadChartData(metric);
            if (!data || !this.charts[metric]) return;

            this.charts[metric].data.labels = data.data.map(d => d.timestamp);
            this.charts[metric].data.datasets[0].data = data.data.map(d => d.value);
            this.charts[metric].data.datasets[1].data = Array(data.data.length).fill(data.average);
            this.charts[metric].update();
        },

        // Chart Configuration
        getChartOptions(metric, unit) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: (context) => {
                                return `${context.dataset.label}: ${context.parsed.y} ${unit}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: this.getTimeUnit(),
                            displayFormats: this.getDisplayFormats()
                        }
                    },
                    y: {
                        beginAtZero: window.chartBeginAtZero || false,
                        grace: 2,
                    }
                }
            };
        },

        getMetricLabel(metric) {
            const labels = {
                'download': 'Download',
                'upload': 'Upload',
                'ping': 'Ping',
                'jitter': 'Jitter',
                'download_latency': 'Download Latency',
                'upload_latency': 'Upload Latency'
            };
            return labels[metric] || metric;
        },

        getMetricColor(metric, alpha = 1) {
            const colors = {
                'download': `rgba(14, 165, 233, ${alpha})`,
                'upload': `rgba(59, 130, 246, ${alpha})`,
                'ping': `rgba(16, 185, 129, ${alpha})`,
                'jitter': `rgba(168, 85, 247, ${alpha})`,
                'download_latency': `rgba(249, 115, 22, ${alpha})`,
                'upload_latency': `rgba(236, 72, 153, ${alpha})`
            };
            return colors[metric] || `rgba(100, 100, 100, ${alpha})`;
        },

        getTimeUnit() {
            switch(this.range) {
                case '24h': return 'hour';
                case 'week': return 'day';
                case 'month': return 'day';
                default: return 'hour';
            }
        },

        getDisplayFormats() {
            switch(this.range) {
                case '24h':
                    return {
                        hour: 'MMM d, HH:mm'
                    };
                case 'week':
                case 'month':
                    return {
                        day: 'MMM d'
                    };
                default:
                    return {};
            }
        },

        // Range Filter
        async changeRange(newRange) {
            this.range = newRange;
            await this.updateAllCharts();
        },

        async updateAllCharts() {
            const metrics = ['download', 'upload', 'ping'];
            await Promise.all([
                ...metrics.map(metric => this.updateChart(metric)),
                ...metrics.map(metric => this.loadStatistics(metric))
            ]);
        },

        // Auto-refresh
        startAutoRefresh() {
            this.refreshInterval = setInterval(() => {
                this.loadStats();
                this.loadHealth();
                this.updateAllCharts();
            }, 60000); // 60 seconds
        },

        // Utilities
        formatSpeed(value) {
            if (!value) return 'n/a';
            return `${value.toFixed(2)} Gbps`;
        },

        formatPing(value) {
            if (!value) return 'n/a';
            return `${value.toFixed(1)} ms`;
        },

        formatTimestamp(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            // Use relative time
            const now = new Date();
            const diff = now - date;

            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 60) return `${minutes} minutes ago`;
            if (hours < 24) return `${hours} hours ago`;
            return `${days} days ago`;
        },

        // Cleanup
        destroy() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
            Object.values(this.charts).forEach(chart => chart.destroy());
        }
    };
}

// Make available globally
window.dashboard = dashboard;
```

#### 3.4 Register JavaScript and Initialize Alpine.js
**File**: `resources/js/app.js`

```javascript
import './bootstrap';
import Alpine from 'alpinejs';
import { dashboard } from './dashboard';

// Initialize Alpine.js (standalone - not from Livewire)
window.Alpine = Alpine;

// Register dashboard component
Alpine.data('dashboard', dashboard);

// Start Alpine
Alpine.start();
```

**Important Notes**:
- Alpine.js is imported directly from the `alpinejs` npm package
- We do NOT use `@livewire/alpine` or any Livewire-provided Alpine
- Alpine is initialized independently in this file
- The dashboard component is registered with `Alpine.data()` for cleaner syntax
- No Livewire directives or components are used in Dashboard V2

#### 3.5 Styling Guide

**Design System Reference**: Use **shadcn** (https://ui.shadcn.com/) as the design system reference for Dashboard V2:
- Component patterns and spacing
- Color palettes and semantic colors
- Border radius and shadow conventions
- Typography hierarchy
- Interactive states (hover, focus, active, disabled)
- Animation timings and transitions

**Critical Design Rules**:
- ✅ **DO use shadcn** design patterns as reference
- ✅ **DO manually implement** shadcn styles with Tailwind CSS
- ❌ **DO NOT use Filament** components or styling patterns
- ❌ **DO NOT reference Filament** for design decisions
- ❌ **DO NOT install shadcn** as a dependency (reference only)

Dashboard V2 is completely independent of Filament's design system. All components should follow shadcn design patterns, NOT Filament.

**shadcn Design Principles to Follow**:
1. **Subtle borders**: Use `border-gray-200 dark:border-gray-800` for subtle separation
2. **Soft shadows**: Prefer subtle shadows like `shadow-sm` and `shadow` over heavy shadows
3. **Rounded corners**: Use consistent border radius (`rounded-lg` for cards, `rounded-md` for inputs)
4. **Semantic colors**:
   - Destructive: `red-500` / `red-600`
   - Success: `green-500` / `green-600`
   - Warning: `yellow-500` / `yellow-600`
   - Primary: `blue-500` / `blue-600` (or theme primary)
   - Muted: `gray-500` / `gray-600`
5. **Focus states**: Always include visible focus rings with `focus:ring-2` and appropriate color
6. **Transitions**: Smooth transitions with `duration-200` or `duration-300`
7. **Spacing**: Follow shadcn's spacing patterns (typically `p-4`, `p-6` for cards)

**File**: `resources/css/app.css`

```css
/* Dashboard V2 specific styles - following shadcn design patterns */

/* Chart containers */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-container canvas {
    max-height: 300px;
}

/* Loading states - shadcn skeleton pattern */
.loading-skeleton {
    @apply animate-pulse bg-gray-200 dark:bg-gray-800 rounded-lg;
    height: 250px;
}

/* Filter selects - shadcn select style */
.filter-select {
    @apply h-10 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-800;
    @apply bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-50;
    @apply text-sm font-medium;
    @apply hover:bg-gray-50 dark:hover:bg-gray-900;
    @apply transition-colors duration-200;
    @apply focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600 focus:ring-offset-2;
    @apply disabled:cursor-not-allowed disabled:opacity-50;
}

/* Stat cards - shadcn card pattern */
.stat-card {
    @apply bg-white dark:bg-gray-950 rounded-lg shadow-sm p-6;
    @apply border border-gray-200 dark:border-gray-800;
    @apply transition-colors duration-200;
}

.stat-value {
    @apply text-3xl font-bold text-gray-900 dark:text-gray-50 mt-2;
    @apply tracking-tight;
}

.stat-label {
    @apply text-sm font-medium text-gray-500 dark:text-gray-400;
    @apply uppercase tracking-wide;
}

.stat-unit {
    @apply text-xl font-normal text-gray-500 dark:text-gray-400 ml-1;
}

/* Health bar - custom component with shadcn colors */
.health-bar {
    @apply flex h-8 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-900;
    @apply border border-gray-200 dark:border-gray-800;
}

.health-bar-fill {
    @apply h-full transition-all duration-500 ease-in-out;
}

/* Progress bar - shadcn progress pattern */
.progress-bar {
    @apply h-4 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-900;
    @apply border border-gray-200 dark:border-gray-800;
}

.progress-bar-fill {
    @apply h-full bg-blue-500 dark:bg-blue-600 transition-all duration-500;
}

/* Buttons - shadcn button variants */
.btn {
    @apply inline-flex items-center justify-center rounded-md px-4 py-2;
    @apply text-sm font-medium;
    @apply transition-colors duration-200;
    @apply focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2;
    @apply disabled:pointer-events-none disabled:opacity-50;
}

.btn-outline {
    @apply border border-gray-200 dark:border-gray-800;
    @apply bg-white dark:bg-gray-950;
    @apply text-gray-900 dark:text-gray-50;
    @apply hover:bg-gray-100 dark:hover:bg-gray-900;
    @apply focus-visible:ring-gray-400 dark:focus-visible:ring-gray-600;
}

.btn-primary {
    @apply border border-transparent;
    @apply bg-gray-900 dark:bg-gray-50 text-white dark:text-gray-900;
    @apply hover:bg-gray-800 dark:hover:bg-gray-200;
    @apply focus-visible:ring-gray-400 dark:focus-visible:ring-gray-600;
}

.btn-destructive {
    @apply border border-transparent;
    @apply bg-red-500 dark:bg-red-600 text-white;
    @apply hover:bg-red-600 dark:hover:bg-red-700;
    @apply focus-visible:ring-red-400 dark:focus-visible:ring-red-600;
}

.btn-ghost {
    @apply border border-transparent;
    @apply bg-transparent;
    @apply text-gray-900 dark:text-gray-50;
    @apply hover:bg-gray-100 dark:hover:bg-gray-800;
    @apply focus-visible:ring-gray-400 dark:focus-visible:ring-gray-600;
}

/* Error states - shadcn alert destructive variant */
.error-message {
    @apply bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800;
    @apply text-red-900 dark:text-red-50 rounded-lg p-4;
}

.error-title {
    @apply font-semibold text-sm mb-1;
}

.error-description {
    @apply text-sm opacity-90;
}

/* Success states - shadcn alert success variant */
.success-message {
    @apply bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800;
    @apply text-green-900 dark:text-green-50 rounded-lg p-4;
}

/* Beta badge */
.beta-badge {
    @apply inline-flex items-center px-2 py-1 text-xs font-medium rounded-full;
    @apply bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300;
}

/* Empty states - shadcn empty state pattern */
.empty-state {
    @apply text-center py-12 px-4;
    @apply border border-dashed border-gray-200 dark:border-gray-800 rounded-lg;
    @apply bg-gray-50 dark:bg-gray-900;
}

.empty-state-icon {
    @apply text-gray-400 dark:text-gray-600 text-5xl mb-3;
}

.empty-state-title {
    @apply text-sm font-semibold text-gray-900 dark:text-gray-50 mb-1;
}

.empty-state-text {
    @apply text-sm text-gray-500 dark:text-gray-400;
}
```

### Phase 4: Testing (Day 4)

#### 4.1 API Tests
**File**: `tests/Feature/Api/Public/StatsControllerTest.php`

```php
<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns latest stats', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed])
        ->count(2)
        ->create();

    $response = $this->getJson('/api/public/stats');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'latest' => [
                'download',
                'upload',
                'ping',
                'status',
                'healthy',
                'server_id',
                'server_name',
                'created_at'
            ],
        ]);
});

it('returns latest stats filtered by server', function () {
    $server1Results = Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 12345])
        ->count(3)
        ->create();

    $server2Results = Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 67890])
        ->count(2)
        ->create();

    $response = $this->getJson('/api/public/stats?server_id=12345');

    $response->assertSuccessful()
        ->assertJson([
            'latest' => [
                'server_id' => 12345,
            ],
        ]);
});

it('returns null when no results exist', function () {
    $response = $this->getJson('/api/public/stats');

    $response->assertSuccessful()
        ->assertJson([
            'latest' => null,
        ]);
});
```

**File**: `tests/Feature/Api/Public/StatisticsControllerTest.php` (NEW)

```php
<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns statistics for download metric', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed])
        ->count(10)
        ->create();

    $response = $this->getJson('/api/public/statistics/download?range=24h');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'metric',
            'range',
            'statistics' => ['latest', 'average', 'lowest', 'highest'],
            'unit',
        ]);
});

it('calculates correct statistics', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'download' => 100])
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'download' => 200])
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'download' => 300])
        ->create();

    $response = $this->getJson('/api/public/statistics/download?range=24h');

    $response->assertSuccessful()
        ->assertJson([
            'statistics' => [
                'latest' => 300,
                'average' => 200,
                'lowest' => 100,
                'highest' => 300,
            ],
        ]);
});

it('filters statistics by server', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 12345])
        ->count(5)
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 67890])
        ->count(3)
        ->create();

    $response = $this->getJson('/api/public/statistics/download?server_id=12345');

    $response->assertSuccessful();
    // Verify response only includes data from server 12345
});

it('validates metric parameter', function () {
    $response = $this->getJson('/api/public/statistics/invalid');

    $response->assertUnprocessable();
});
```

**File**: `tests/Feature/Api/Public/ChartDataControllerTest.php`

```php
<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns chart data for download metric', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed])
        ->count(10)
        ->create();

    $response = $this->getJson('/api/public/charts/download?range=24h');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'metric',
            'range',
            'data' => [
                '*' => ['value', 'timestamp']
            ],
            'average',
            'unit',
        ]);
});

it('validates metric parameter', function () {
    $response = $this->getJson('/api/public/charts/invalid');

    $response->assertUnprocessable();
});

it('validates range parameter', function () {
    $response = $this->getJson('/api/public/charts/download?range=invalid');

    $response->assertUnprocessable();
});

it('filters data by 24h range', function () {
    // Create old results
    Result::factory()
        ->state([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subDays(2),
        ])
        ->count(5)
        ->create();

    // Create recent results
    Result::factory()
        ->state([
            'status' => ResultStatus::Completed,
            'created_at' => now()->subHours(12),
        ])
        ->count(3)
        ->create();

    $response = $this->getJson('/api/public/charts/download?range=24h');

    $response->assertSuccessful();

    $data = $response->json('data');
    expect($data)->toHaveCount(3);
});

it('filters chart data by server', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 12345])
        ->count(5)
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 67890])
        ->count(3)
        ->create();

    $response = $this->getJson('/api/public/charts/download?server_id=12345');

    $response->assertSuccessful();
    // Verify response only includes data from server 12345
});
```

**File**: `tests/Feature/Api/Public/HealthControllerTest.php` (NEW)

```php
<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns health data', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed])
        ->count(8)
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Failed])
        ->count(2)
        ->create();

    $response = $this->getJson('/api/public/health?range=24h');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'range',
            'latest_status',
            'health_percentage',
            'total_tests',
            'successful_tests',
            'failed_tests',
            'health_over_time',
        ]);
});

it('calculates correct health percentage', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed])
        ->count(9)
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Failed])
        ->count(1)
        ->create();

    $response = $this->getJson('/api/public/health?range=24h');

    $response->assertSuccessful()
        ->assertJson([
            'health_percentage' => 90,
            'total_tests' => 10,
            'successful_tests' => 9,
            'failed_tests' => 1,
        ]);
});

it('filters health data by server', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 12345])
        ->count(5)
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 67890])
        ->count(3)
        ->create();

    $response = $this->getJson('/api/public/health?server_id=12345');

    $response->assertSuccessful()
        ->assertJson([
            'total_tests' => 5,
        ]);
});
```

**File**: `tests/Feature/Api/Public/ServersControllerTest.php` (NEW)

```php
<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns list of servers', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 12345])
        ->count(5)
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 67890])
        ->count(3)
        ->create();

    $response = $this->getJson('/api/public/servers');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'servers' => [
                '*' => ['id', 'name', 'host', 'location', 'test_count']
            ]
        ]);
});

it('returns servers ordered by test count', function () {
    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 12345])
        ->count(10)
        ->create();

    Result::factory()
        ->state(['status' => ResultStatus::Completed, 'server_id' => 67890])
        ->count(5)
        ->create();

    $response = $this->getJson('/api/public/servers');

    $response->assertSuccessful();

    $servers = $response->json('servers');
    expect($servers[0]['test_count'])->toBeGreaterThanOrEqual($servers[1]['test_count']);
});
```

#### 4.2 Frontend Tests
- [ ] Test chart rendering for all three metrics (download, upload, ping)
- [ ] Test time range filter changes
- [ ] Test server filter changes
- [ ] Test reset filters functionality
- [ ] Test filter state persistence in localStorage
  - [ ] Verify filters are saved when changed
  - [ ] Verify filters are restored on page reload
  - [ ] Verify reset clears localStorage state
- [ ] Test auto-refresh for all data
- [ ] Test error handling and empty states
- [ ] Test dark mode compatibility
- [ ] Test health bar visualization
- [ ] Test statistics cards display
- [ ] Test loading states

### Phase 5: Performance Optimization (Day 4-5)

#### 5.1 Caching Strategy
- [ ] Implement cache for stats endpoint (5 minute TTL)
- [ ] Implement cache for chart data (1-5 minute TTL based on range)
- [ ] Add cache tags for easy invalidation
- [ ] Invalidate cache when new results are created

#### 5.2 Query Optimization
- [ ] Add database indexes for frequently queried columns
- [ ] Optimize queries with proper select statements
- [ ] Consider pagination for large datasets

#### 5.3 Response Optimization
- [ ] Compress API responses
- [ ] Add proper cache headers
- [ ] Consider CDN for static assets

### Phase 6: Configuration & Documentation (Day 5)

#### 6.1 Configuration
- [ ] Add configuration for public API rate limiting
- [ ] Add configuration for cache TTLs
- [ ] Add `ENABLE_DASHBOARD_V2` env variable to enable/disable V2 access
- [ ] Ensure `PUBLIC_DASHBOARD` env variable works correctly for both dashboards
- [ ] V1 remains at `/` (unchanged)
- [ ] V2 accessible at `/v2` only when enabled

#### 6.2 Documentation
- [ ] Update README with new architecture
- [ ] Document API endpoints
- [ ] Add comments to Alpine component
- [ ] Create troubleshooting guide
- [ ] Document V2 access pattern (environment variable gated)
- [ ] Document differences between V1 and V2

## Technical Specifications

### API Specifications

#### Rate Limiting
- Public API endpoints: 100 requests per minute per IP
- Implemented via Laravel's rate limiting middleware

#### Caching
```php
// Stats endpoint
Cache::tags(['public', 'stats'])
    ->remember('public:stats', now()->addMinutes(5), function () {
        // Query logic
    });

// Chart data endpoint
Cache::tags(['public', 'charts', "chart:{$metric}"])
    ->remember("public:chart:{$metric}:{$range}", $ttl, function () {
        // Query logic
    });

// Cache invalidation on new result
Result::created(function ($result) {
    Cache::tags(['public'])->flush();
});
```

#### Response Format
All API responses follow this structure:
```json
{
  "data": { /* actual data */ },
  "meta": {
    "cached": true,
    "cached_at": "2025-11-26T10:30:00Z"
  }
}
```

### Frontend Specifications

#### Browser Support
- Modern browsers with ES6+ support
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

#### Dependencies
**Required npm packages** (add to `package.json`):
```json
{
  "alpinejs": "^3.13.0",
  "chart.js": "^4.4.0",
  "chartjs-adapter-date-fns": "^3.0.0",
  "date-fns": "^3.0.0"
}
```

**Important**:
- Alpine.js is installed as a standalone package from npm
- Do NOT rely on Alpine.js bundled with Livewire
- Dashboard V2 has no Livewire dependencies
- All JavaScript is vanilla or Alpine-based (no Livewire wire: directives)

#### Filter State Persistence
**Storage Method**: Browser's localStorage API
**Storage Key**: `dashboardV2Filters`
**Stored Data**:
```json
{
  "range": "24h|week|month",
  "selectedServer": "server_id|empty_string"
}
```

**Behavior**:
- Filters are saved automatically when changed
- Filters persist across browser sessions
- Filters are restored on page load
- Reset button clears both UI state and localStorage
- Graceful fallback to defaults if localStorage is unavailable or corrupted

**Privacy Considerations**:
- Only filter preferences are stored (no sensitive data)
- User can clear via reset button or browser's localStorage clear
- No tracking or analytics data stored

#### Performance Targets
- Initial page load: < 2 seconds
- Chart render: < 500ms
- API response time: < 100ms (cached), < 500ms (uncached)
- Time to Interactive (TTI): < 3 seconds
- localStorage read/write: < 5ms

### Security Considerations

#### Public API Access
- No authentication required (existing behavior)
- Rate limiting to prevent abuse
- Only expose completed results
- No sensitive data in responses
- CORS configuration if needed

#### Input Validation
- Validate all query parameters
- Whitelist allowed metrics and ranges
- Sanitize any user input
- Prevent SQL injection via parameterized queries

#### localStorage Security
- Only store non-sensitive filter preferences (time range, server ID)
- No user credentials, tokens, or personal data stored
- Data is client-side only, not transmitted to server
- Users can clear via browser settings or reset button
- Graceful degradation if localStorage is unavailable (incognito mode)
- JSON parsing wrapped in try-catch to prevent XSS from corrupted data

## Testing Strategy

### Test Coverage Goals
- API Endpoints: 100% coverage
- Controllers: 100% coverage
- Resources: 90% coverage
- Frontend: Manual testing (E2E tests optional)

### Test Types
1. **Unit Tests**: Individual methods and functions
2. **Feature Tests**: API endpoint behavior
3. **Integration Tests**: Full data flow
4. **Browser Tests** (Optional): Dusk tests for frontend

### Test Data
- Use factories for consistent test data
- Test edge cases (no data, single data point, large datasets)
- Test all time ranges
- Test all metrics

## Rollout Plan

### Phase 1: Development (Days 1-5)
- Complete all implementation phases
- Achieve test coverage goals
- Performance testing

### Phase 2: Internal Testing (Day 5)
- Deploy to staging environment
- Set `ENABLE_DASHBOARD_V2=true` in staging
- Manual testing of `/v2` route
- Performance validation
- Cross-browser testing
- V1 at `/` remains default and unchanged

### Phase 3: Testing in Production (Week 1-2)
- Deploy to production with `ENABLE_DASHBOARD_V2=false` (disabled by default)
- Enable for testing: Set `ENABLE_DASHBOARD_V2=true`
- Access V2 via direct URL: `/v2`
- Monitor V2 performance and stability
- Fix any issues reported
- V1 remains primary dashboard for users

### Phase 4: Gradual Rollout (Week 3-4)
- Keep `ENABLE_DASHBOARD_V2=true` for continued testing
- Consider making V2 the primary dashboard if stable
- V1 remains available at `/` for fallback

### Phase 5: Full Migration (Future)
- After V2 proves stable for 1+ month
- Redirect `/` to V2, or replace V1 entirely
- Optionally remove V1 or keep as legacy option
- Remove Filament widgets from public dashboard only

### Rollback Plan
If issues arise with V2:
1. Set `ENABLE_DASHBOARD_V2=false` to disable V2 access
2. `/v2` route will redirect to `/` (V1)
3. Fix issues in V2
4. Re-enable when ready
5. V1 remains fully functional throughout

## Success Metrics

### Performance Metrics (V2 vs V1)
- [ ] Server response time reduced by > 80% (compared to V1)
- [ ] Page load time < 2 seconds
- [ ] Lighthouse performance score > 90
- [ ] No N+1 queries
- [ ] Initial chart render < 500ms
- [ ] Memory usage comparable or better than V1

### User Experience Metrics
- [ ] All charts render correctly (download, upload, ping)
- [ ] Time range and server filters work smoothly without page reload
- [ ] Reset filters button clears all selections
- [ ] Filter state persists across page reloads and browser sessions
- [ ] Saved filters are automatically restored when returning to the page
- [ ] Statistics cards display correct values (latest, average, lowest, highest)
- [ ] Health bar shows correct percentage and colors
- [ ] Auto-refresh functions properly for all data
- [ ] Dark mode works correctly
- [ ] Mobile responsive layout adapts well
- [ ] Graceful error handling for API failures
- [ ] Loading states provide good feedback
- [ ] Empty states display when no data available

### Technical Metrics
- [ ] Test coverage > 95%
- [ ] No console errors
- [ ] API response times within targets
- [ ] Cache hit rate > 80%
- [ ] Both V1 and V2 remain functional
- [ ] Environment variable gating works correctly

### Testing Metrics
- [ ] Monitor V2 performance when enabled
- [ ] Track error rates for V2
- [ ] Compare performance between V1 and V2
- [ ] Validate redirect behavior when V2 disabled

## Future Enhancements

These features can be added after the initial implementation:

### Real-time Updates (WebSockets with Laravel Reverb)

**Goal**: Replace 60-second polling with real-time push notifications for instant dashboard updates using Laravel Reverb.

**Use Case**: Users want immediate updates when speedtests complete without waiting for the next poll cycle.

**Technology**: Laravel Reverb - Laravel's official WebSocket server (native, self-hosted, free, built on ReactPHP)

#### Requirements

**Backend Setup**:

1. **Install Laravel Reverb**:
   ```bash
   php artisan install:broadcasting
   ```
   This installs Reverb and publishes configuration files.

2. **Install Reverb Package**:
   ```bash
   composer require laravel/reverb
   ```

3. **Create Broadcast Events**:
   ```php
   // app/Events/SpeedtestResultCompleted.php
   <?php

   namespace App\Events;

   use App\Models\Result;
   use Illuminate\Broadcasting\Channel;
   use Illuminate\Broadcasting\InteractsWithSockets;
   use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
   use Illuminate\Foundation\Events\Dispatchable;
   use Illuminate\Queue\SerializesModels;

   class SpeedtestResultCompleted implements ShouldBroadcast
   {
       use Dispatchable, InteractsWithSockets, SerializesModels;

       public function __construct(
           public Result $result
       ) {}

       public function broadcastOn(): array
       {
           return [
               new Channel('speedtest-results'),
           ];
       }

       public function broadcastAs(): string
       {
           return 'speedtest.completed';
       }

       public function broadcastWith(): array
       {
           return [
               'id' => $this->result->id,
               'download' => $this->result->download,
               'upload' => $this->result->upload,
               'ping' => $this->result->ping,
               'status' => $this->result->status,
               'created_at' => $this->result->created_at->toIso8601String(),
           ];
       }
   }
   ```

4. **Trigger Events**:
   ```php
   // In your speedtest completion logic (e.g., after saving result)
   use App\Events\SpeedtestResultCompleted;

   // When a speedtest completes
   event(new SpeedtestResultCompleted($result));
   ```

5. **Server Infrastructure**:
   - Reverb WebSocket server process (runs separately from Laravel)
   - Port 8080 (Reverb HTTP) and 6001 (WebSocket) accessible
   - Process manager (Supervisor) to keep Reverb running
   - ~50-100MB RAM base + ~1-2MB per 1000 concurrent connections

**Frontend Setup**:

1. **Install Laravel Echo**:
   ```bash
   npm install --save-dev laravel-echo pusher-js
   ```
   Note: Even though we use Reverb, we still need `pusher-js` as Echo uses the Pusher protocol.

2. **Configure Echo for Reverb**:
   ```javascript
   // resources/js/echo.js (new file)
   import Echo from 'laravel-echo';
   import Pusher from 'pusher-js';

   window.Pusher = Pusher;

   window.Echo = new Echo({
       broadcaster: 'reverb',
       key: import.meta.env.VITE_REVERB_APP_KEY,
       wsHost: import.meta.env.VITE_REVERB_HOST,
       wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
       wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
       forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
       enabledTransports: ['ws', 'wss'],
   });

   export default Echo;
   ```

3. **Update app.js**:
   ```javascript
   import './bootstrap';
   import Alpine from 'alpinejs';
   import { dashboard } from './dashboard';
   import Echo from './echo'; // Import Echo configuration

   // Initialize Alpine.js (standalone - not from Livewire)
   window.Alpine = Alpine;

   // Register dashboard component
   Alpine.data('dashboard', dashboard);

   // Start Alpine
   Alpine.start();
   ```

4. **Integrate with Alpine.js Dashboard Component**:
   ```javascript
   // resources/js/dashboard.js
   export function dashboard() {
       return {
           // ... existing state ...

           init() {
               this.loadFilterState();
               this.loadServers();
               this.loadAllData();
               this.setupReverbConnection(); // NEW: Replace startAutoRefresh()
           },

           setupReverbConnection() {
               if (!window.Echo) {
                   console.warn('Echo not available, falling back to polling');
                   this.startAutoRefresh();
                   return;
               }

               // Listen for new speedtest results
               window.Echo.channel('speedtest-results')
                   .listen('.speedtest.completed', (event) => {
                       console.log('New speedtest result received:', event);

                       // Update dashboard with new data
                       this.loadStats();
                       this.loadHealth();
                       this.updateAllCharts();
                   });

               // Handle connection status
               window.Echo.connector.pusher.connection.bind('connected', () => {
                   console.log('✅ Connected to Reverb');
               });

               window.Echo.connector.pusher.connection.bind('disconnected', () => {
                   console.warn('❌ Disconnected from Reverb, falling back to polling');
                   this.startAutoRefresh();
               });

               window.Echo.connector.pusher.connection.bind('error', (error) => {
                   console.error('Reverb connection error:', error);
                   this.startAutoRefresh(); // Fallback
               });
           },

           destroy() {
               // Clean up Reverb connection
               if (window.Echo) {
                   window.Echo.leaveChannel('speedtest-results');
               }

               if (this.refreshInterval) {
                   clearInterval(this.refreshInterval);
               }

               Object.values(this.charts).forEach(chart => chart.destroy());
           }
       };
   }
   ```

**Configuration**:

1. **Environment Variables** (`.env`):
   ```bash
   BROADCAST_CONNECTION=reverb

   REVERB_APP_ID=your-app-id
   REVERB_APP_KEY=your-app-key
   REVERB_APP_SECRET=your-app-secret
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http

   # For frontend (Vite)
   VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
   VITE_REVERB_HOST="${REVERB_HOST}"
   VITE_REVERB_PORT="${REVERB_PORT}"
   VITE_REVERB_SCHEME="${REVERB_SCHEME}"
   ```

2. **Reverb Configuration** (`config/reverb.php`):
   ```php
   return [
       'servers' => [
           'reverb' => [
               'host' => env('REVERB_HOST', '0.0.0.0'),
               'port' => env('REVERB_PORT', 8080),
               'hostname' => env('REVERB_HOSTNAME', 'localhost'),
               'options' => [
                   'tls' => [],
               ],
               'scaling' => [
                   'enabled' => env('REVERB_SCALING_ENABLED', false),
                   'channel' => env('REVERB_SCALING_CHANNEL', 'reverb'),
               ],
           ],
       ],

       'apps' => [
           'provider' => 'config',
           'apps' => [
               [
                   'id' => env('REVERB_APP_ID'),
                   'key' => env('REVERB_APP_KEY'),
                   'secret' => env('REVERB_APP_SECRET'),
                   'capacity' => null,
                   'allowed_origins' => ['*'],
               ],
           ],
       ],
   ];
   ```

3. **Broadcasting Configuration** (`config/broadcasting.php`):
   ```php
   'connections' => [
       'reverb' => [
           'driver' => 'reverb',
           'key' => env('REVERB_APP_KEY'),
           'secret' => env('REVERB_APP_SECRET'),
           'app_id' => env('REVERB_APP_ID'),
           'options' => [
               'host' => env('REVERB_HOST', '127.0.0.1'),
               'port' => env('REVERB_PORT', 8080),
               'scheme' => env('REVERB_SCHEME', 'http'),
           ],
       ],
   ],
   ```

**Running Reverb**:

1. **Development**:
   ```bash
   php artisan reverb:start
   ```
   Add `--debug` flag for verbose logging during development.

2. **Production with Supervisor**:
   ```ini
   # /etc/supervisor/conf.d/reverb.conf
   [program:reverb]
   command=php /path/to/your/project/artisan reverb:start --host=0.0.0.0 --port=8080
   directory=/path/to/your/project
   autostart=true
   autorestart=true
   user=www-data
   redirect_stderr=true
   stdout_logfile=/path/to/your/project/storage/logs/reverb.log
   stopwaitsecs=60
   ```

   Then:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start reverb
   ```

**Deployment**:
- Reverb runs as a separate long-running process
- Reverse proxy (Nginx) configuration for WebSocket:
  ```nginx
  location /reverb {
      proxy_pass http://localhost:8080;
      proxy_http_version 1.1;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "Upgrade";
      proxy_set_header Host $host;
      proxy_cache_bypass $http_upgrade;
  }
  ```
- For horizontal scaling: Enable Redis scaling in `config/reverb.php`
- Monitor Reverb process uptime with Supervisor
- Load balancer needs WebSocket support (sticky sessions if multiple Reverb instances)

#### Benefits
- ✅ Instant updates (vs. up to 60 seconds delay)
- ✅ Reduced API calls (~60 fewer requests per hour per user)
- ✅ Lower server load (push vs. pull)
- ✅ Better user experience (live updates feel more modern)
- ✅ Enables future features (live test progress, notifications)

#### Costs
- ⚠️ Additional infrastructure complexity
- ⚠️ Persistent connections consume memory
- ⚠️ Requires monitoring WebSocket server
- ⚠️ Additional bundle size (~50KB for Echo + client library)
- ⚠️ More complex deployment process

#### Implementation Strategy
1. Keep existing polling as fallback mechanism
2. Implement WebSocket connection with graceful degradation
3. Add connection status indicator (connected/disconnected)
4. Implement automatic reconnection logic
5. Add feature flag to enable/disable WebSockets: `ENABLE_REALTIME_UPDATES`

#### Testing
- Test WebSocket connection establishment
- Test event broadcasting on speedtest completion
- Test reconnection after disconnect
- Test fallback to polling when WebSocket unavailable
- Load testing with multiple concurrent connections

#### Estimated Effort
- Backend implementation: 4-6 hours
- Frontend integration: 4-6 hours
- Testing and debugging: 4-6 hours
- Deployment configuration: 2-4 hours
- **Total**: 2-3 days

#### When to Implement
Consider implementing WebSockets when:
- Dashboard V2 is stable and widely adopted
- Users specifically request real-time updates
- Speedtests run very frequently (every 1-5 minutes)
- Polling becomes a performance bottleneck
- Adding live speedtest progress visualization

**Not Recommended For**:
- Initial V2 release (adds complexity without critical need)
- Low-frequency speedtests (hourly or less frequent)
- Limited server resources (polling is more efficient at small scale)

### Advanced Filtering
- Date range picker with custom date selection
- Metric comparison view
- Multiple server selection (not just single)

### Enhanced Filter Persistence
- Consider using Alpine.js persist plugin for more robust state management
- Sync filter state across multiple tabs/windows
- Add user preference profiles (save multiple filter configurations)

### Export Features
- Export chart data as CSV
- Generate PDF reports
- Share chart snapshots

### Customization
- User-configurable refresh intervals
- Chart type selection (line, bar, area)
- Color theme customization
- Toggleable statistics (show/hide specific metrics)

### Analytics
- Track which metrics are viewed most
- Popular time ranges
- Average session duration

## Appendix

### File Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php (existing, unchanged)
│   │   ├── DashboardV2Controller.php (new)
│   │   └── Api/
│   │       └── Public/
│   │           ├── StatsController.php (new)
│   │           ├── StatisticsController.php (new)
│   │           ├── ChartDataController.php (new)
│   │           ├── HealthController.php (new)
│   │           └── ServersController.php (new)
│   ├── Resources/
│   │   └── Public/
│   │       ├── StatResource.php (new)
│   │       ├── StatisticsResource.php (new)
│   │       ├── ChartDataResource.php (new)
│   │       ├── HealthResource.php (new)
│   │       └── ServerResource.php (new)
│   └── Middleware/
│       └── PublicApiRateLimit.php (new)
├── Services/
│   ├── ChartDataService.php (new)
│   ├── StatisticsService.php (new)
│   └── HealthService.php (new)
└── Helpers/
    └── ChartFormatter.php (new)

resources/
├── js/
│   ├── app.js (modified)
│   └── dashboard.js (new)
├── css/
│   └── app.css (modified)
└── views/
    ├── dashboard.blade.php (existing, unchanged)
    └── dashboard-v2.blade.php (new)

routes/
├── web.php (add /v2 route)
└── api/
    └── public.php (new)

tests/
└── Feature/
    ├── DashboardV2Test.php (new)
    └── Api/
        └── Public/
            ├── StatsControllerTest.php (new)
            ├── StatisticsControllerTest.php (new)
            ├── ChartDataControllerTest.php (new)
            ├── HealthControllerTest.php (new)
            └── ServersControllerTest.php (new)
```

### Environment Variables
```bash
# Public Dashboard
PUBLIC_DASHBOARD=true
DEFAULT_CHART_RANGE=24h

# Dashboard V2 Configuration
# Set to true to enable access to /v2 route
ENABLE_DASHBOARD_V2=false

# Chart Configuration
CHART_BEGIN_AT_ZERO=false
CHART_DATETIME_FORMAT="M j, H:i"

# Cache Configuration
PUBLIC_API_CACHE_TTL=300
CHART_CACHE_TTL=60
STATS_CACHE_TTL=60
HEALTH_CACHE_TTL=60
SERVERS_CACHE_TTL=600

# Rate Limiting
PUBLIC_API_RATE_LIMIT=100
```

### Configuration Files

**config/speedtest.php** (additions):
```php
'dashboard_v2' => [
    'enabled' => env('ENABLE_DASHBOARD_V2', false),
],

'public_api' => [
    'cache_ttl' => env('PUBLIC_API_CACHE_TTL', 300),
    'chart_cache_ttl' => env('CHART_CACHE_TTL', 60),
    'stats_cache_ttl' => env('STATS_CACHE_TTL', 60),
    'health_cache_ttl' => env('HEALTH_CACHE_TTL', 60),
    'servers_cache_ttl' => env('SERVERS_CACHE_TTL', 600),
    'rate_limit' => env('PUBLIC_API_RATE_LIMIT', 100),
],
```

### Useful Commands

```bash
# Install dependencies (includes Alpine.js standalone)
npm install alpinejs chart.js chartjs-adapter-date-fns date-fns

# Verify Alpine.js is installed
npm list alpinejs

# Build assets (compiles Alpine.js + Chart.js)
npm run build

# Development mode (watch for changes)
npm run dev

# Run tests
php artisan test --filter=Public

# Clear cache
php artisan cache:clear
php artisan config:clear

# Run pint
vendor/bin/pint --dirty

# Verify no Livewire on Dashboard V2 page
# 1. Visit /v2 in browser
# 2. Open DevTools Console
# 3. Check: window.Alpine should be defined
# 4. Check: window.Livewire should be undefined
```

## Dashboard Comparison: V1 vs V2

| Feature | Dashboard V1 (Existing) | Dashboard V2 (New) |
|---------|------------------------|-------------------|
| **Rendering** | Server-side (Livewire) | Client-side (ChartJS) |
| **Data Fetching** | Included in page load | API calls (fetch) |
| **Charts Displayed** | 7 charts (Download, Upload, Ping, Jitter, DL Latency, UL Latency, Stats) | 3 core charts (Download, Upload, Ping) |
| **Statistics** | Latest values with trend % | Latest, Average, Lowest, Highest |
| **Filtering** | Time range only | Time range + Server selection |
| **Filter Persistence** | Not available | localStorage (remembers preferences) |
| **Health Monitoring** | Not available | Test health status + visualization |
| **Auto-refresh** | Full component re-render (7 widgets) | Targeted data updates |
| **Filter Changes** | Page reload/re-render | Instant client-side update |
| **Performance** | Heavy server load | Minimal server load |
| **Caching** | Limited | Aggressive (API level) |
| **Extensibility** | Limited by Filament | Highly extensible |
| **Initial Load** | Slower (all data rendered) | Faster (progressive loading) |
| **Dependencies** | Filament, Livewire | **Alpine.js (standalone), ChartJS** |
| **Code Complexity** | Widget-based | Component-based (Alpine.data) |
| **Real-time Updates** | Polling with full re-render | Can add WebSockets easily |
| **Bundle Size** | Larger (Livewire + Filament) | **Smaller (no Livewire overhead)** |
| **Browser Support** | Modern | Modern (ES6+) |
| **Maintenance** | Tied to Filament updates | **Independent (no Livewire/Filament)** |
| **JavaScript Patterns** | Livewire wire: directives | **Alpine x: directives + vanilla JS** |
| **Access** | `/` route | `/v2` route (env-gated) |
| **Status** | Stable, proven | Beta, experimental |
| **Layout** | Vertical list | Card-based grid |
| **Design System** | Filament styling | **shadcn design patterns (reference only)** |

## Questions & Decisions

### Open Questions
1. Should we add authentication option for public API in the future?
2. Should we implement GraphQL for more flexible querying?
3. Should we add WebSocket support for real-time updates in V2?
4. After V2 is stable, should we remove V1 or keep both indefinitely?
5. When should we make V2 the default dashboard at `/`?
6. Should health percentage trigger alerts when below certain thresholds?
7. Should we allow users to customize which statistics are displayed?
8. Should we add the ability to export statistics data?
9. Should we support multiple server selection (not just one at a time)?

### Design Decisions Made
1. ✅ **Use Alpine.js (standalone) over vanilla JS** - Installed as explicit npm dependency, NOT from Livewire
2. ✅ **Zero Livewire dependency** - Dashboard V2 is completely independent of Livewire/Filament
3. ✅ Implement caching at API level (better performance, simpler frontend)
4. ✅ Keep admin dashboard using Filament (separation of concerns, V1 unchanged)
5. ✅ Use ChartJS over other libraries (widely supported, good documentation)
6. ✅ Public API without authentication (matches current behavior, optional auth for future)
7. ✅ Create V2 at `/v2` route (separate from V1, lower risk)
8. ✅ Gate V2 access via environment variable (testing only)
9. ✅ No UI toggle - environment variable controlled access only
10. ✅ Focus on core metrics (Download, Upload, Ping) - remove less-used charts (Jitter, Latency)
11. ✅ Add comprehensive statistics (Latest, Average, Lowest, Highest)
12. ✅ Implement server filtering for multi-server setups
13. ✅ Add health monitoring for test success tracking
14. ✅ Card-based grid layout for better organization and mobile responsiveness
15. ✅ Persist filter state using browser's localStorage (enhance UX by remembering preferences)
16. ✅ **Use standard JavaScript patterns** - fetch API for requests, Alpine.data() for components
17. ✅ **Smaller bundle size** - No Livewire overhead, only Alpine.js + Chart.js
18. ✅ **Use shadcn as design system reference** - NOT installed as dependency, reference for UI patterns, colors, spacing, and component styles
19. ✅ **NO Filament design patterns** - Dashboard V2 does NOT use Filament components, styling, or design patterns; exclusively uses shadcn-inspired design
20. ❌ Quota tracking widget - Deferred to future version (not ready for V2)

---

**Document Version**: 2.4
**Last Updated**: 2025-11-26
**Author**: Claude Code
**Status**: Updated - Filament design reference removed, shadcn is now the exclusive design system

**Changes in v2.4**:
- **Removed Filament as design reference** - Dashboard V2 does NOT use Filament components or styling
- **shadcn is the exclusive design system** - All UI components must follow shadcn patterns
- Added "Critical Design Rules" section explicitly prohibiting Filament styling
- Updated Phase 1 checklist to verify NO Filament components or CSS classes
- Added design system verification checklist (shadcn YES, Filament NO)
- Updated dashboard layout section to clarify shadcn-only styling
- Updated frontend checklist with specific shadcn class examples

**Changes in v2.3**:
- **Added shadcn as design system reference** - Use shadcn (https://ui.shadcn.com/) for UI patterns, component styles, colors, and spacing
- **NOT a dependency** - shadcn is reference only, manually implement styles with Tailwind CSS
- Updated styling section with comprehensive shadcn design principles
- Added shadcn-based CSS classes for buttons, cards, alerts, and form controls
- Updated comparison table to highlight shadcn design patterns
- Added verification checklist for shadcn design principles

**Changes in v2.2**:
- **Clarified Alpine.js is standalone** - Explicit npm dependency, NOT from Livewire
- Updated all references to emphasize zero Livewire dependency
- Added verification checklist to ensure no Livewire assets are used
- Updated architecture diagrams and technology stack
- Added Alpine.js initialization code (Alpine.data pattern)
- Emphasized standard JavaScript patterns (fetch API, localStorage)
- Added bundle size checks and verification steps

**Changes in v2.1**:
- Reorganized implementation into 3 independent phases
  - Phase 1: Core infrastructure, filters, health monitoring, and Download metrics
  - Phase 2: Upload metrics
  - Phase 3: Ping metrics
- Created detailed checklists for each phase
- Added completion criteria for each phase
- Allows for incremental development and deployment

**Changes in v2.0**:
- Added wireframe-based layout with card design
- Enhanced statistics: Latest, Average, Lowest, Highest for each metric
- Added server filtering dropdown
- Added reset filters functionality
- Implemented filter state persistence using browser's localStorage
- Implemented test health monitoring with visualization
- Streamlined to focus on core metrics (Download, Upload, Ping)
- Removed less commonly used charts (Jitter, Download Latency, Upload Latency)
- Added 5 new API endpoints: Statistics, Health, Servers (in addition to existing Stats and Charts)
- Updated all tests, documentation, and implementation plans
- Enhanced styling with health bars and modern card-based UI
- Deferred quota tracking to future version (not ready for V2)
