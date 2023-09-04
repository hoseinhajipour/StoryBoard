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


<script>
    const canvas = document.getElementById("renderCanvas"); // Get the canvas element
    const engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine
    var gizmo;
    var utilLayer;
    var gizmoManager;
    var shadowGenerator;
    var selectedMesh = null;

    const createScene = function () {
        const scene = new BABYLON.Scene(engine);
        var camera = new BABYLON.ArcRotateCamera("Camera", 0, 0, 10, new BABYLON.Vector3(0, 0, 0), scene);
        camera.setPosition(new BABYLON.Vector3(0, 0, -10));
        camera.attachControl(canvas, true);
        const light_0 = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(0, 1, 0), scene);
        light_0.intensity = 0.7;
        const light = new BABYLON.DirectionalLight("dir01", new BABYLON.Vector3(0, -1, 1), scene);
        light.position = new BABYLON.Vector3(0, 15, -30);
        gizmoManager = new BABYLON.GizmoManager(scene);
        shadowGenerator = new BABYLON.ShadowGenerator(2048, light);

        // Create SSAO and configure all properties (for the example)
        var ssaoRatio = {
            ssaoRatio: 0.5, // Ratio of the SSAO post-process, in a lower resolution
            combineRatio: 1.0 // Ratio of the combine post-process (combines the SSAO and the scene)
        };

        var ssao = new BABYLON.SSAORenderingPipeline("ssao", scene, ssaoRatio);
        ssao.fallOff = 0.000001;
        ssao.area = 1;
        ssao.radius = 0.0001;
        ssao.totalStrength = 1.0;
        ssao.base = 0.5;

        // Attach camera to the SSAO render pipeline
        scene.postProcessRenderPipelineManager.attachCamerasToRenderPipeline("ssao", camera);

        return scene;
    };


    const scene = createScene(); //Call the createScene function
    // Register a render loop to repeatedly render the scene
    engine.runRenderLoop(function () {
        scene.render();
    });
    // Watch for browser/canvas resize events
    window.addEventListener("resize", function () {
        engine.resize();
    });

    //  scene.debugLayer.show();
</script>

</body>
</html>
