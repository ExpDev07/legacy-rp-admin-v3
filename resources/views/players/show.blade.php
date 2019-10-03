@extends('layouts.dashboard')

@section('title', $player->name)

@section('description')
    <p>
        Viewing the profile of {{ $player->name }} ({{ $player->identifier }}). Here you can perform various administrative
        actions related to this player.
    </p>
@endsection

@section('main')

    @if ($ban = $player->bans()->first())
        <div class="row">
            <div class="col mb-4">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body">
                        <div class="d-md-flex align-items-center justify-content-between">
                            <!-- Ban information -->
                            <div>
                                This player is currently banned by

                                <!-- Determine whether we're going to use the issuer or banner-id -->
                                @if ($issuer = $ban->issuer)
                                    {{ $issuer->name }}
                                @else
                                    {{ $ban['banner-id'] }}
                                @endif
                                for: {{ $ban->reason }}
                            </div>
                            <!-- Unbanning -->
                            <form method="POST" action="{{ route('players.bans.destroy', compact('player', 'ban')) }}">
                                @csrf @method('DELETE')

                                <!-- Button for unbanning -->
                                <button class="btn btn-sm btn-dark btn-icon-split">
                                    <!-- Icon -->
                                    <span class="icon"><i class="fas fa-trash"></i></span>

                                    <!-- Text -->
                                    <span class="text">
                                        Unban
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Player information -->
    <div class="row">
        <div class="col mb-4">
            <div class="card border-left-primary">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <!-- Title -->
                        <h6 class="text-primary font-weight-bold">Player Information</h6>

                        <!-- Ban player button -->
                        @if (!$player->isBanned())
                            <a class="btn btn-danger btn-sm btn-icon-split" href="#" data-toggle="modal" data-target="#banModal">
                                <!-- Icon -->
                                <span class="icon"><i class="fas fa-gavel"></i></span>

                                <!-- Text -->
                                <span class="text">
                                    Ban Player
                                </span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        This player has a total playtime of <span class="font-weight-bold">{{ $player->getPlayTime() }}</span>.
                    </p>
                </div>
                <div class="card-footer">
                    <!-- Visiting steam profile -->
                    @if ($steam = $player->getSteamID())
                        <a class="btn btn-sm btn-dark btn-icon-split" href="http://s.team/p/{{ $steam->RenderSteamInvite() }}">
                            <!-- Icon -->
                            <span class="icon"><i class="fab fa-steam"></i></span>

                            <!-- Text -->
                            <span class="text">
                                Steam Profile
                            </span>
                        </a>
                    @endif

                    <!-- Viewing logs -->
                    <a class="btn btn-sm btn-primary btn-icon-split" href="{{ route('logs.index', [ 'player' => $player->identifier ]) }}">
                        <!-- Icon -->
                        <span class="icon"><i class="fas fa-toilet-paper"></i></span>

                        <!-- Text -->
                        <span class="text">
                            View Logs
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Warnings -->
    <div class="row">
        <div class="col mb-4">
            <div class="card border-left-danger">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <!-- Title -->
                        <h6 class="text-primary font-weight-bold">Warnings</h6>

                        <!-- Add warning button -->
                        <a class="btn btn-danger btn-sm btn-icon-split" href="#" data-toggle="modal" data-target="#warningModal">
                            <!-- Icon -->
                            <span class="icon"><i class="fas fa-exclamation-circle"></i></span>

                            <!-- Text -->
                            <span class="text">
                                New Warning
                            </span>
                        </a>
                    </div>
                </div>
                @if ($warnings = $player->warnings())
                    <!-- Displaying -->
                    <div class="card-body p-0">
                        @forelse ($warnings->latest()->get() as $warning)
                            <div class="card">
                                <div class="card-body">
                                    {{ $warning->message }}
                                </div>
                                <div class="card-footer">
                                    By <span class="font-weight-bold">{{ $warning->issuer->name }}</span> - {{ $warning->created_at }}
                                </div>
                            </div>
                        @empty
                            <div class="m-4">
                                <p class="text-success font-weight-bold">
                                    This player has no warnings on their record! Excellent. Let them know they're doing a great job.
                                </p>
                                <div class="text-center">
                                    <img class="img-fluid mt-5 mb-4" style="width: 20rem" src="{{ asset('images/empty.svg') }}" alt="">
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <!-- Counting -->
                    @if ($count = $warnings->count())
                        <div class="card-footer">
                            <p>
                                {{ $count }} / 3 warnings
                            </p>
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $count / 3 * 100 }}%"></div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Warning Modal -->
    <form class="was-validated" method="POST" action="{{ route('players.warnings.store', compact('player')) }}">
        @csrf

        @component('modal', [ 'target' => 'warningModal' ])
            @slot('title')
                Add a warning to {{ $player->name }}!
            @endslot

            @slot('actions')
                <button class="btn btn-primary" type="submit">Add warning</button>
            @endslot

            <p>
                Please enter the reason for this warning. Include any links/evidence as well. If you are unsure  about something, don't
                hesitate contacting another staff member.
            </p>

            <!-- Reason input -->
            <label class="font-weight-bold" for="message">Reason</label>
            <textarea class="form-control is-invalid" id="message" name="message" placeholder="InzidiuZ did an oopsie." required></textarea>
            <div class="invalid-feedback">
                Please enter a reason in the text area.
            </div>
        @endcomponent
    </form>

    <!-- Ban Modal -->
    <form class="was-validated" method="POST" action="{{ route('players.bans.store', compact('player')) }}">
        @csrf

        @component('modal', [ 'target' => 'banModal' ])
            @slot('title')
                Ban {{ $player->name }}!
            @endslot

            @slot('actions')
                <button class="btn btn-danger" type="submit">Ban player!</button>
            @endslot

            <p>
                You are now banning this player from our game-servers. Before proceeding any further, make sure you're well within reason to go
                through with this. Checking with a secondary staff member is always a good idea.
            </p>

            <!-- Reason input -->
            <label class="font-weight-bold" for="reason">Reason</label>
            <textarea class="form-control is-invalid" id="reason" name="reason" placeholder="InzidiuZ did a really big oopsie." required></textarea>
            <div class="invalid-feedback">
                Please enter a reason in the text area.
            </div>
        @endcomponent
    </form>

@endsection
