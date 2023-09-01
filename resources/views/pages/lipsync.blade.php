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
                'o': 'viseme_O',
                'e': 'viseme_E',
                'w': 'viseme_U',
                'i': 'viseme_I',
                'k': 'viseme_kk',
                'f': 'viseme_FF',
                'n': 'viseme_nn',
                's': 'viseme_SS',
                'ch': 'viseme_CH',
                'c': 'viseme_CH',
                't': 'viseme_TH',
                'h': 'viseme_TH',
                'd': 'viseme_DD',
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

        function lipSync(phonemes, audio_duration) {
            if (HeadMesh) {
                // Ensure that the mesh has a morph target manager
                if (HeadMesh.morphTargetManager) {
                    var morphVisemeKeys = [];
                    console.log(phonemes);
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

                            // Initialize morphVisemeKeys[viseme] as an array if it's undefined
                            if (!morphVisemeKeys[viseme.name]) {
                                morphVisemeKeys[viseme.name] = [];
                            }

                            morphVisemeKeys[viseme.name].push(morphTargetKeys);


                        }
                    });

                    var animationGroup = new BABYLON.AnimationGroup("talk");

                    for (var viseme_name in morphVisemeKeys) {

                        var VisemeKeys = morphVisemeKeys[viseme_name];
                        var viseme = findMorph(HeadMesh.morphTargetManager, viseme_name);

                        var morphTargetAnimation = new BABYLON.Animation(
                            viseme_name + "_anim",
                            "influence",
                            frameRate,
                            BABYLON.Animation.ANIMATIONTYPE_FLOAT,
                            BABYLON.Animation.ANIMATIONLOOPMODE_CYCLE
                        );
                        VisemeKeys = VisemeKeys[0];
                        var morphTargetKeys = [];
                        morphTargetKeys.push({
                            frame: 0,
                            value: 0.0
                        });
                        VisemeKeys.forEach(val => {
                            morphTargetKeys.push({
                                frame: val.frame,
                                value: val.value
                            });
                        });

                        var endframe = secondsToFrames(audio_duration, 30);
                        morphTargetKeys.push({
                            frame: endframe,
                            value: 0.0
                        });
                        console.log(viseme_name, morphTargetKeys);
                        morphTargetAnimation.setKeys(morphTargetKeys);
                        viseme.animations.push(morphTargetAnimation);
                        console.log(viseme);
                        animationGroup.addTargetedAnimation(morphTargetAnimation, viseme);
                    }


                    AutoBlinkAnimate(animationGroup, audio_duration);
                    //  animationGroup.play();

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

        function AutoBlinkAnimate(animationGroup, audio_duration) {
            var eyeBlinkLeft = findMorph(HeadMesh.morphTargetManager, "eyeBlinkLeft");
            var eyeBlinkRight = findMorph(HeadMesh.morphTargetManager, "eyeBlinkRight");

            var blinkDuration = 5; // Duration of the blink in frames
            var blinkWait = 50; // Duration of the blink in frames
            var totalFrames = secondsToFrames(audio_duration, frameRate);

            // Calculate the number of complete blink cycles
            console.log(totalFrames);
            var completeCycles = Math.floor(totalFrames / (blinkWait + blinkDuration)) - 1;
            console.log(completeCycles);
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


        function ExportScene() {
            BABYLON.GLTF2Export.GLBAsync(scene, "fileName").then((gltf) => {
                gltf.downloadFiles();
            });
        }
    </script>
</div>
