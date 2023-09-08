function ExportScene() {
    /*
    var combinedAnimationGroup = new BABYLON.AnimationGroup("CombinedAnimationGroup");

// Loop through your existing animationGroups
    scene.animationGroups.forEach(function (animationGroup) {
        animationGroup.targetedAnimations.forEach(function (targetedAnimation) {
            combinedAnimationGroup.addTargetedAnimation(targetedAnimation.animation);
        });
        // Dispose of the original animation group

    });
    scene.animationGroups.forEach(function (animationGroup) {
        if (animationGroup.name !== "CombinedAnimationGroup") {
            animationGroup.dispose();
        }

    });
*/
    BABYLON.GLTF2Export.GLBAsync(scene, "fileName").then((gltf) => {
        gltf.downloadFiles();
    });

}

function AddCube() {
    var cube = BABYLON.MeshBuilder.CreateBox("Cube " + (scene.meshes.length + 1), {size: 1}, scene);
    cube.position = new BABYLON.Vector3(0, 0, 0);

    gizmoManager.attachToMesh(cube);
    cube.receiveShadows = true;
    shadowGenerator.addShadowCaster(cube, true);
    gizmoManager.positionGizmoEnabled = true;
    gizmoManager.rotationGizmoEnabled = false;
    gizmoManager.scaleGizmoEnabled = false;

    updateObjectNamesFromScene();
}
