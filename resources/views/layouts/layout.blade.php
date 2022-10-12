<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="overflow-x: hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    {{-- <title>{{ Auth::user()->title }}</title> --}}

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    {{-- <script src="{{ asset('js/ConectorPlugin.js') }}"></script> --}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" defer rel="stylesheet">

    <link rel="icon" href="/images/icon.png">
</head>
<body id="body-pd" class="bg-white">
    <div id="app">
        @include('partials.dialog')
        @include('partials.dialog-2')
        @include('partials.loader')

        <main class="pt-4">
            @include('layouts.nav')
        </main>
    </div>
    <div id="sound"></div>
</body>
<script type="application/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
        let user_id = "<?php echo Auth::user()->id_usuario; ?>";
        listener(user_id);
    });
</script>
</html>