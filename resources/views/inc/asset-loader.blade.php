<div>
    <livewire:modal.upload-asset-modal/>
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
            <div class="row">
                @foreach($Characters as $Character)
                    <div class="col-4">
                        <div class="card shadow text-center my-2"
                             onclick="loadModel('{{ url('storage/'.str_replace("\\", "/", json_decode($Character->url)[0]->download_link))  }}','{{$Character->title}}' )">
                            <div class="card-body">
                                <img src="{{Voyager::image($Character->icon)}}" width="100%">
                                <b>{{$Character->title}}</b>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <livewire:asset-loader.animation-assets/>
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


</div>
<script>
    function loadModel(asset_url, name) {

        BABYLON.SceneLoader.ImportMesh(null, "", asset_url, scene, function (meshes, particleSystems, skeletons) {
            selectedMesh = meshes[0];
            if (name) {
                selectedMesh.name = name;
            }
            gizmoManager.attachToMesh(meshes[0]);

            meshes.forEach(function (mesh) {
                mesh.receiveShadows = true;
                shadowGenerator.addShadowCaster(mesh, true);
            });


            gizmoManager.positionGizmoEnabled = true;
            gizmoManager.rotationGizmoEnabled = false;
            gizmoManager.scaleGizmoEnabled = false;

            updateObjectNamesFromScene();
        });
    }
</script>



