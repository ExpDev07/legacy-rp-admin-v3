@extends('layouts.dashboard')

@section('title', $player->name)

@section('description')
    Viewing the profile of {{ $player->name }} ({{ $player->identifier }}). Here you can perform various administrative
    actions related to this player.
@endsection

@section('main')

    <!-- Checking for ban -->
    @if ($ban = $player->bans()->first())
        <div class="row">
            <div class="col">
                <div class="card bg-danger">
                    <div class="card-body text-white">
                        <span class="font-weight-bold">
                            This player is currently banned by

                            <!-- Determine whether we're going to use the issuer or banner-id -->
                            @if ($issuer = $ban->issuer)
                                {{ $issuer->name }}
                            @else
                                {{ $ban['banner-id'] }}
                            @endif
                            for:
                        </span>
                        {{ $ban->reason }}
                    </div>
                    <div class="card-footer">
                        <span class="font-weight-bold">Ref ID #:</span> {{ $ban->ref_id }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Player information -->
    <div class="row">
        <div class="col">
            <div class="card border-left-primary mb-4">
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
                    <a class="btn btn-sm btn-dark btn-icon-split" href="#">
                        <!-- Icon -->
                        <span class="icon"><i class="fab fa-steam"></i></span>

                        <!-- Text -->
                        <span class="text">
                            Steam Profile
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Warnings -->
    <div class="row">
        <div class="col">
            <div class="card border-left-danger mb-4">
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
                <div class="card-body">
                    @forelse ($player->warnings()->latest()->get() as $warning)
                        <div class="card mb-4">
                            <div class="card-body">
                                {{ $warning->message }}
                            </div>
                            <div class="card-footer">
                                By <span class="font-weight-bold">{{ $warning->issuer->name }}</span> - {{ $warning->created_at }}
                            </div>
                        </div>
                    @empty
                        <p class="text-success font-weight-bold m-0">This player has no warnings on their record! Excellent.</p>
                    @endforelse
                </div>
                <div class="card-footer">
                    <p>
                        2 / 3 warnings
                    </p>
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 66%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Modal-->
    <div class="modal fade" id="warningModal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Add a warning to {{ $player->name }}</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <!-- Form for submitting a new warning -->
                <form class="was-validated" method="POST" action="{{ route('players.warnings.store', compact('player')) }}">
                    @csrf

                    <div class="modal-body">
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
                    </div>
                    <!-- Actions to perform -->
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Add warning</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
