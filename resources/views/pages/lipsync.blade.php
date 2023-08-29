<div class="container">
    <div id="waveform">
        <!-- the waveform will be rendered here -->
    </div>
    <form wire:submit.prevent="AnalyzeAudio">
        <input type="file" wire:model="audio">
        <button type="submit">Analyze Audio</button>
    </form>

    <button id="exportButton" class="btn btn-primary">Export</button>


    <script type="module">
        var wavesurfer;
        var wsRegions;
        var topTimeline;
        var bottomTimline;
        var phonemes;
        var audio_url;

        import WaveSurfer from 'https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.esm.js'
        import RegionsPlugin from 'https://unpkg.com/wavesurfer.js@7/dist/plugins/regions.esm.js'

        wavesurfer = WaveSurfer.create({
            container: '#waveform',
            waveColor: 'violet',
        });
        wsRegions = wavesurfer.registerPlugin(RegionsPlugin.create())

        wsRegions.enableDragSelection({
            color: 'rgba(255, 0, 0, 0.1)',
        })

        wsRegions.on('region-clicked', (region, e) => {
            region.content.innerText = "test";
        })

        wsRegions.on('region-updated', (region) => {
            console.log('Updated region', region)
        })
        window.addEventListener('conversationHistoryLoaded', function (event) {
            phonemes = JSON.parse(event.detail.phonemes);
            audio_url = event.detail.audio_url;
            wavesurfer.load(audio_url);
            wavesurfer.on('ready', function () {
                console.log(wavesurfer);
                // Create regions for phonemes
                //     wavesurfer.clearRegions();
                phonemes.forEach(phoneme => {
                    console.log(phoneme);
                    wsRegions.addRegion({
                        start: phoneme.start,
                        end: phoneme.start + phoneme.duration,
                        content: phoneme.content,
                        drag: true,
                        resize: true,
                    })
                });
            });


        });

        // Get a reference to the button element
        var button = document.getElementById('exportButton');

        function mapPhonemeToWeight(phonemeContent) {
            // Define a mapping object that associates phonemes with weights
            var phonemeWeightMapping = {
                'a': 0.5,
                'b': 1.0,
                'c': 0.8,
                'n': 0.8,
                'w': 0.8,
                'f': 0.8,
                't': 0.8,
            };

            // Check if the phoneme is in the mapping, return default weight if not found
            return phonemeWeightMapping[phonemeContent] || 0.0;
        }

        // Add a click event listener
        button.addEventListener('click', function () {
            var url = 'https://raw.githubusercontent.com/chris45242/BabylonModel/main/';
            var fileName = "project.blend1.gltf";

            const Casi = BABYLON.SceneLoader.Append(url, fileName, scene, function (s) {
                // Create a default arc rotate camera and light.
                scene.createDefaultCameraOrLight(true, true, true);
                scene.activeCamera.alpha += Math.PI;

                scene.stopAllAnimations();

                var audioDuration = 120;
                wsRegions.regions.forEach(region => {
                    var phonemeContent = region.content.innerText;
                    var phonemeWeight = mapPhonemeToWeight(phonemeContent); // Implement your mapping function

                    var animation = new BABYLON.Animation(
                        "phonemeAnimation",
                        "morphTargetInfluences", // Property to animate
                        30, // Frame rate
                        BABYLON.Animation.ANIMATIONTYPE_FLOAT,
                        BABYLON.Animation.ANIMATIONLOOPMODE_CYCLE
                    );

                    var numFrames = Math.floor(audioDuration * 30); // Adjust frame count based on audio duration
                    var frameDuration = audioDuration / numFrames;

                    var keyFrames = [];
                    for (var i = 0; i <= numFrames; i++) {
                        keyFrames.push({
                            frame: i,
                            value: [phonemeWeight, 0, 0] // Adjust the index of the target and the weight
                        });
                    }

                    animation.setKeys(keyFrames);
                    avatar.animations.push(animation);
                });

                scene.beginAnimation(avatar, 0, audioDuration * 30, true); // Start animations


                // Change current vowel repeatedly
                var speech = new BABYLON.Sound("speech", audio_url, scene, function () {
                    setTimeout(speech.play(), 9000);
                });
            });
        });
    </script>

    <canvas id="renderCanvas"></canvas>
    <script>
        const canvas = document.getElementById("renderCanvas"); // Get the canvas element
        const engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine
        var gizmo;
        var utilLayer;
        var gizmoManager;
        var shadowGenerator;
        var selectedMesh = null;

        var createScene = function () {
            // This creates a basic Babylon Scene object (non-mesh)
            var scene = new BABYLON.Scene(engine);

            // This creates and positions a free camera (non-mesh)
            var camera = new BABYLON.FreeCamera("camera1", new BABYLON.Vector3(0, 2, 2), scene);

            // This targets the camera to scene origin
            camera.setTarget(new BABYLON.Vector3(0, 1.5, 0));

            // This attaches the camera to the canvas
            //camera.attachControl(canvas, true);

            // This creates a light, aiming 0,1,0 - to the sky (non-mesh)
            var light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(0, 1, 0), scene);

            // Default intensity is 1. Let's dim the light a small amount
            light.intensity = 0.7;


            BABYLON.SceneLoader.ImportMesh(null, "", "http://127.0.0.1:8000/storage/characters/August2023/naPwiKre5KXdrP73Yi7r.glb", scene, function (meshes, particleSystems, skeletons) {
                // Find the mesh with the name "Wolf3D_Head"
                const targetMeshName = "Wolf3D_Head";
                const targetMesh = meshes.find(mesh => mesh.name === targetMeshName);

                if (targetMesh) {
                    // Check if the mesh has morph targets
                    if (targetMesh.morphTargetManager) {
                        console.log(`Mesh ${targetMeshName} has ${targetMesh.morphTargetManager.numTargets} morph targets.`);

                        // Print information about each morph target
                        for (let i = 0; i < targetMesh.morphTargetManager.numTargets; i++) {
                            const morphTarget = targetMesh.morphTargetManager.getTarget(i);
                            if (morphTarget.name == "viseme_DD") {
                                morphTarget.influence = 1;
                            }

                            console.log(`Morph Target ${i}: Name - ${morphTarget.name}, Influence - ${morphTarget.influence}`);
                        }
                    } else {
                        console.log(`Mesh ${targetMeshName} does not have morph targets.`);
                    }
                } else {
                    console.log(`Mesh ${targetMeshName} not found in the loaded meshes.`);
                }
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
    </script>
</div>
