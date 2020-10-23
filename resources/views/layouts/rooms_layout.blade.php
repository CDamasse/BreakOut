<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BreakOut') }}</title>

    <!-- Scripts -->
    @yield('scripts')

    <!-- Styles généraux à toutes les salles-->
    @yield('styles')
    <link rel="stylesheet" href="{{asset('css/all.css')}}" > <!-- FontAwesome -->

</head>
<body>
<div id="app" class="app">
    <div class="container">
        div.row
    </div>
    @yield('room')
</div>
</body>
</html>
