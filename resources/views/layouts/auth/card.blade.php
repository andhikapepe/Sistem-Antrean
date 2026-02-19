<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-neutral-100 dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 min-h-screen antialiased">
        <div class="flex flex-col justify-center items-center gap-6 bg-muted p-6 md:p-10 min-h-svh">
            <div class="flex flex-col gap-6 w-full max-w-md">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex justify-center items-center rounded-md w-9 h-9">
                        <x-app-logo-icon class="fill-current size-9 text-black dark:text-white" />
                    </span>

                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <div class="flex flex-col gap-6">
                    <div class="bg-white dark:bg-stone-950 shadow-xs border dark:border-stone-800 rounded-xl text-stone-800">
                        <div class="px-10 py-8">{{ $slot }}</div>
                    </div>
                </div>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
