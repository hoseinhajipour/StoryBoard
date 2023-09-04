<div>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                    type="button" role="tab" aria-controls="home" aria-selected="true">Scene
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                    type="button" role="tab" aria-controls="profile" aria-selected="false">Character
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                    type="button" role="tab" aria-controls="contact" aria-selected="false">Animation
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="Props-tab" data-bs-toggle="tab" data-bs-target="#Props"
                    type="button" role="tab" aria-controls="Props" aria-selected="false">Props
            </button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            @foreach($Scenes as $Scene)
                <div class="card shadow"
                     onclick="loadModel('{{ url('storage/'.str_replace("\\", "/", json_decode($Scene->url)[0]->download_link))  }}' )">
                    <div class="card-body">
                        <img src="{{Voyager::image($Scene->icon)}}" width="128">
                        {{$Scene->title}}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            @foreach($Characters as $Character)
                <div class="card shadow"
                     onclick="loadModel('{{ url('storage/'.str_replace("\\", "/", json_decode($Character->url)[0]->download_link))  }}' )">
                    <div class="card-body">
                        <img src="{{Voyager::image($Character->icon)}}" width="128">
                        {{$Character->title}}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            @foreach($Animations as $Animation)
                <div class="card shadow"
                     onclick="loadAnimation('{{ url('storage/'.str_replace("\\", "/", json_decode($Animation->url)[0]->download_link))  }}' )">
                    <div class="card-body">
                        <img src="{{Voyager::image($Animation->icon)}}" width="128">
                        {{$Animation->title}}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="tab-pane fade" id="Props" role="tabpanel" aria-labelledby="Props-tab">
            @foreach($Props as $Prop)
                <div class="card shadow"
                     onclick="loadModel('{{ url('storage/'.str_replace("\\", "/", json_decode($Prop->url)[0]->download_link))  }}' )">
                    <div class="card-body">
                        <img src="{{Voyager::image($Prop->icon)}}" width="128">
                        {{$Prop->title}}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button class="btn btn-primary" onclick="ExportScene()">Export Scene</button>
</div>
<script>

    function loadAnimation(asset_url) {
        /*

                const walkAnim = scene.getAnimationGroupByName("Walking");
                const walkBackAnim = scene.getAnimationGroupByName("WalkingBack");
                const idleAnim = scene.getAnimationGroupByName("Idle");
                const sambaAnim = scene.getAnimationGroupByName("Samba");
                sambaAnim.start();
        */
        // Get all animation groups in the scene
        var animationGroups = scene.animationGroups;

        // Loop through animation groups and log their names
        for (var i = 0; i < animationGroups.length; i++) {
            var animationGroupName = animationGroups[i].name;
            console.log("AnimationGroup name:", animationGroupName);
        }
        //  const Currentnim = scene.getAnimationGroupByName(animationGroups[i-1].name);
        //  Currentnim.start();

        BABYLON.SceneLoader.ImportAnimations("", asset_url, scene, false, BABYLON.SceneLoaderAnimationGroupLoadingMode.Clean, null, (scene) => {
        });


    }

    function loadModel(asset_url) {

        BABYLON.SceneLoader.ImportMesh(null, "", asset_url, scene, function (meshes, particleSystems, skeletons) {

            selectedMesh = meshes[0];
            gizmoManager.attachToMesh(meshes[0]);

            meshes.forEach(function (mesh) {
                mesh.receiveShadows = true;
                shadowGenerator.addShadowCaster(mesh, true);
            });


            gizmoManager.positionGizmoEnabled = true;
            gizmoManager.rotationGizmoEnabled = false;
            gizmoManager.scaleGizmoEnabled = false;
        });
    }

    document.addEventListener("keydown", function (event) {
        // Check the key code and toggle gizmos accordingly
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

    function ExportScene() {
        BABYLON.GLTF2Export.GLBAsync(scene, "fileName").then((gltf) => {
            gltf.downloadFiles();
        });
    }
</script>



