<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ (isset($title) ? $title . ' | ' : '') . ($appSettings['app_name'] ?? config('app.name')) }}
    </title>
    <meta name="description" content="{{ $appSettings['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $appSettings['meta_keywords'] ?? '' }}">
    <meta name="author" content="Andhika Putra Pratama, andhika6@gmail.com">

    @if (!empty($appSettings['app_logo']))
        <link rel="icon" href="{{ asset('storage/' . $appSettings['app_logo']) }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $appSettings['app_logo']) }}">
    @else
        <link rel="icon" href="/favicon.ico">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Menghilangkan scrollbar untuk tampilan TV/Kiosk */
        body {
            overflow: hidden;
            touch-action: manipulation;
            /* Mencegah zoom saat double tap di Kiosk */
        }

        /* Animasi Teks Berjalan (Marquee) */
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            display: inline-block;
            animation: marquee 30s linear infinite;
        }

        @media print {

            /* Sembunyikan semua elemen UI Kiosk */
            body * {
                visibility: hidden;
            }

            /* Hanya tampilkan area yang ditandai sebagai struk */
            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                color: black !important;
                background: white !important;
                padding: 0;
                margin: 0;
            }

            /* Hilangkan header/footer bawaan browser (tanggal, url, dll) */
            @page {
                margin: 0;
                size: auto;
            }
        }
    </style>
</head>

<body class="bg-zinc-950 h-full font-sans text-white antialiased">
    <x-toaster-hub />

    {{ $slot }}

    @livewireScripts
</body>

</html>
