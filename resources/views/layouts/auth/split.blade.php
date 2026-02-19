<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-white dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 min-h-screen antialiased">
        <div class="relative flex-col justify-center items-center grid lg:grid-cols-2 px-8 sm:px-0 lg:px-0 lg:max-w-none h-dvh">
            <div class="hidden relative lg:flex flex-col bg-muted p-10 dark:border-e dark:border-neutral-800 h-full text-white">
                <div class="absolute inset-0 bg-neutral-900"></div>
                <a href="{{ route('home') }}" class="z-20 relative flex items-center font-medium text-lg" wire:navigate>
                    <span class="flex justify-center items-center rounded-md w-10 h-10">
                        <x-app-logo-icon class="fill-current me-2 h-7 text-white" />
                    </span>
                    {{ config('app.name', 'Laravel') }}
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="z-20 relative mt-auto">
                    <blockquote class="space-y-2">
                        <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                        <footer><flux:heading>{{ trim($author) }}</flux:heading></footer>
                    </blockquote>
                </div>
            </div>
            <div class="lg:p-8 w-full">
                <div class="flex flex-col justify-center space-y-6 mx-auto w-full sm:w-[350px]">
                    <a href="{{ route('home') }}" class="lg:hidden z-20 flex flex-col items-center gap-2 font-medium" wire:navigate>
                        <span class="flex justify-center items-center rounded-md w-9 h-9">
                            <x-app-logo-icon class="fill-current size-9 text-black dark:text-white" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
