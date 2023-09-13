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
                console.log(sourceBone.name);
                if (sourceBone.name != "RightEye" || sourceBone.name != "LeftEye") {


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

                } else {

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

    function getTotalKeyframesCount(animationGroup) {
        let totalKeyframesCount = 0;

        // Iterate through the animations in the AnimationGroup
        for (const animation of animationGroup.targetedAnimations) {
            if (animation.animation.targetProperty === 'position') {
                // Assuming 'position' is the property you want to count keyframes for
                totalKeyframesCount += animation.animation.getKeys().length;
            }
            // You can add more conditions for other target properties if needed
        }

        return totalKeyframesCount;
    }

    function loadAnimation(asset_url) {
        BABYLON.SceneLoader.ImportMesh(null, "", asset_url, scene, function (meshes, particleSystems, skeletons) {
            var lastGroup = scene.animationGroups[scene.animationGroups.length - 1];

            var animatables = lastGroup._animatables;
            console.log("time frame : " + timeline.getTime() / 60)
            animatables.forEach(anim => {
                var new_target = findNodeByName(selectedMesh, anim.target.name);

                if (new_target) {
                    if (new_target.name !== "RightEye" && new_target.name !== "LeftEye") {
                        var animations = anim.target.animations;

                        animations.forEach(_anim => {
                            // Create a copy of the _anim animation
                            var modifiedAnim = _anim.clone();


                            // Offset the keyframes in the modified animation
                            modifiedAnim.getKeys().forEach(keyframe => {
                                // Offset the keyframe as needed
                                keyframe.frame += timeline.getTime() / 60;
                            });

                            // Push the modified animation to new_target.animations
                            new_target.animations.push(modifiedAnim);

                            // Add the modified animation as a targeted animation
                            lastGroup.addTargetedAnimation(modifiedAnim, new_target);
                        });
                    }
                }
            });
            lastGroup.normalize(0, lastGroup.to);

            //    lastGroup.dispose();

            meshes.forEach(mesh => {
                mesh.dispose();
            });


            updateObjectNamesFromScene();

            // add to timeline

            if (timeline) {
                // Add keyframe
                let rows = [
                    {
                        title: selectedMesh.name,
                        style: {
                            height: 100,
                            keyframesStyle: {
                                shape: 'rect',
                                width: 4,
                                height: 70,
                            },
                        },
                        keyframes: [
                            {val: timeline.getTime()},
                            {val: timeline.getTime() + getTotalKeyframesCount(lastGroup)}
                        ],
                    },
                ];

                // Add keyframe
                const currentModel = timeline.getModel();
                currentModel.rows.push(rows[0]);
                timeline.setModel(currentModel);

                // Generate outline list menu
                generateHTMLOutlineListNodes(currentModel.rows);
            }

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



