<div class="text-center">

    <button type="button" class="btn btn-primary" onclick="openDialog()">
        Open Lip sync Editor
    </button>


    <div class="modal fade" id="dialogLipsyncModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
         wire:ignore>
        <div class="modal-dialog">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label>Title</label>
                    <input id="lipsync_title" type="text" class="form-control my-3">
                    <label> Zoom: <input type="range" min="10" max="1000" value="100"> </label>
                    <div id="waveform">
                        <!-- the waveform will be rendered here -->
                    </div>
                    <form wire:submit.prevent="AnalyzeAudio">
                        <input type="file" wire:model="audio">
                        <button type="submit">Analyze Audio</button>
                    </form>

                    <input id="current_audio_url" type="hidden">
                    <button id="AppendToTimeLine" class="btn btn-primary">Append To TimeLine</button>
                </div>
            </div>
        </div>
    </div>


    <div class="d-none">
        <div id="dialog_lip_icons">
            <div data-role="body text-dark">
                <div class="row">
                    @foreach($lips_icons as $icon)
                        <div class="col-3 text-center">
                            <button onclick="SetLipKey('{{$icon->name}}')">
                                <img src="{{Voyager::image($icon->icon)}}" width="100%"/>
                            </button>
                            <label class="d-block">{{$icon->name}}</label>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>


    @push('script')
        <script type="text/javascript">
            var dialog;

            function openDialog() {
                document.dispatchEvent(new Event("initAudio"));

                $('#dialogLipsyncModal').modal('show');
                /*
                dialog = $("#dialog").dialog({
                    minWidth: 200,
                    maxWidth: innerWidth / 2,
                    minHeight: 300,
                    maxHeight: innerHeight / 2,
                    width: innerWidth / 2,
                    height: innerHeight / 2,
                    modal: false
                });

                 */
            }

            var dialog_lip_icons;
            var current_region;

            function openDialoglip_icons(region) {
                current_region = region;
                dialog_lip_icons = $("#dialog_lip_icons").dialog({
                    minWidth: 200,
                    maxWidth: innerWidth / 3,
                    minHeight: 300,
                    maxHeight: innerHeight / 2,
                    width: innerWidth / 3,
                    height: innerHeight / 2,
                    modal: false
                });
            }

            function SetLipKey(new_key) {
                current_region.content.innerText = new_key;
            }
        </script>
        <script>
            var excludeTargets = [];

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

            function combineKeyFrames(animationGroup, morphVisemeKeys, audio_duration, _HeadMesh) {
                for (var viseme_name in morphVisemeKeys) {
                    //  excludeTargets.push(viseme_name);
                    var VisemeKeys = morphVisemeKeys[viseme_name];
                    var viseme = findMorph(_HeadMesh.morphTargetManager, viseme_name);

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

            function findValueOldFrame(old_keys, frame) {
                console.log(old_keys);
                old_keys.forEach(old_frame => {

                    if (old_frame.frame == frame) {
                        console.log(frame, old_frame.value);
                        return old_frame.value;
                    }
                });
                return 0;
            }

            function resampleAnimation(animation, newFrameRate) {
                // Get the original animation keys
                var keys = animation.getKeys();

                // Calculate the new time interval between keyframes
                var newTimeInterval = 1 / newFrameRate;

                // Create an array to store the resampled keys
                var resampledKeys = [];

                // Iterate through the original keys and resample
                for (var i = 0; i < keys.length - 1; i++) {
                    var currentKey = keys[i];
                    var nextKey = keys[i + 1];
                    var currentTime = currentKey.frame;
                    var nextTime = nextKey.frame;

                    // Interpolate between the current and next keyframes
                    for (var t = currentTime; t < nextTime; t += newTimeInterval) {
                        var interpolationFactor = (t - currentTime) / (nextTime - currentTime);
                        var interpolatedValue = currentKey.value + (nextKey.value - currentKey.value) * interpolationFactor;

                        // Push the resampled key to the array
                        resampledKeys.push({
                            frame: t,
                            value: interpolatedValue,
                        });
                    }
                }

                // Make sure to add the last keyframe
                resampledKeys.push(keys[keys.length - 1]);

                // Update the animation with the resampled keys
                animation.setKeys(resampledKeys);

                // Return the resampled animation
                return animation;
            }

            function AllZeroKeyframes(animationGroup, audio_duration, _HeadMesh) {
                // Calculate total frames
                var totalFrames = secondsToFrames(audio_duration, frameRate);


                // Iterate through morph targets
                for (let i = 0; i < _HeadMesh.morphTargetManager.numTargets; i++) {
                    const morphTarget = _HeadMesh.morphTargetManager.getTarget(i);

                    // Check if the morph target is in the exclusion list
                    if (excludeTargets.includes(morphTarget.name)) {
                        console.log("exclude : " + morphTarget.name)
                        continue; // Skip this morph target
                    }

                    var morphTargetKeys = [];

                    if (morphTarget.animations[0]) {
                        var anim = morphTarget.animations[0];

                        resampleAnimation(anim, 1);
                    } else {
                        // Create keyframes for zero influence

                        for (let j = 0; j < totalFrames; j++) {
                            morphTargetKeys.push({frame: j, value: 0.0});
                        }

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
            }

            function AutoBlinkAnimate(animationGroup, audio_duration, _HeadMesh) {
                var eyeBlinkLeft = findMorph(_HeadMesh.morphTargetManager, "eyeBlinkLeft");
                var eyeBlinkRight = findMorph(_HeadMesh.morphTargetManager, "eyeBlinkRight");

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

            function lipSync_(phonemes, audio_duration, _HeadMesh, title, start_frame, audio_url) {
                if (_HeadMesh) {
                    // Ensure that the mesh has a morph target manager
                    if (_HeadMesh.morphTargetManager) {
                        var morphVisemeKeys = [];
                        phonemes.forEach(phoneme => {

                            var viseme = findMorph(_HeadMesh.morphTargetManager, mapPhoneme(phoneme.content));

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

                        var FaceAnimationGroup = new BABYLON.AnimationGroup(_HeadMesh.name + "_talk_" + title);

                        combineKeyFrames(FaceAnimationGroup, morphVisemeKeys, audio_duration, _HeadMesh);
                        AutoBlinkAnimate(FaceAnimationGroup, audio_duration, _HeadMesh);
                        AllZeroKeyframes(FaceAnimationGroup, audio_duration, _HeadMesh);

                        FaceAnimationGroup.normalize(0, FaceAnimationGroup.to);
                        FaceAnimationGroup.offset = start_frame;
                        FaceAnimationGroup.blendingSpeed = 0.1;
                        FaceAnimationGroup.enableBlending = true;
                        FaceAnimationGroup.weight = 1.0;

                        updateObjectNamesFromScene();

                        //apply offset frame
                        var animatables = FaceAnimationGroup._targetedAnimations;


                        var customEventData = {
                            name_: _HeadMesh.name + "_talk_" + title,
                            url_: audio_url,
                        };

                        var event1 = new BABYLON.AnimationEvent(
                            start_frame,
                            function (customEventData) {
                                if (playing === true) {
                                    // You can access custom values from the customEventData object
                                    var name_ = customEventData.name_;

                                    var soundByName = scene.getSoundByName(name_);

                                    if (soundByName) {
                                        soundByName.play();
                                    } else {
                                        var url_ = customEventData.url_;
                                        // Load the sound and play it automatically once ready
                                        new BABYLON.Sound(
                                            name_,
                                            url_,
                                            scene,
                                            function () {
                                                this.play();
                                            },
                                            {
                                                loop: false,
                                                autoplay: false, // You can set this to true if you want it to autoplay
                                            }
                                        );

                                    }

                                }
                            }.bind(null, customEventData), // Bind customEventData to the event handler
                            true
                        );
                        // Attach your event to your animation
                        animatables[0].animation.addEvent(event1);
                        animatables.forEach(anim => {
                            var animations = anim.animation._keys;
                            animations.forEach(keyframe => {
                                keyframe.frame += start_frame;

                            });
                        });

                        if (timeline) {
                            // Add keyframe
                            let rows = [
                                {
                                    title: _HeadMesh.name + "_talk_" + title,
                                    audio_url: $("#current_audio_url").val(),
                                    type: "audio",
                                    style: {
                                        height: 60,
                                        keyframesStyle: {
                                            shape: 'rect',
                                            width: 4,
                                            height: 60,
                                        },
                                    },
                                    offset: start_frame,
                                    keyframes: [
                                        {val: framesToMilliseconds(start_frame)},
                                        {val: framesToMilliseconds(start_frame + FaceAnimationGroup.to)}
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


                    } else {
                        console.error("Morph target manager not found on the mesh.");
                    }
                } else {
                    console.error("Mesh with name 'Wolf3D_Head' not found.");
                }
            }
        </script>
    @endpush
</div>
