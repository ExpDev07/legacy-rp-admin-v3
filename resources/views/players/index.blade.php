@extends('layouts.dashboard')

@section('title', 'Search players')

@section('description')
    Search players by their <span class="text-primary font-weight-bold">name or identifier</span>. Don't know somebody's
    identifier? Use <a href="http://www.vacbanned.com/">VAC Banned</a> to help you specify your search.
@endsection

@section('main')
    <!-- Searching row -->
    <div class="row">
        <div class="col">
            <form>
                <div class="input-group">
                    <!-- Query input -->
                    <input class="form-control" name="q" placeholder="Marius Truckster">

                    <!-- Search -->
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results row -->
    <div class="row mt-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-primary font-weight-bold">Players Found ({{ $players->total() }})</h5>
                </div>
                <div class="card-body">
                    <!-- The table row -->
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th>Identifier</th>
                                        <th>Name</th>
                                        <th>Staff</th>
                                        <th>Banned</th>
                                        <th>Administer User</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($players as $player)
                                        <tr>
                                            <!-- General information -->
                                            <td>{{ $player->name }}</td>
                                            <td>{{ $player->identifier }}</td>

                                            <!-- Boolean values -->
                                            <td>{{ $player->isStaff() ? 'yes' : 'no' }}</td>
                                            <td>{{ $player->isBanned() ? 'yes' : 'no' }}</td>

                                            <!-- Linking to visit their profile -->
                                            <td>
                                                <a class="font-weight-bold" href="{{ route('players.show', compact('player')) }}">
                                                    Visit Profile
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <p>
                                            We <span class="text-danger">couldn't find any players</span> matching your  search. Please
                                            try a different one. An example would be <span class="font-weight-bold">John Doe</span> or
                                            <span class="font-weight-bold">steam:1100001003d06ec</span>.
                                        </p>
                                    @endforelse
                                    </tbody>
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
                </div>
            </div>
        </div>
    </div>
@endsection

