function ExportScene() {
    var maxframe = 0;
    scene.animationGroups.forEach(function (animationGroup) {
        if (maxframe < animationGroup.to) {
            maxframe = animationGroup.to;
        }
    });
    scene.animationGroups.forEach(function (animationGroup) {
        animationGroup.normalize(0, maxframe);
    });

    const skybox = scene.getMeshByName("SkyBox"); // Replace 'yourSkyboxName' with the actual name of your skybox
    if (skybox) {
        skybox.setEnabled(false);
    }

    BABYLON.GLTF2Export.GLBAsync(scene, "fileName").then((gltf) => {
        // Restore the skybox after exporting
        if (skybox) {
            skybox.setEnabled(true);
        }

        gltf.downloadFiles();
    });
}

function RenderMovie() {
    var maxframe = 0;
    scene.animationGroups.forEach(function (animationGroup) {
        if (maxframe < animationGroup.to) {
            maxframe = animationGroup.to;
        }
    });

    if (BABYLON.VideoRecorder.IsSupported(engine)) {

        var recorderOptions = {
          //  mimeType: 'mp4',
            fps: 60,
            recordChunckSize: 2048,
        };


        var recorder = new BABYLON.VideoRecorder(engine, recorderOptions);
        canvas.requestFullscreen().catch(function (error) {
            console.error("Fullscreen error:", error);
        });

        recorder.startRecording();

        var animationGroups = scene.animationGroups;
        animationGroups.forEach(group => {
            group.stop();
            group.play();
        })

        setTimeout(() => {
            recorder.stopRecording()

            var animationGroups = scene.animationGroups;
            animationGroups.forEach(group => {
                group.stop();
            });

            document.exitFullscreen();
        }, maxframe * frameRate);
    }

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
