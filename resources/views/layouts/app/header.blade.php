<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Overview') }}
                </flux:navbar.item>
                @if (auth()->user()->isAdmin())
                <flux:navbar.item icon="users" :href="route('admin.employees')" :current="request()->routeIs('admin.employees')" wire:navigate>
                    {{ __('Active employees') }}
                </flux:navbar.item>
                <flux:navbar.item icon="check-circle" :href="route('admin.approvals')" :current="request()->routeIs('admin.approvals')" wire:navigate>
                    {{ __('Approve requests') }}
                </flux:navbar.item>
            @endif
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Request leave')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="plus" :href="route('leave-requests.create')" :label="__('Request leave')" wire:navigate />
                </flux:tooltip>
                <flux:tooltip :content="__('My requests')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        :href="route('leave-requests.index')"
                        :label="__('My requests')"
                        wire:navigate
                    />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('PTO')">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Overview')  }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                    @if (auth()->user()->isAdmin())
                        <flux:sidebar.item icon="users" :href="route('admin.employees')" :current="request()->routeIs('admin.employees')" wire:navigate>
                            {{ __('Active employees') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="check-circle" :href="route('admin.approvals')" :current="request()->routeIs('admin.approvals')" wire:navigate>
                            {{ __('Approve requests') }}
                        </flux:sidebar.item>
                    @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="{{ route('leave-requests.create') }}" wire:navigate>
                    {{ __('Request leave') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="book-open-text" href="{{ route('leave-requests.index') }}" wire:navigate>
                    {{ __('My requests') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
