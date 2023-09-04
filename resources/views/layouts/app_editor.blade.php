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
            height: 80vh;
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
    <script src="https://cdn.babylonjs.com/babylon.js"></script>
    <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.min.js"></script>
    <script src="https://cdn.babylonjs.com/inspector/babylon.inspector.bundle.js"></script>
    <script src="https://cdn.babylonjs.com/viewer/babylon.viewer.js"></script>
    <script src="https://cdn.babylonjs.com/gui/babylon.gui.min.js"></script>
    <!-- links to the latest version of the minified serializers -->
    <script src="https://preview.babylonjs.com/serializers/babylonjs.serializers.min.js"></script>
    <script src="https://cdn.babylonjs.com/postProcessesLibrary/babylonjs.postProcess.min.js"></script>

</head>
<body>


<main>
    {{ $slot }}
</main>

<livewire:scripts/>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script type="module">

    import {Timeliner} from './timeliner/src/timeliner.js'

    var target = {
        x: 0,
        y: 0,
        rotate: 0
    };

    // initialize timeliner
    var timeliner = new Timeliner(target);
    timeliner.addLayer('x');
    timeliner.addLayer('y');
    timeliner.addLayer('rotate');

    timeliner.load({
        "version": "animator",
        "modified": "Mon Dec 08 2014 10:41:11 GMT+0800 (SGT)",
        "title": "Untitled",
        "layers": []
    });


    function animate() {
        requestAnimationFrame(animate);
    }

    animate();


</script>

</body>
</html>
