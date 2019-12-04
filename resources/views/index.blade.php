@extends('layouts.dashboard')

@section('description')
    You are now at the dashboard. This is the best place to get a full overview over server activities.
@endsection

@section('header')
    <div class="header header-cover bg bg-city">
        <div class="container">

            <!-- Searching for players -->
            <form method="GET" action="{{ route('players.index') }}">

                <!-- Name or identifier -->
                <div class="input-group input-group-lg">
                    <input class="form-control" name="q" placeholder="Search for player..." value="{{ request('q') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary">
                            Search
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
@endsection

@section('main')
    <p class="text-warning font-weight-bold">
        Oops! This page is still in progress... You can either search for players by using the form above or navigate
        elsewhere.
    </p>
    <div class="text-center">
        <img class="img-fluid mt-5" style="width: 30rem" src="{{ asset('images/signal_searching.svg') }}" alt="">
    </div>
@endsection
