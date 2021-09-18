<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />
    <script defer src="{{ mix('js/app.js') }}"></script>

    <script>
        Window.App = {!! json_encode([
            'user' =>   Auth::user(),
            'csrfToken' => csrf_token(),
            'signedIn' => Auth::check()
        ]) !!};
    </script>

    <style>
        body { padding-bottom : 100px;}
        .level {display : flex; align-items: center;}
        .flex {flex: 1;}
        .mr-1 {margin-right: 1em;}
        .ml-a {margin-left: auto;}
        [v-cloak] {display:none;}
        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 0;
        }
        h6{
            margin-top: 0.4em;
        }
    </style>
    @yield('header')
</head>

<body>
    <div id="app">
        @include('layouts.nav')

        <main class="py-4">
            @yield('content')
        </main>

        <flash message="{{ session('flash') }}"></flash>
    </div>

</body>

</html>

