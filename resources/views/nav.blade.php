<nav id="main-nav" class="navbar navbar-expand-md navbar-dark">
    <a class="navbar-brand" href="{{ url('/') }}">LorinCup</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        @auth
            <ul class="navbar-nav ml-auto">
                <li class="nav-item @if($active_menu == 'tournament') active @endif">
                    <a class="nav-link" href="{{ route('tournaments.index') }}">مسابقات@if($active_menu == 'tournament')
                            <span class="sr-only">(current)</span>@endif</a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link" href="#">تنظیمات</a>--}}
{{--                </li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link" href="#">پشتیبانی</a>--}}
{{--                </li>--}}
            </ul>
    @endauth
    <!-- Left Side Of Navbar -->
        <ul class="navbar-nav mr-auto">
            <!-- Authentication Links -->
            @guest
                <li class="nav-item">
                    <a class="nav-link ml-0 pl-0" href="{{ route('login') }}">ورود /</a>
                </li>
                <li class="nav-item mr-0 pr-0">
                    <a class="nav-link" href="{{ route('register') }}">ثبت نام</a>
                </li>
            @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-left text-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            خروج
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>
