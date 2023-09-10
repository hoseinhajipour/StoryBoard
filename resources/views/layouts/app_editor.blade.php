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

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="{{asset('css/jq.Schedule_style.css')}}">
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="{{ asset('js/Init.js') }}"></script>
<script src="{{ asset('js/MainMenu.js') }}"></script>

<script type="text/javascript" src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>
<link href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />
<script src="{{ asset('js/timeline/timeline.js') }}"></script>
@yield('script')
@stack('script')
</body>
</html>
