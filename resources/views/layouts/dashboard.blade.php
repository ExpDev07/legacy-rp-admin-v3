@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <li>
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                    <div class="sidebar-brand-text mx-3">L-RP Admin<sup>v3</sup></div>
                </a>
            </li>

            <!-- Divider -->
            <li><hr class="sidebar-divider my-0"></li>

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <li><hr class="sidebar-divider"></li>

            <!-- Heading -->
            <li>
                <div class="sidebar-heading">
                    Server
                </div>
            </li>

            <!-- Nav Item - Players -->
            <li class="nav-item">
                <!-- Searching players -->
                <a class="nav-link" href="{{ route('players.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Players</span>
                </a>
            </li>

            <!-- Viewing logs -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logs.index') }}">
                    <i class="fas fa-toilet-paper"></i>
                    <span>Logs</span>
                </a>
            </li>

            <!-- Divider -->
            <li><hr class="sidebar-divider my-0"></li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block navbar-search" action="{{ route('players.index') }}">
                        <div class="input-group">
                            <!-- Query input -->
                            <input class="form-control bg-light border-0" name="q" placeholder="Search players..." aria-label="Search">

                            <!-- Search -->
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Divider -->
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        @if ($user = auth()->user())
                            <li class="nav-item dropdown no-arrow">
                                <!-- User part -->
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ $user->name }}</span>
                                    <img class="img-profile rounded-circle" alt="Avatar" src="{{ $user->avatar }}" />
                                </a>
                                <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                    <!-- Profile -->
                                    @if ($player = $user->player)
                                        <a class="dropdown-item" href="{{ route('players.show', compact('player')) }}">
                                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Profile
                                        </a>
                                    @endif

                                    <!-- Divider -->
                                    <div class="dropdown-divider"></div>

                                    <!-- Logging out -->
                                    <form class="dropdown-item" method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="btn btn-sm btn-danger w-100" type="submit">Logout</button>
                                    </form>
                                </div>
                            </li>
                        @endif
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Page main content -->
                <main class="container-fluid">
                    <!-- Page Heading -->
                    <div class="mb-4">
                        <!-- Title -->
                        <h3 class="text-dark">@yield('title')</h3>

                        <!-- Description -->
                        @yield('description')
                    </div>

                    <!-- Main content -->
                    @yield('main')
                </main>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-light">
                <div class="container my-auto text-center">
                    Made with <i class="fas fa-heart text-danger"></i> by ExpDev (Marius) - <a href="https://github.com/ExpDev07/legacy-rp-admin-v3">https://github.com/ExpDev07/legacy-rp-admin-v3</a>
                </div>
            </footer>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
@endsection
