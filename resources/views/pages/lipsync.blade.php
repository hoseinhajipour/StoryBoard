<div class="container">
    <script type="module" src="{{asset("js/LipSyncTimeline.js")}}"></script>

    <label> Zoom: <input type="range" min="10" max="1000" value="100"> </label>
    <div id="waveform">
        <!-- the waveform will be rendered here -->
    </div>
    <form wire:submit.prevent="AnalyzeAudio">
        <input type="file" wire:model="audio">
        <button type="submit">Analyze Audio</button>
    </form>

    <button id="exportButton" onclick="ExportScene()" class="btn btn-primary">Export</button>
    <button id="lipSync" class="btn btn-primary">lip Sync KeyFrame</button>


    <button id="play" class="btn btn-success">Play</button>
    <br/>
    <canvas id="renderCanvas"></canvas>
    <script>
        const canvas = document.getElementById("renderCanvas"); // Get the canvas element
        const engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine
        var gizmo;
        var utilLayer;
        var gizmoManager;
        var shadowGenerator;
        var HeadMesh = null;
        var CurrentanimationGroup = null;

        function findMorph(Manager, name) {
            for (let i = 0; i < Manager.numTargets; i++) {
                const morphTarget = Manager.getTarget(i);
                if (morphTarget.name == name) {
                    return morphTarget;
                }
            }
            return null;
        }

        function secondsToFrames(seconds, frameRate) {
            return Math.round(seconds * frameRate);
        }

        function trimAndRemoveUnicode(inputString) {
            // Remove Unicode characters (non-ASCII) using a regular expression
            const stringWithoutUnicode = inputString.replace(/[^\x00-\x7F]+/g, '');

            // Trim the resulting string (remove leading and trailing whitespace)
            const trimmedString = stringWithoutUnicode.trim();

            return trimmedString;
        }


        function mapPhoneme(phonemeContent) {
            // Define a mapping object that associates phonemes with weights
            var phonemeWeightMapping = {
                'a': 'viseme_aa',
                'A': 'viseme_aa',
                'o': 'viseme_O',
                'e': 'viseme_E',
                'w': 'viseme_U',
                'i': 'viseme_I',
                'k': 'viseme_kk',
                'f': 'viseme_FF',
                'n': 'viseme_nn',
                'm': 'viseme_nn',
                's': 'viseme_SS',
                'ch': 'viseme_CH',
                'c': 'viseme_CH',
                't': 'viseme_TH',
                'h': 'viseme_TH',
                'd': 'viseme_DD',
                'y': 'viseme_SS',
                'p': 'viseme_PP',
                'z': 'viseme_E',
            };

            // Check if the phoneme is in the mapping, return default weight if not found
            return phonemeWeightMapping[trimAndRemoveUnicode(phonemeContent)] || "viseme_sil";
        }

        var createScene = function () {
            // This creates a basic Babylon Scene object (non-mesh)
            var scene = new BABYLON.Scene(engine);

            // This creates and positions a free camera (non-mesh)
            var camera = new BABYLON.FreeCamera("camera1", new BABYLON.Vector3(0, 1.5, 1), scene);

            // This targets the camera to scene origin
            camera.setTarget(new BABYLON.Vector3(0, 1.5, 0));
            camera.minZ = 0.01;
            // This attaches the camera to the canvas
            //camera.attachControl(canvas, true);

            // This creates a light, aiming 0,1,0 - to the sky (non-mesh)
            var light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(0, 1, 0), scene);

            // Default intensity is 1. Let's dim the light a small amount
            light.intensity = 0.7;


            // Assuming you've already created the 'scene' object

            BABYLON.SceneLoader.ImportMesh(null, "", "http://127.0.0.1:8000/storage/characters/August2023/naPwiKre5KXdrP73Yi7r.glb", scene, function (meshes, particleSystems, skeletons) {
                // Find the mesh with the name "Wolf3D_Head"
                const targetMeshName = "Wolf3D_Head";
                HeadMesh = meshes.find(mesh => mesh.name === targetMeshName);


            });
            // Our built-in 'ground' shape.
            var ground = BABYLON.MeshBuilder.CreateGround("ground", {width: 6, height: 6}, scene);

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

        var frameRate = 30;
        var excludeTargets = [];


        function lipSync(phonemes, audio_duration) {
            if (HeadMesh) {
                // Ensure that the mesh has a morph target manager
                if (HeadMesh.morphTargetManager) {
                    var morphVisemeKeys = [];
                    phonemes.forEach(phoneme => {

                        var viseme = findMorph(HeadMesh.morphTargetManager, mapPhoneme(phoneme.content));

                        if (viseme) {
                            var start = secondsToFrames(phoneme.start, frameRate);
                            if (phoneme.duration < 0.2) {
                                phoneme.duration = 0.2;
                            }
                            var end = secondsToFrames(phoneme.start + phoneme.duration, frameRate);
                            var mid = start + Math.round((end - start) / 2);

                            if (mid === end) {
                                end++;
                            }
                            var morphTargetKeys = [];
                            morphTargetKeys.push({
                                frame: start,
                                value: 0.0
                            });

                            morphTargetKeys.push({
                                frame: mid,
                                value: 1.0
                            });

                            morphTargetKeys.push({
                                frame: end,
                                value: 0.0
                            });


                            if (!morphVisemeKeys[mapPhoneme(phoneme.content)]) {
                                morphVisemeKeys[mapPhoneme(phoneme.content)] = [];
                            }

                            morphVisemeKeys[mapPhoneme(phoneme.content)].push(morphTargetKeys);


                        }
                    });

                    var animationGroup = new BABYLON.AnimationGroup("talk");


                    excludeTargets = ["eyeBlinkLeft", "eyeBlinkRight"];


                    combineKeyFrames(animationGroup, morphVisemeKeys, audio_duration);


                    AutoBlinkAnimate(animationGroup, audio_duration);


                    AllZeroKeyframes(animationGroup, audio_duration);

                    document.addEventListener('playAnim', () => {
                        animationGroup.play();
                        console.log('start event triggered on platform');
                    });


                } else {
                    console.error("Morph target manager not found on the mesh.");
                }
            } else {
                console.error("Mesh with name 'Wolf3D_Head' not found.");
            }
        }


        function combineKeyFrames(animationGroup, morphVisemeKeys, audio_duration) {
            for (var viseme_name in morphVisemeKeys) {
                if (viseme_name !== "viseme_sil") {
                    excludeTargets.push(viseme_name);
                    var VisemeKeys = morphVisemeKeys[viseme_name];
                    var viseme = findMorph(HeadMesh.morphTargetManager, viseme_name);

                    var morphTargetAnimation = new BABYLON.Animation(
                        viseme_name,
                        "influence",
                        frameRate,
                        BABYLON.Animation.ANIMATIONTYPE_FLOAT,
                        BABYLON.Animation.ANIMATIONLOOPMODE_CYCLE
                    );

                    var morphTargetKeys = [];
                    morphTargetKeys.push({
                        frame: 0,
                        value: 0.0
                    });


                    VisemeKeys.forEach(subval => {
                        subval.forEach(val => {
                            morphTargetKeys.push({
                                frame: val.frame,
                                value: val.value
                            });
                        });

                    });

                    var endframe = secondsToFrames(audio_duration, frameRate);
                    morphTargetKeys.push({
                        frame: endframe,
                        value: 0.0
                    });

                    //  const normalizedKeyframes = normalizeKeyframes(morphTargetKeys);

                    morphTargetAnimation.setKeys(morphTargetKeys);
                    viseme.animations.push(morphTargetAnimation);
                    animationGroup.addTargetedAnimation(morphTargetAnimation, viseme);
                }
            }
        }

        function AllZeroKeyframes(animationGroup, audio_duration) {
            // Calculate total frames
            var totalFrames = secondsToFrames(audio_duration, frameRate);


            // Iterate through morph targets
            for (let i = 0; i < HeadMesh.morphTargetManager.numTargets; i++) {
                const morphTarget = HeadMesh.morphTargetManager.getTarget(i);

                // Check if the morph target is in the exclusion list
                if (excludeTargets.includes(morphTarget.name)) {
                    console.log("exclude : " + morphTarget.name)
                    continue; // Skip this morph target
                }


                // Create keyframes for zero influence
                var morphTargetKeys = [];
                morphTargetKeys.push({frame: 0, value: 0.0});
                morphTargetKeys.push({frame: totalFrames, value: 0.0});

                // Create an animation for the morph target
                var zeroAnimation = new BABYLON.Animation(
                    morphTarget.name,
                    "influence",
                    frameRate,
                    BABYLON.Animation.ANIMATIONTYPE_FLOAT,
                    BABYLON.Animation.ANIMATIONLOOPMODE_CYCLE
                );
                zeroAnimation.setKeys(morphTargetKeys);

                // Add the animation to the morph target
                morphTarget.animations.push(zeroAnimation);

                // Add the animation to the animation group
                animationGroup.addTargetedAnimation(zeroAnimation, morphTarget);
            }
        }

        function AutoBlinkAnimate(animationGroup, audio_duration) {
            var eyeBlinkLeft = findMorph(HeadMesh.morphTargetManager, "eyeBlinkLeft");
            var eyeBlinkRight = findMorph(HeadMesh.morphTargetManager, "eyeBlinkRight");

            var blinkDuration = 5; // Duration of the blink in frames
            var blinkWait = 50; // Duration of the blink in frames
            var totalFrames = secondsToFrames(audio_duration, frameRate);

            // Calculate the number of complete blink cycles
            var completeCycles = Math.floor(totalFrames / (blinkWait + blinkDuration)) - 1;
            if (completeCycles > 0) {
                // Create an array to store the keyframes for the blink animation
                var morphTargetKeys = [];

                morphTargetKeys.push({
                    frame: 0,
                    value: 0
                });

                for (var i = 0; i < completeCycles; i++) {

                    morphTargetKeys.push({
                        frame: blinkWait + (i * blinkWait),
                        value: 0
                    });
                    morphTargetKeys.push({
                        frame: blinkWait + (i * blinkWait) + (blinkDuration / 2),
                        value: 1.0
                    });
                    morphTargetKeys.push({
                        frame: blinkWait + (i * blinkWait) + blinkDuration,
                        value: 0
                    });
                }
                morphTargetKeys.push({
                    frame: totalFrames,
                    value: 0
                });
                // Create animations for both left and right eye blinks
                var eyeBlinkLeftAnimation = new BABYLON.Animation(
                    "eyeBlinkLeft",
                    "influence",
                    frameRate,
                    BABYLON.Animation.ANIMATIONTYPE_FLOAT,
                    BABYLON.Animation.ANIMATIONLOOPMODE_CYCLE
                );

                var eyeBlinkRightAnimation = new BABYLON.Animation(
                    "eyeBlinkRight",
                    "influence",
                    frameRate,
                    BABYLON.Animation.ANIMATIONTYPE_FLOAT,
                    BABYLON.Animation.ANIMATIONLOOPMODE_CYCLE
                );

                // Set the keyframes for both animations
                eyeBlinkLeftAnimation.setKeys(morphTargetKeys);
                eyeBlinkRightAnimation.setKeys(morphTargetKeys);


                eyeBlinkLeft.animations.push(eyeBlinkLeftAnimation);
                animationGroup.addTargetedAnimation(eyeBlinkLeftAnimation, eyeBlinkLeft);

                eyeBlinkRight.animations.push(eyeBlinkRightAnimation);
                animationGroup.addTargetedAnimation(eyeBlinkRightAnimation, eyeBlinkRight);
            }


        }

        function normalizeKeyframes(keyframes) {
            // Step 1: Sort the keyframes by the 'frame' property
            const sortedKeyframes = keyframes.slice().sort((a, b) => a.frame - b.frame);

            // Step 2: Initialize a result array for normalized keyframes
            const normalizedKeyframes = [];

            // Step 3: Iterate through the sorted keyframes and handle overlaps
            let previousFrame = -Infinity;
            for (const keyframe of sortedKeyframes) {
                // Ensure 'frame' is an integer greater than 0
                keyframe.frame = Math.max(1, Math.floor(keyframe.frame));

                // Ensure 'value' is between 0 and 1
                keyframe.value = Math.min(1, Math.max(0, keyframe.value));

                if (keyframe.frame >= previousFrame) {
                    // No overlap, add the normalized keyframe to the result array
                    normalizedKeyframes.push({frame: keyframe.frame, value: keyframe.value});
                    previousFrame = keyframe.frame;
                } else {
                    // Handle the overlap here (e.g., by averaging values)
                    const existingKeyframe = normalizedKeyframes[normalizedKeyframes.length - 1];
                    existingKeyframe.value = (existingKeyframe.value + keyframe.value) / 2;
                }
            }

            return normalizedKeyframes;
        }


        function ExportScene() {
            /*
            BABYLON.GLTF2Export.GLTFAsync(scene, "character_anim").then((gltf) => {
                gltf.downloadFiles();
            });

             */
            BABYLON.GLTF2Export.GLBAsync(scene, "character_anim").then((gltf) => {
                gltf.downloadFiles();
            });
        }
    </script>
</div>
