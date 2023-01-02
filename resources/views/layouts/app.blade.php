<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="icon" href="/images/icon.png">
    
    {{-- <script type="text/javascript">

        function callbackThen(response){
            // read HTTP status
            // console.log(response.status);
            // read Promise object
            response.json().then(function(data){
                // console.log(data);
            });
        }

        function callbackCatch(error){
            console.error('Error:', error)
        }
    </script>

    {!! htmlScriptTagJsApi([
        'callback_then' => 'callbackThen',
        'callback_catch' => 'callbackCatch'
    ]) !!} --}}
</head>
<body>
    <div id="app">
        @include('partials.loader')

        <main id="main">
            @yield('content')
        </main>
    </div>
</body>
</html>
