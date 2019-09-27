@extends('layouts.app')

@section('content')
    <header>
        <nav class="navbar navbar-dark bg-dark">
            <!-- Navigation brand -->
            <div class="navbar-brand">
                Legacy RP
            </div>

            <!-- Right section -->
            <div class="d-flex">
                <!-- Navigation links -->
                <ul class="navbar-nav mr-3">
                    <li class="nav-item">
                        <a href="#" class="nav-link">{{ auth()->user()->name }}</a>
                    </li>
                </ul>

                <!-- Logging out -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger" type="submit">Logout</button>
                </form>
            </div>
        </nav>
    </header>

    <div class="container-fluid">

        

        @yield('main')
    </div>
@endsection
