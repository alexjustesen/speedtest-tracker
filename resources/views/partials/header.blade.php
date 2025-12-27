<flux:header container sticky class="bg-neutral-50 dark:bg-neutral-950 border-b border-neutral-200 dark:border-neutral-800">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:brand href="#" class="max-lg:hidden dark:hidden">
        <x-slot name="logo" class="size-8 rounded-full bg-neutral-900 text-white text-xs font-bold">
            <flux:icon name="rabbit" variant="mini" />
        </x-slot>
    </flux:brand>
    <flux:brand href="#" class="max-lg:hidden! hidden dark:flex">
        <x-slot name="logo" class="size-8 rounded-full bg-white text-neutral-900 text-xs font-bold">
            <flux:icon name="rabbit" variant="mini" />
        </x-slot>
    </flux:brand>

    <flux:navbar class="-mb-px max-lg:hidden">
        <flux:navbar.item icon="chart-no-axes-combined" :href="route('dashboard')">Dashboard</flux:navbar.item>
        {{-- <flux:navbar.item icon="table" href="#">Results</flux:navbar.item> --}}
    </flux:navbar>

    <flux:spacer />

    <flux:navbar>
        <div
            x-data="{
                theme: localStorage.getItem('theme') ?? 'system',
                setTheme(newTheme) {
                    this.theme = newTheme;
                    localStorage.setItem('theme', newTheme);
                    const effectiveTheme = newTheme === 'system'
                        ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                        : newTheme;
                    if (effectiveTheme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
                }
            }"
        >
            <flux:dropdown position="bottom" align="end">
                <flux:button variant="subtle" size="sm" square aria-label="Change theme">
                    <flux:icon.sun x-show="theme === 'light'" variant="mini" class="text-neutral-500 dark:text-white" />
                    <flux:icon.moon x-show="theme === 'dark'" variant="mini" class="text-neutral-500 dark:text-white" />
                    <flux:icon.computer-desktop x-show="theme === 'system'" variant="mini" class="text-neutral-500 dark:text-white" />
                </flux:button>

                <flux:menu>
                    <flux:menu.item x-on:click="setTheme('light')" icon="sun">
                        {{ __('general.light') }}
                    </flux:menu.item>

                    <flux:menu.item x-on:click="setTheme('dark')" icon="moon">
                        {{ __('general.dark') }}
                    </flux:menu.item>

                    <flux:menu.item x-on:click="setTheme('system')" icon="computer-desktop">
                        {{ __('general.system') }}
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>

        {{-- TODO: Add speedtest modal here --}}

        <flux:separator vertical variant="subtle" class="max-lg:hidden my-2"/>

        @auth
            <flux:navbar.item class="max-lg:hidden" icon="settings" :href="route('filament.admin.pages.dashboard')">Admin Panel</flux:navbar.item>
        @else
            <flux:navbar.item class="max-lg:hidden" icon="log-in" :href="route('login')">Login</flux:navbar.item>
        @endauth
    </flux:navbar>
</flux:header>

<flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-neutral-50 dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-700">
    <flux:sidebar.header>
        <flux:sidebar.brand
            href="#"
            logo="https://fluxui.dev/img/demo/logo.png"
            logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png"
            name="Acme Inc."
        />
        <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
    </flux:sidebar.header>
    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" href="#" current>Home</flux:sidebar.item>
        <flux:sidebar.item icon="inbox" badge="12" href="#">Inbox</flux:sidebar.item>
        <flux:sidebar.item icon="document-text" href="#">Documents</flux:sidebar.item>
        <flux:sidebar.item icon="calendar" href="#">Calendar</flux:sidebar.item>
        <flux:sidebar.group expandable heading="Favorites" class="grid">
            <flux:sidebar.item href="#">Marketing site</flux:sidebar.item>
            <flux:sidebar.item href="#">Android app</flux:sidebar.item>
            <flux:sidebar.item href="#">Brand guidelines</flux:sidebar.item>
        </flux:sidebar.group>
    </flux:sidebar.nav>
    <flux:sidebar.spacer />
    <flux:sidebar.nav>
        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
        <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
    </flux:sidebar.nav>
</flux:sidebar>
