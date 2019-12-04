@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <!-- Top navigation bar -->
    @include('navbar')

    <!-- A header maybe? -->
    <header>
        @yield('header')
    </header>

    <!-- Main content -->
    <main class="py-5">
        <div class="container">

            <!-- Title and description -->
            <div class="mt-2 mb-5">
                <h1 class="font-weight-bolder">
                    @yield('title')
                </h1>
                <p>
                    @yield('description')
                </p>
            </div>

            @yield('main')

        </div>
    </main>
@endsection
