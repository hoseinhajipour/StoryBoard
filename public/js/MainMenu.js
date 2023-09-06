function ExportScene() {
    BABYLON.GLTF2Export.GLBAsync(scene, "fileName").then((gltf) => {
        gltf.downloadFiles();
    });
}

function AddCube() {
    var cube = BABYLON.MeshBuilder.CreateBox("Cube " + (scene.meshes.length + 1), { size: 1 }, scene);
    cube.position = new BABYLON.Vector3(0, 0, 0);

    gizmoManager.attachToMesh(cube);
    cube.receiveShadows = true;
    shadowGenerator.addShadowCaster(cube, true);
    gizmoManager.positionGizmoEnabled = true;
    gizmoManager.rotationGizmoEnabled = false;
    gizmoManager.scaleGizmoEnabled = false;

    updateObjectNamesFromScene();
}
