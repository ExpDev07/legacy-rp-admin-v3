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

    <div class="row">
        <div class="col">
            <!-- Player information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="text-primary">Player Information</h5>
                </div>
            </div>
        </div>
        <div class="col">
            <!-- Warnings -->
            <div class="card">
                <div class="card-header">
                    <h5 class="text-primary">Warnings</h5>
                </div>
            </div>
        </div>
    </div>
@endsection
