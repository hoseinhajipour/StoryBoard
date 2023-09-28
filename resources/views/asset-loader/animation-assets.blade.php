<div>
    <div class="row my-3">
        <div class="col-4 border border-white" wire:ignore>

            <ul id="myUL">
                @foreach($categories as $category)
                    <li>
                        <span data-id="{{ $category->id }}" class="caret folder">{{ $category->title }}</span>
                        @if(count($category->subcategories) > 0)
                            <ul class="nested">
                                @include('partials.subcategories', ['subcategories' => $category->subcategories])
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>

        </div>
        <div class="col-8 ">
            <div class="row">
                <div class="col-12 my-2">
                    <div class="input-group">
                        <input class="form-control border-end-0 border rounded-pill" type="text" value="search"
                               id="example-search-input">
                        <span class="input-group-append">
                <button class="btn btn-outline-secondary bg-white border-start-0 border rounded-pill ms-n3"
                        type="button">
                    <i class="fa fa-search"></i>
                </button>
            </span>
                    </div>

                </div>
                @foreach($Animations as $Animation)
                    <div class="col-6">
                        <div class="card shadow text-center"
                             @if(json_decode($Animation->url))
                                 onclick="loadAnimation('{{ url('storage/'.str_replace("\\", "/", json_decode($Animation->url)[0]->download_link))  }}','{{$Animation->title}}' )"
                            @endif
                        >
                            <div class="card-body">
                                <img src="{{Voyager::image($Animation->icon)}}" width="100%">
                                {{$Animation->title}}
                            </div>
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
            var milliseconds = (totalKeyframesCount / frameRate) * 1000;
            return milliseconds / 18;
            //  return totalKeyframesCount;
        }

        function applyAnimationToCharacter(asset_url, name, Character) {
            console.log(asset_url);
            console.log(name);
            console.log(Character.name);
            BABYLON.SceneLoader.ImportMesh(null, "", asset_url, scene, function (meshes, particleSystems, skeletons) {
                var lastGroup = scene.animationGroups[scene.animationGroups.length - 1];

                var endFrame_ = lastGroup.to;
                var animatables = lastGroup._animatables;
                animatables.forEach(anim => {
                    var new_target = findNodeByName(Character, anim.target.name);

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
                lastGroup.blendingSpeed = 0.1;
                lastGroup.enableBlending = true;
                lastGroup.weight = 1.0;
                lastGroup.name = Character.name + "_" + name;
                lastGroup.offset = timeline.getTime() / 60;

                meshes.forEach(mesh => {
                    mesh.dispose();
                });


                updateObjectNamesFromScene();

                // add to timeline

                if (timeline) {
                    // Add keyframe
                    let rows = [
                        {
                            title: Character.name + "_" + name,
                            style: {
                                height: 60,
                                keyframesStyle: {
                                    shape: 'rect',
                                    width: 4,
                                    height: 60,
                                },
                            },
                            offset: timeline.getTime() / 200,
                            keyframes: [
                                {val: timeline.getTime()},
                                //  {val: (timeline.getTime()) + getTotalKeyframesCount(lastGroup)}
                                {val: (timeline.getTime()) + endFrame_ * frameRate}
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

        function loadAnimation(asset_url, name) {
            var selectedCharcter = selectedMesh;
            applyAnimationToCharacter(asset_url, name, selectedCharcter)
        }
    </script>


    <script>
        var toggler = document.getElementsByClassName("caret");
        var i;

        for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function () {
                this.parentElement.querySelector(".nested").classList.toggle("sub_active");
                this.classList.toggle("caret-down");
            });
        }

        // Function to remove "folder_active" class from all folders
        function clearFolderActive() {
            for (var i = 0; i < folders.length; i++) {
                folders[i].classList.remove("folder_active");
            }
        }

        var folders = document.getElementsByClassName("folder");
        for (i = 0; i < folders.length; i++) {
            folders[i].addEventListener("click", function () {
                clearFolderActive();
                this.classList.toggle("folder_active");

                var dataId = this.getAttribute("data-id");
                Livewire.emit('loadCategory', dataId);
            });
        }
    </script>

</div>
