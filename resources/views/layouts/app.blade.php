<!DOCTYPE html>
<html lang="{{ str_replace('-', '_', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="author" content="ExpDev07">
    <meta name="description" content="Legacy Roleplay - Admin Web Panel">

    <!-- Page title -->
    <title>Legacy RP - Admin - @yield('title')</title>

    <!-- Favicon -->
    <link integrity="" rel="icon" type="image/png" href="{{ asset('favicon.jpg') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Styling -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
</head>

<body>
    <!-- Application wrapper -->
    <div id="app">
        <!-- Page content -->
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('/js/app.js') }}"></script>
</body>


