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
var HighlightLayer;
const createScene = function (laoadformurl = null) {
    const scene = new BABYLON.Scene(engine);
    Maincamera = new BABYLON.ArcRotateCamera("Camera", 0, 0, 1, new BABYLON.Vector3(0, 0, 0), scene);
    Maincamera.setPosition(new BABYLON.Vector3(0, 1.5, 2));
    Maincamera.minZ = 0.001;
    Maincamera.collisionMask = 1; //check to see if needed
    Maincamera.checkCollisions = true;
    //   Maincamera.wheelPrecision = 0.001;

    Maincamera.setTarget(new BABYLON.Vector3(0, 1.5, 0));
    Maincamera.attachControl(canvas, true);
    this.Maincamera.wheelPrecision = 100;
    this.Maincamera.zoomToMouseLocation = true;


    const light_0 = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(0, 1, 0), scene);
    light_0.intensity = 0.7;
    const light = new BABYLON.DirectionalLight("dir01", new BABYLON.Vector3(0, -1, 1), scene);
    light.position = new BABYLON.Vector3(0, 15, -30);
    gizmoManager = new BABYLON.GizmoManager(scene);
    shadowGenerator = new BABYLON.ShadowGenerator(2048, light);

    // Skybox

    var box = BABYLON.Mesh.CreateBox('SkyBox', 2048, scene, false, BABYLON.Mesh.BACKSIDE);
    box.material = new BABYLON.SkyMaterial('sky', scene);
    box.material.inclination = -0.35;
    // Reflection probe
    var rp = new BABYLON.ReflectionProbe('ref', 1024, scene);
    rp.renderList.push(box);


    // Create SSAO and configure all properties (for the example)
    var ssaoRatio = {
        ssaoRatio: 0.5, // Ratio of the SSAO post-process, in a lower resolution
        combineRatio: 2 // Ratio of the combine post-process (combines the SSAO and the scene)
    };

    var ssao = new BABYLON.SSAORenderingPipeline("ssao", scene, ssaoRatio);
    ssao.fallOff = 0.000001;
    ssao.area = 1;
    ssao.radius = 0.0001;
    ssao.totalStrength = 1.0;
    ssao.base = 0.5;

    // Attach camera to the SSAO render pipeline
    scene.postProcessRenderPipelineManager.attachCamerasToRenderPipeline("ssao", Maincamera);


    HighlightLayer = new BABYLON.HighlightLayer("hl1", scene, {
        mainTextureRatio: 1,
        mainTextureFixedSize: 2048,
        blurTextureSizeRatio: 1,
        blurVerticalSize: 2,
        blurHorizontalSize: 2,
        threshold: .025,
    });

    return scene;
};


var scene = createScene(); //Call the createScene function
// Register a render loop to repeatedly render the scene
engine.runRenderLoop(function () {
    if (scene) {
        scene.render();
    }

});
// Watch for browser/canvas resize events
window.addEventListener("resize", function () {
    engine.resize();
});

scene.debugLayer.show();
//scene.debugLayer.show({ embedMode: true });
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
    if (event.keyCode == 46) {
        selectedMesh.dispose();
        updateObjectNamesFromScene();
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
