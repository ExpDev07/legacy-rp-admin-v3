<!DOCTYPE html>
<html lang="{{ str_replace('-', '_', app()->getLocale()) }}" prefix="og: http://ogp.me/ns#">
<head>
    <!-- Metadata -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="author" content="ExpDev07">
    <meta name="description" content="Legacy Roleplay - Admin Web Panel">

    <!-- Open Graph Protocol -->
    <meta property="og:title" content="Legacy RP - Web Panel">
    <meta property="og:type" content="admin.fivem">
    <meta property="og:image" content="{{ asset('favicon.jpg') }}">

    <!-- Page title -->
    <title>Legacy RP - Admin @yield('title')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.jpg') }}">

    <!-- Styling -->
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">

    <!-- Scripts -->
    <script defer type="application/javascript" src="{{ mix('js/app.js') }}"></script>
    <script defer type="application/javascript" src="https://kit.fontawesome.com/0074643143.js" crossorigin="anonymous"></script>
</head>

<body class="h-full font-sans bg-gray-200 text-gray-700 antialiased">
    @inertia
</body>


