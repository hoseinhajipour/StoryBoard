const canvas = document.getElementById("renderCanvas"); // Get the canvas element
const engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine
var gizmo;
var utilLayer;
var gizmoManager;
var shadowGenerator;
var selectedMesh = null;
var objectNames = [];
var Maincamera;
var frameRate = 30;
const createScene = function () {
    const scene = new BABYLON.Scene(engine);
    Maincamera = new BABYLON.ArcRotateCamera("Camera", 0, 0, 5, new BABYLON.Vector3(0, 0, 0), scene);
    Maincamera.setPosition(new BABYLON.Vector3(0, 1.5, 2));
    Maincamera.minZ=0.001;
    Maincamera.setTarget(new BABYLON.Vector3(0, 1.5, 0));
    Maincamera.attachControl(canvas, true);
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
    scene.postProcessRenderPipelineManager.attachCamerasToRenderPipeline("ssao", Maincamera);

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

scene.debugLayer.show();
document.addEventListener("keydown", function (event) {
    // Check the key code and toggle gizmos accordingly

    if (event.key === "f" || event.key === "F") {
        // Get the position of the selected gizmo
        console.log(selectedMesh)
        if (selectedMesh) {
            var meshPosition = selectedMesh.position;
            Maincamera.target = meshPosition;

            // Move the camera closer to the object
            var distance = 5; // Adjust this value as needed
            var direction = Maincamera.position.subtract(meshPosition).normalize();
            Maincamera.setPosition(meshPosition.add(direction.scale(-distance)));
        }
    }

    switch (event.key) {
        case "e":
            gizmoManager.positionGizmoEnabled = false;
            gizmoManager.rotationGizmoEnabled = true;
            gizmoManager.scaleGizmoEnabled = false;
            break;
        case "w":
            gizmoManager.positionGizmoEnabled = true;
            gizmoManager.rotationGizmoEnabled = false;
            gizmoManager.scaleGizmoEnabled = false;
            break;
        case "s":
            gizmoManager.positionGizmoEnabled = false;
            gizmoManager.rotationGizmoEnabled = false;
            gizmoManager.scaleGizmoEnabled = true;
            break;
    }
});
