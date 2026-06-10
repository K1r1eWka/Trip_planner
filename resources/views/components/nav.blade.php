<nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(6px);">
    <div class="container">
        <a class="navbar-brand text-white" href="{{ url('/') }}">
            ✈ Trip Planner
        </a>
        <div class="navbar-nav ms-auto">
            @auth
                <a class="nav-link text-white" href="{{ route('trips.index') }}">My Trips</a>
                <span class="nav-link text-white-50">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link text-white">Logout</button>
                </form>
            @endauth
            @guest
                <a class="nav-link text-white" href="{{ route('login') }}">Login</a>
                <a class="nav-link text-white" href="{{ route('register') }}">Register</a>
            @endguest
        </div>
    </div>
</nav>