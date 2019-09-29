@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="d-flex min-vh-100">
        <!-- The image half (hide on smaller devices) -->
        <div class="col d-none d-md-block bg" style="background-image: url('{{ asset('images/bg-city-dark.png') }}')"></div>

        <!-- The content half -->
        <div class="col d-flex align-items-center">
            <div class="p-3">
                <h1 class="text-dark">Login with steam.</h1>

                <p>
                    Please login using the steam account which you've got staff permissions on. The database is
                    hooked up with the game-server, so we'll seamlessly be able to grab your information there and
                    process the authentication.
                </p>

                <a href="{{ route('login.steam') }}" class="btn btn-primary">
                    Login with steam now
                </a>
            </div>
        </div>
    </div>
@endsection
