@extends('layouts.dashboard')

@section('description')
    You are now at the dashboard. This is the best place to get a full overview over server activities.
@endsection

@section('main')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h6 class="text-primary font-weight-bold">WHOOPS! We're still working here</h6>
                </div>
                <div class="card-body">
                    <p>
                        This page is still a work in progress. Navigate to other pages using the sidebar on the left.
                    </p>
                    <div class="text-center">
                        <img class="img-fluid mt-5 mb-4" style="width: 40rem" src="{{ asset('images/under_construction.svg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
