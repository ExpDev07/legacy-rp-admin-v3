@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="d-flex min-vh-100">
        <!-- The image half (hide on smaller devices) -->
        <div class="col d-none d-md-block bg" style="background-image: url('{{ asset('images/bg-city-dark.png') }}')"></div>

        <!-- The content half -->
        <div class="col d-flex align-items-center">
            <div class="p-3">
                <!-- Render any errors -->
                @if ($error = session()->get('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                @endif

                <h1 class="text-dark">Login with steam.</h1>

                <p>
                    Please login using the steam account which you've got staff permissions on. The database is
                    hooked up with the game-server, so we'll seamlessly be able to grab your information there and
                    process the authentication.
                </p>

                <!-- Logging in with steam button -->
                <a class="btn btn-primary btn-icon-split" href="{{ route('login.steam') }}">
                    <!-- Icon -->
                    <span class="icon"><i class="fab fa-steam"></i></span>

                    <!-- Text -->
                    <span class="text">
                        Login with steam now
                    </span>
                </a>
            </div>
        </div>
    </div>
@endsection
