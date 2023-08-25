<div>
    @foreach($Scenes as $Scene)
        <div class="card shadow"
             onclick="loadModel('{{ url('storage/'.str_replace("\\", "/", json_decode($Scene->url)[0]->download_link))  }}' )">
            <div class="card-body">
                <img src="{{Voyager::image($Scene->icon)}}" width="128">
                {{$Scene->title}}
            </div>
        </div>
    @endforeach
    <hr/>
    @foreach($Props as $Prop)
        <div class="card shadow"
             onclick="loadModel('{{ url('storage/'.str_replace("\\", "/", json_decode($Prop->url)[0]->download_link))  }}' )">
            <div class="card-body">
                <img src="{{Voyager::image($Prop->icon)}}" width="128">
                {{$Prop->title}}
            </div>
        </div>
    @endforeach

    <hr/>
    @foreach($Characters as $Character)
        <div class="card shadow"
             onclick="loadModel('{{ url('storage/'.str_replace("\\", "/", json_decode($Character->url)[0]->download_link))  }}' )">
            <div class="card-body">
                <img src="{{Voyager::image($Character->icon)}}" width="128">
                {{$Character->title}}
            </div>
        </div>
    @endforeach

    <button class="btn btn-primary" onclick="ExportScene()">Export Scene</button>
</div>
<script>
    function loadModel(asset_url) {

        BABYLON.SceneLoader.ImportMesh(null, "", asset_url, scene, function (meshes, particleSystems, skeletons) {


            gizmoManager.attachToMesh(meshes[0]);

            meshes.forEach(function (mesh) {
                mesh.receiveShadows = true;
                shadowGenerator.addShadowCaster(mesh, true);
            });


            gizmoManager.positionGizmoEnabled = true;
            gizmoManager.rotationGizmoEnabled = false;
            gizmoManager.scaleGizmoEnabled = false;
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

        });
    }

    function ExportScene() {
        BABYLON.GLTF2Export.GLBAsync(scene, "fileName").then((gltf) => {
            gltf.downloadFiles();
        });
    }
</script>