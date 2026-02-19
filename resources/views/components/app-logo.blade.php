@props([
    'sidebar' => false,
])

@if ($sidebar)
    <flux:sidebar.brand name="{{ $appSettings['app_name'] ?? 'Laravel Starter Kit' }}" {{ $attributes }}>
        @if (!empty($appSettings['app_logo']))
            <x-slot name="logo"
                class="flex justify-center items-center rounded-md size-8 aspect-square bg-accent-content text-accent-foreground">
                <img src="{{ asset('storage/' . $appSettings['app_logo']) }}" class="size-full object-cover">
            </x-slot>
        @else
            <x-slot name="logo"
                class="flex justify-center items-center rounded-md size-8 aspect-square bg-accent-content text-accent-foreground">
                <x-app-logo-icon class="fill-current size-5 text-white dark:text-black" />
            </x-slot>
        @endif

    </flux:sidebar.brand>
@else
    <flux:brand name="{{ $appSettings['app_name'] ?? 'Laravel Starter Kit' }}" {{ $attributes }}>
        @if (!empty($appSettings['app_logo']))
            <x-slot name="logo"
                class="flex justify-center items-center rounded-md size-8 aspect-square bg-accent-content text-accent-foreground">
                <img src="{{ asset('storage/' . $appSettings['app_logo']) }}" class="size-full object-cover">
            </x-slot>
        @else
            <x-slot name="logo"
                class="flex justify-center items-center rounded-md size-8 aspect-square bg-accent-content text-accent-foreground">
                <x-app-logo-icon class="fill-current size-5 text-white dark:text-black" />
            </x-slot>
        @endif
    </flux:brand>
@endif
