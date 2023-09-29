<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ setting('site.title') }}</title>

    <livewire:styles/>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        html, body {
            overflow: hidden;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #renderCanvas {
            width: 100%;
            height: 60vh;
            touch-action: none;
        }

        #ghostpane {
            top: unset !important;
            bottom: 0px;
        }

        body {
            background: #343434;
            color: white;
        }
    </style>
    <script src="{{asset("js/lib/babylon.js")}}"></script>
    <script src="{{asset("js/lib/babylonjs.loaders.min.js")}}"></script>
    <script src="{{asset("js/lib/babylon.inspector.bundle.js")}}"></script>
    <script src="{{asset("js/lib/babylon.viewer.js")}}"></script>
    <script src="{{asset("js/lib/babylonjs.materials.js")}}"></script>
    <script src="{{asset("js/lib/babylon.gui.min.js")}}"></script>
    <script src="{{asset("js/lib/babylonjs.serializers.min.js")}}"></script>
    <script src="{{asset("js/lib/babylonjs.postProcess.min.js")}}"></script>
    <script src="{{asset("js/lib/babylonjs.proceduralTextures.min.js")}}"></script>



    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&display=block" rel="stylesheet" />
    <script src="{{asset("animation-timeline-control/lib/animation-timeline.js")}}"></script>

    <!-- Alpine v3 -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Focus plugin -->
    <script defer src="https://unpkg.com/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />

    @yield('head')
    @stack('head')
</head>
<body>
<livewire:inc.menu/>
<main>
    {{ $slot }}
</main>

<livewire:scripts/>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

@stack('before-script')
<script src="{{ asset('js/Init.js') }}"></script>
<script src="{{ asset('js/MainMenu.js') }}"></script>
@livewire('livewire-ui-modal')


@yield('script')
@stack('script')


</body>
</html>
