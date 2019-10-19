@extends('layouts.dashboard')

@section('title', 'Search players')

@section('description')
    <p>
        Search players by their <span class="text-primary font-weight-bold">name or identifier</span>. Don't know somebody's
        identifier? Use <a href="http://www.vacbanned.com/">VAC Banned</a> to help you specify your search.
    </p>
@endsection

@section('main')
    <!-- Searching row -->
    <div class="row">
        <div class="col mb-4">
            <form>
                <div class="input-group">
                    <!-- Query input -->
                    <input class="form-control" name="q" placeholder="Marius Truckster" value="{{ request('q') }}">

                    <!-- Search -->
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results row -->
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h6 class="text-primary font-weight-bold">Players found</h6>
                </div>
                <div class="card-body">
                    @if ($players->isEmpty())
                        <p>
                            We couldn't find any players matching your search. Please  try a different one. An example would
                            be <span class="font-weight-bold">John Doe</span> or <span class="font-weight-bold">steam:1100001003d06ec</span>.
                        </p>
                        <div class="text-center">
                            <img class="img-fluid mt-5" style="width: 30rem" src="{{ asset('images/signal_searching.svg') }}" alt="">
                        </div>
                    @else
                        <!-- The table row -->
                        <div class="row">
                            <div class="col">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable">
                                        <thead>
                                        <tr>
                                            <th>Identifier</th>
                                            <th>Name</th>
                                            <th>Warnings</th>
                                            <th>Banned</th>
                                            <th>Administer User</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($players as $player)
                                                <tr>
                                                    <!-- General information -->
                                                    <td>{{ $player->identifier }}</td>
                                                    <td>{{ $player->name }}</td>

                                                    <!-- Counting warnings -->
                                                    <td>{{ $player->warnings()->count() }}</td>

                                                    <!-- Boolean values -->
                                                    <td>{{ $player->isBanned() ? 'yes' : 'no' }}</td>

                                                    <!-- Linking to visit their profile -->
                                                    <td>
                                                        <a class="font-weight-bold" href="{{ route('players.show', compact('player')) }}">
                                                            Visit Profile
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Pagination row -->
                        <div class="row mt-2">
                            <div class="col">
                                {{ $players->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

