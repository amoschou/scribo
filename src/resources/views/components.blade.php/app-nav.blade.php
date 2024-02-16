<nav class="navbar navbar-expand border-bottom">
    <div class="container-fluid">
        @if (request()->input('format') !== 'pdf')
            <button class="btn d-none d-md-block me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-collapsible" aria-expanded="false" aria-controls="sidebar-collapsible">
                <i class="bi bi-layout-sidebar"></i>
            </button>

            <button class="btn d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar-offcanvas" aria-controls="sidebar-offcanvas">
                <i class="bi bi-layout-sidebar"></i>
            </button>
        @endif

        <a class="navbar-brand" href="{{ $binder->getRootFolder()->href() }}">{{ $binder->getTitle() ?? '' }}</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                @foreach (config('scribo.menu') ?? [] as $label => $url)
                    <li class="nav-item"><a class="nav-link" href="{{ url($url) }}">{{ $label }}</a></li>
                @endforeach
                {{--
                    <li class="nav-item"><a class="nav-link" href="#">Link</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Link</a></li>
                --}}
            </ul>

            @if (request()->input('format') !== 'pdf')
                <!-- <button type="button" class="btn">
                    <i class="bi bi-person-fill"></i>
                </button> -->

                <ul class="navbar-nav me-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->profile['display_name'] ?? '%NOUSERNAME%' }}
                        </a>

                        <ul class="dropdown-menu">
                            {{--
                            <li><a class="dropdown-item" href="#">{{ Auth::user()->profile['display_name'] ?? '%NOUSERNAME%' }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            --}}
                            <li>
                                <form id="logoutform" method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a form="logoutform" class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log out') }}</a>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</nav>


