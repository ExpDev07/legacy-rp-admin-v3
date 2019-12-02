@extends('layouts.dashboard')

@section('title', $character->name . ' (#' . $character->cid . ') (character)')

@section('description')
    <p>
        Viewing a character. <a href="{{ route('players.show', $character->player) }}">Return to player</a>.
    </p>
@endsection

@section('main')
    <!-- Basic stat cards -->
    <div class="row">

        <!-- Money in bank -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Bank</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($character->bank) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Money in cash -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cash</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($character->cash) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Job</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $character->job }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Date of Birth</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $character->dob }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Changing of stuff -->
    <form class="card" method="POST">
        @csrf @method('PUT')

        <div class="card-header">
            <h6 class="text-primary font-weight-bold">Edit Character</h6>
        </div>
        <div class="card-body">
            <!-- Updating the full name -->
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name</label>
                        <input class="form-control" name="firstname" value="{{ $character->firstname }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Surname</label>
                        <input class="form-control" name="lastname" value="{{ $character->lastname }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-block btn-primary">
                Save
            </button>
        </div>
    </form>
@endsection