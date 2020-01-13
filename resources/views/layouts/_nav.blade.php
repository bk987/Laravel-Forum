<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a id="browseDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ __('Browse') }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu" aria-labelledby="browseDropdown">
                        <a class="dropdown-item" href="{{ route('threads.index') }}">
                            {{ __('All Threads') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('threads.index') }}?popular=1">
                            {{ __('Popular Threads') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('threads.index') }}?unanswered=1">
                            {{ __('Unanswered Threads') }}
                        </a>
                        @auth
                            <a class="dropdown-item" href="{{ route('threads.index') }}?subscribed=1">
                                {{ __('Subscribed Threads') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('threads.index') }}?author={{ auth()->user()->name }}">
                                {{ __('My Threads') }}
                            </a>
                        @endauth
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('threads.create') }}">
                        {{ __('New Thread') }}
                    </a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <user-notifications></user-notifications>
                    
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('profile', auth()->user()) }}">
                                {{ __('My Profile') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>