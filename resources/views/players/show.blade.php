@extends('layouts.dashboard')

@section('title', $player->name)

@section('description')
    Viewing the profile of {{ $player->name }} ({{ $player->identifier }}). Here you can perform various administrative
    actions related to this player.
@endsection

@section('main')

    @if ($ban = $player->bans()->first())
        <div class="row">
            <div class="col mb-4">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
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
                            <form>
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
                    <h5 class="text-primary">Player Information</h5>
                </div>
                <div class="card-body">
                    <p>
                        This player has a total playtime of <span class="font-weight-bold">{{ $player->getPlayTime() }}</span>.
                    </p>
                </div>
                <div class="card-footer">
                    <!-- Visiting steam profile -->
                    @if ($steam = $player->getSteamProfile())
                        <a class="btn btn-sm btn-dark btn-icon-split" href="{{ $steam }}">
                            <!-- Icon -->
                            <span class="icon"><i class="fab fa-steam"></i></span>

                            <!-- Text -->
                            <span class="text">
                                Steam Profile
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Warnings -->
    <div class="row">
        <div class="col mb-4">
            <div class="card border-left-danger">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <!-- Title -->
                        <h5 class="text-primary">Warnings</h5>

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
                            <p class="text-success font-weight-bold m-4">
                                This player has no warnings on their record! Excellent.
                            </p>
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
                Please enter the reason for this warning. Include any links/evidence as well. If you are unsure
                about something, don't hesitate contacting another staff member.
            </p>

            <!-- Reason input -->
            <label class="font-weight-bold" for="message">Reason</label>
            <textarea class="form-control is-invalid" id="message" name="message" placeholder="InzidiuZ did an oopsie." required></textarea>
            <div class="invalid-feedback">
                Please enter a reason in the text area.
            </div>
        @endcomponent
    </form>

@endsection