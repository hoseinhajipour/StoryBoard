<div class="w-100">
    <button class="btn btn-primary" id="play-button">Play</button>
    <label id="frameView">
        <span id="current_frame">0</span><span>/</span><span id="total_frames">100</span>
    </label>
    <div class="d-none">

        <div class="timeline-container">
            <input type="range" class="form-control" value="0"
                   onchange="updateFrame()"
                   id="timeline-slider" min="0" max="100" step="1">

        </div>
    </div>
    <div id="mytimeline">

    </div>
</div>

@push('script')

    <script>
        const slider = document.getElementById('timeline-slider');
        const playButton = document.getElementById('play-button');
        let playing = false;
        let playInterval;
        var MasteranimationGroup = new BABYLON.AnimationGroup("master");
        var frame = 0;
        var maxframe = 100;

        // Function to start or stop the timeline playback
        function togglePlay() {
            var animationGroups = scene.animationGroups;
            if (playing) {
                playButton.textContent = 'Play';
                playing = false;
                animationGroups.forEach(group => {
                    group.stop();
                });
                if (window.interval) {
                    clearInterval(window.interval);
                }
            } else {
                playButton.textContent = 'Pause';
                playing = true;


                animationGroups.forEach(group => {
                    group.play();
                    if (group.animatables[0] && group.animatables[0].toFrame > maxframe) {
                        maxframe = group.animatables[0].toFrame;
                        group.goToFrame(slider.value);
                    }
                    // animationGroups.goToFrame(slider.value);
                })
                //  MasteranimationGroup.play();
                //   MasteranimationGroup.goToFrame(slider.value);

                slider.max = maxframe / frameRate;

                $("#current_frame").html(slider.value);
                $("#total_frames").html(slider.max);
                // clear old interval in case playground is run more than once
                if (window.interval) {
                    clearInterval(window.interval);
                }

                // log frame every 500 ms
                window.interval = setInterval(() => {
                    frame++;
                    if (frame >= (maxframe / frameRate)) {
                        frame = 0;
                        animationGroups.forEach(group => {
                            group.stop();
                            group.play();
                        })
                        console.log("looping");
                    }
                    slider.value = frame;
                    $("#current_frame").html(frame);

                    /*
                    if (MasteranimationGroup.animatables[0]) {
                        slider.value = MasteranimationGroup.animatables[0].masterFrame;
                        $("#current_frame").html(slider.value / frameRate);
                    }*/
                }, 500);
            }
        }

        function updateFrame() {
            var animationGroups = scene.animationGroups;
            animationGroups.forEach(group => {
                if (group.animatables[0] && group.animatables[0].toFrame > maxframe) {
                    group.goToFrame(slider.value);
                }
            })
        }

        let startTime = null;

        function animate() {
            if (startTime === null) {
                startTime = performance.now();
            }

            const currentTime = performance.now();
            const elapsedTime = currentTime - startTime;

            // Calculate the current frame based on the elapsed time and animation duration
            const animationDuration = MasteranimationGroup._runtimeAnimations[0]._animation.totalFrame / MasteranimationGroup._runtimeAnimations[0]._animation.framePerSecond * 1000; // Duration in milliseconds
            const currentFrame = Math.floor((elapsedTime / animationDuration) * MasteranimationGroup._runtimeAnimations[0]._animation.totalFrame);

            console.log("Current Frame: " + currentFrame);

            if (currentFrame < animationGroup._runtimeAnimations[0]._animation.totalFrame) {
                // Continue animating
                requestAnimationFrame(animate);
            }
        }


        // Event listener for the play button
        playButton.addEventListener('click', togglePlay);
    </script>

    <script>

        var isPlaying = false;
        var intervalId = null;
        var millisecondsPerFrame = 1; // Adjust this as needed

        playButton.addEventListener('click', function () {
            if (isPlaying) {
                // Stop the animation
                clearInterval(intervalId);
                isPlaying = false;
            } else {
                // Start the animation
                intervalId = setInterval(function () {
                    // Get the current custom time marker position
                    var customTime = timeline.getCustomTime(id);

                    // Calculate the new time by adding a small time increment
                    var newTime = new Date(customTime.getTime() + 100);

                    // Update the custom time marker's position
                    timeline.setCustomTime(newTime, id);

                    // Check if we have reached the end time, and stop the animation if necessary
                    if (newTime >= options.end) {
                        clearInterval(intervalId);
                        isPlaying = false;
                    }
                }, millisecondsPerFrame);

                isPlaying = true;
            }
        });
    </script>

@endpush
