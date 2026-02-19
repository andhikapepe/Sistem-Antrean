<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

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
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
