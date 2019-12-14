<nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
    <div class="container">

        <!-- Branding -->
        <a class="navbar-brand" href="/">
            <img src="{{ asset('favicon.jpg') }}" alt="Logo" class="mr-2" width="30" height="30">
            LegacyRP
        </a>

        <!-- Navbar toggler -->
        <button class="navbar-toggler" data-toggle="collapse" data-target="#nav" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation -->
        <div id="nav" class="collapse navbar-collapse" role="navigation">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logs.index') }}">Server Logs</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('players.index') }}">Players</a>
                </li>

                @if ($user = auth()->user())
                    <li class="nav-item dropdown">
                        <!-- User part -->
                        <a class="nav-link" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <span class="mr-2">{{ $user->name }}</span>
                            <img class="rounded-circle" src="{{ $user->avatar }}" alt="Avatar" height="30" width="30">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu" aria-labelledby="userDropdown">
                            <!-- Profile -->
                            <a class="dropdown-item" href="{{ route('players.show', $user->player) }}">
                                <span class="fas fa-user"></span>
                                Profile
                            </a>

                            <!-- Divider -->
                            <div class="dropdown-divider"></div>

                            <!-- Logging out -->
                            <form class="dropdown-item" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-danger btn-sm w-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @endif
            </ul>
        </div>

    </div>
</nav>

<!-- Spacer to push fixed navbar up -->
<div class="py-4"></div>
