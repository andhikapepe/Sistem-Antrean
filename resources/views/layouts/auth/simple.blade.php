<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="bg-white dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 min-h-screen antialiased">
    <div class="flex flex-col justify-center items-center gap-6 bg-background p-6 md:p-10 min-h-svh">
        <div class="flex flex-col gap-2 w-full max-w-sm">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                <span class="flex justify-center items-center mb-1 rounded-md w-9 h-9">
                    @if (!empty($appSettings['app_logo']))
                        <img src="{{ asset('storage/' . $appSettings['app_logo']) }}" class="fill-current size-9 text-black dark:text-white">
                    @else
                        <x-app-logo-icon class="fill-current size-9 text-black dark:text-white" />
                    @endif
                </span>
                <span class="sr-only">{{ $appSettings['app_name'] ?? config('app.name', 'Laravel Starter Kit') }}</span>
            </a>
            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
        </div>
    </div>

    @fluxScripts
</body>

</html>
