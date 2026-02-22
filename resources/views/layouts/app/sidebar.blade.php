<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="bg-white dark:bg-zinc-800 min-h-screen">
    <x-toaster-hub />

    <flux:sidebar sticky collapsible="mobile"
        class="bg-zinc-50 dark:bg-zinc-900 border-e border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                {{-- MENU CUSTOMER SERVICE (Petugas Loket) --}}
                @role('customer_service|admin')
                    <flux:sidebar.item icon="megaphone" :href="route('select-unit')" wire:navigate>
                        Konsol Panggilan
                    </flux:sidebar.item>
                @endrole

                @role('admin')
                    {{-- Grup 1: Pengaturan Infrastruktur (Umum & User) --}}
                    <flux:sidebar.group :heading="__('Sistem Master')" icon="cog-6-tooth" expandable="true">
                        <flux:sidebar.item icon="building-office" :href="route('general')"
                            :current="request()->routeIs('general')" wire:navigate>
                            {{ __('Umum') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="shield-check" :href="route('roles')" :current="request()->routeIs('roles')"
                            wire:navigate>
                            {{ __('Role & Hak Akses') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="users" :href="route('users')" :current="request()->routeIs('users')"
                            wire:navigate>
                            {{ __('Akun Pengguna') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>

                    {{-- Grup 2: Pengaturan Teknis Antrean --}}
                    <flux:sidebar.group :heading="__('Konfigurasi Antrean')" icon="ticket" expandable="true">
                        <flux:sidebar.item icon="building-office-2" :href="route('units')"
                            :current="request()->routeIs('units')" wire:navigate>
                            {{ __('Unit Layanan') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="tag" :href="route('categories')"
                            :current="request()->routeIs('categories')" wire:navigate>
                            {{ __('Kategori Antrean') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="arrows-right-left" :href="route('mapping')"
                            :current="request()->routeIs('mapping')" wire:navigate>
                            {{ __('Pemetaan Antrean') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="printer" :href="route('printers')"
                            :current="request()->routeIs('printers')" wire:navigate>
                            {{ __('Pengaturan Printer') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endrole
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        {{-- <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav> --}}

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 font-normal text-sm">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-sm text-start">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="flex-1 grid text-sm text-start leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
