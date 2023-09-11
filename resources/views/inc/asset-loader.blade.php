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
                     onclick="loadModel('{{ url('storage/'.str_replace("\\", "/", json_decode($Character->url)[0]->download_link))  }}','{{$Character->title}}' )">
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
</div>
<script>

    function copyAnimations(sourceSkeleton, targetSkeleton) {
        // Iterate through the source skeleton's animations
        for (var i = 0; i < sourceSkeleton.animationRanges.length; i++) {
            var animationRange = sourceSkeleton.animationRanges[i];

            // Create a new animation range on the target skeleton with the same name
            var targetAnimationRange = targetSkeleton.createAnimationRange(animationRange.name, animationRange.from, animationRange.to);

            // Copy keyframes from the source animation to the target animation
            for (var j = 0; j < sourceSkeleton.bones.length; j++) {
                var sourceBone = sourceSkeleton.bones[j];
                var targetBone = targetSkeleton.getBoneByName(sourceBone.name);

                if (targetBone) {
                    var sourceAnimation = sourceBone.animations[i];

                    if (sourceAnimation) {
                        // Clone the animation from the source bone to the target bone
                        var targetAnimation = sourceAnimation.clone();

                        // Add the cloned animation to the target animation range
                        targetAnimationRange.addTargetedAnimation(targetAnimation, targetBone);
                    }
                }
            }
        }
    }

    function findNodeByName(node, name) {
        if (node.name === name) {
            return node; // Node with the specified name found
        }

        for (var i = 0; i < node.getChildren().length; i++) {
            var child = node.getChildren()[i];
            var foundNode = findNodeByName(child, name);
            if (foundNode) {
                return foundNode; // Node found within a child
            }
        }

        return null; // Node not found within selectedNode or its children
    }


    function loadAnimation(asset_url) {
        BABYLON.SceneLoader.ImportMesh(null, "", asset_url, scene, function (meshes, particleSystems, skeletons) {
            var lastGroup = scene.animationGroups[scene.animationGroups.length - 1];

            var animatables = lastGroup._animatables;
            animatables.forEach(anim => {
                var new_target = findNodeByName(selectedMesh, anim.target.name);
                if (new_target) {
                    var animations = anim.target.animations;
                    animations.forEach(_anim => {
                        new_target.animations.push(_anim);
                        lastGroup.addTargetedAnimation(_anim, new_target);
                    })
                }
            });
            //    lastGroup.dispose();

            meshes.forEach(mesh => {
                mesh.dispose();
            });


            updateObjectNamesFromScene();
        });

    }

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



