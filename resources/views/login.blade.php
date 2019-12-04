@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="d-flex min-vh-100">
        <!-- The image half (hide on smaller devices) -->
        <div class="col d-none d-md-block bg bg-city"></div>

        <!-- The content half -->
        <div class="col d-flex align-items-center">
            <div class="p-3">
                <!-- Render any errors -->
                @if ($error = session()->get('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                @endif

                <h1 class="font-weight-bold">
                    Login with steam.
                </h1>

                <p class="mt-3 mb-4">
                    Please login using the steam account which you've got staff permissions on. The database is
                    hooked up with the game-server, so we'll seamlessly be able to grab your information there and
                    process the authentication.
                </p>

                <!-- Logging in with steam button -->
                <a class="btn btn-primary" href="{{ route('login.steam') }}">
                    <span class="icon"><i class="fab fa-steam"></i></span>
                    Login with steam now
                </a>
            </div>
        </div>
    </div>
@endsection
