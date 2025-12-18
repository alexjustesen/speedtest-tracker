<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }} - {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            const theme = localStorage.getItem('theme') ?? 'system'

            if (
                theme === 'dark' ||
                (theme === 'system' &&
                    window.matchMedia('(prefers-color-scheme: dark)')
                        .matches)
            ) {
                document.documentElement.classList.add('dark')
            }
        </script>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
        <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:brand href="#" class="max-lg:hidden dark:hidden">
                <x-slot name="logo" class="size-8 rounded-full bg-zinc-900 text-white text-xs font-bold">
                    <flux:icon name="rabbit" variant="mini" />
                </x-slot>
            </flux:brand>
            <flux:brand href="#" class="max-lg:hidden! hidden dark:flex">
                <x-slot name="logo" class="size-8 rounded-full bg-white text-zinc-900 text-xs font-bold">
                    <flux:icon name="rabbit" variant="mini" />
                </x-slot>
            </flux:brand>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="chart-no-axes-combined" :href="route('dashboard')">Dashboard</flux:navbar.item>
                {{-- <flux:navbar.item icon="table" href="#">Results</flux:navbar.item> --}}
            </flux:navbar>

            <flux:spacer />

            <flux:navbar>
                <flux:dropdown x-data="{
                    theme: localStorage.getItem('theme') ?? 'system',
                    updateTheme(newTheme) {
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
                    }
                }" align="end">
                    <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
                        <flux:icon.sun x-show="theme === 'light'" variant="mini" class="text-zinc-500 dark:text-white" />
                        <flux:icon.moon x-show="theme === 'dark'" variant="mini" class="text-zinc-500 dark:text-white" />
                        <flux:icon.moon x-show="theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches" variant="mini" class="text-zinc-500 dark:text-white" />
                        <flux:icon.sun x-show="theme === 'system' && !window.matchMedia('(prefers-color-scheme: dark)').matches" variant="mini" class="text-zinc-500 dark:text-white" />
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item icon="sun" x-on:click="updateTheme('light')">Light</flux:menu.item>
                        <flux:menu.item icon="moon" x-on:click="updateTheme('dark')">Dark</flux:menu.item>
                        <flux:menu.item icon="monitor" x-on:click="updateTheme('system')">System</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                {{-- TODO: Add speedtest modal here --}}

                @auth
                    <flux:navbar.item class="max-lg:hidden" icon="settings" :href="route('filament.admin.pages.dashboard')">Admin Panel</flux:navbar.item>
                @else
                    <flux:navbar.item class="max-lg:hidden" icon="log-in" :href="route('login')">Login</flux:navbar.item>
                @endauth
            </flux:navbar>
        </flux:header>

        <flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
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

        <flux:main container>
            {{ $slot }}
        </flux:main>

        @fluxScripts
    </body>
</html>
