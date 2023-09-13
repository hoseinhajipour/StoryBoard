<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name') }}</title>

    <livewire:styles/>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a href="{{ route('index') }}" class="navbar-brand">
                <i class="fa fa-code text-primary"></i> {{ config('app.name') }}
            </a>

            <button type="button" data-bs-toggle="collapse" data-bs-target="#nav" class="navbar-toggler">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="nav" class="collapse navbar-collapse">
                <div class="navbar-nav ms-auto">
                    @guest
                        @if(Route::has('login'))
                            <a href="{{ route('login') }}" class="nav-link">Login</a>
                            <a href="{{ route('register') }}" class="nav-link">Register</a>
                        @endif
                    @else
                        <div class="nav-item dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle">
                                <i class="fa fa-user"></i> {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('logout') }}" class="dropdown-item">Logout</a>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main class="my-4">
        {{ $slot }}
    </main>

    <livewire:scripts/>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
</body>
</html>
