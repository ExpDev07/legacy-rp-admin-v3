@extends('layouts.dashboard')

@section('title', 'Server Logs')

@section('description')
    <div class="d-sm-flex align-items-center justify-content-between">
        <p class="m-0">
            This page shows all logged actions that occurred on the game-server.
        </p>

        <!-- Refresh button -->
        <div class="d-none d-lg-block">
            <a class="btn btn-sm btn-primary btn-icon-split" href="{{ route('logs.index', request()->query()) }}">
                <!-- Icon -->
                <span class="icon"><i class="fas fa-redo"></i></span>

                <!-- Text -->
                <span class="text">
                    Refresh
                </span>
            </a>
        </div>
    </div>
@endsection

@section('main')
    <!-- Filtering -->
    <div class="row">
        <div class="col mb-4">
            <form class="card">
                <div class="card-header">
                    <h6 class="text-primary font-weight-bold">Filter logs</h6>
                </div>
                <div class="card-body">
                    <!-- Server and action -->
                    <div class="form-row">
                        <!-- Selecting server -->
                        <div class="col">
                            <div class="form-group">
                                <label>Server</label>
                                <input class="form-control" name="serverId" placeholder="1" value="{{ request('serverId') }}">
                            </div>
                        </div>
                        <!-- Action -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Action</label>
                                <input class="form-control" name="action" placeholder="Player Dropped" value="{{ request('action') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Player -->
                    <div class="form-group">
                        <label>Player</label>
                        <input class="form-control" name="player" placeholder="steam:11000011299aec0" value="{{ request('player') }}">
                    </div>

                    <!-- Details -->
                    <div class="form-group">
                        <label>Details</label>
                        <input class="form-control" name="details" placeholder="Dropped out 11857" value="{{ request('details') }}">
                    </div>
                </div>
                <div class="card-footer">
                    <!-- Submitting -->
                    <button class="btn btn-block btn-primary">
                        Filter Results
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results row -->
    <div id="logs" class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h6 class="text-primary font-weight-bold">Logged actions found</h6>
                </div>
                <div class="card-body">
                    @if ($logs->isEmpty())
                        <p>
                            No logged actions were found yet. Someone's gotta do something first... Happy logging!
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
                                            <th>@</th>
                                            <th>Server</th>
                                            <th>Action</th>
                                            <th>Player</th>
                                            <th>Details</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($logs as $log)
                                            <tr>
                                                <!-- Timestamp -->
                                                <td>{{ $log->timestamp }}</td>

                                                <!-- Server ID -->
                                                <td>{{ $log->serverId ?? 0 }}</td>

                                                <!-- Action -->
                                                <td>{{ $log->action }}</td>

                                                <!-- Player that did the action -->
                                                <td>
                                                    @if ($player = $log->player)
                                                        <a href="{{ route('players.show', compact('player')) }}">
                                                            {{ $player->name }}
                                                        </a>
                                                    @else
                                                        No player.
                                                    @endif
                                                </td>

                                                <!-- Details -->
                                                <td>{{ $log->details }}</td>
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
                                {{ $logs->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection