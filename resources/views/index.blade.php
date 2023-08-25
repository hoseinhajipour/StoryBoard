@section('title', 'Index')

<div class="row">
    <div class="col-8">
        <canvas id="renderCanvas"></canvas>
    </div>
    <div class="col-4">
        <livewire:inc.asset-loader/>
    </div>
</div>

<script>
    const canvas = document.getElementById("renderCanvas"); // Get the canvas element
    const engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine
    var gizmo;
    var utilLayer;
    var gizmoManager;
    var shadowGenerator;

    const createScene = function () {
        // Creates a basic Babylon Scene object
        const scene = new BABYLON.Scene(engine);
        // Creates and positions a free camera
// Creates, angles, distances and targets the camera
        var camera = new BABYLON.ArcRotateCamera("Camera", 0, 0, 10, new BABYLON.Vector3(0, 0, 0), scene);

        // This positions the camera
        camera.setPosition(new BABYLON.Vector3(0, 0, -10));

        // This attaches the camera to the canvas
        camera.attachControl(canvas, true);


        const light_0 = new BABYLON.HemisphericLight("light",
            new BABYLON.Vector3(0, 1, 0), scene);
        // Dim the light a small amount - 0 to 1
        light_0.intensity = 0.7;

        // Creates a light, aiming 0,1,0 - to the sky
        const  light = new BABYLON.DirectionalLight("dir01", new BABYLON.Vector3(0, -1, 1), scene);
        light.position = new BABYLON.Vector3(0, 15, -30);

        gizmoManager = new BABYLON.GizmoManager(scene);

        // Shadow generator
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

