@push('head')
    <script src="{{asset('js/timeline/timeline.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/timeline.css')}}">
@endpush

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
    <div id="mytimeline"></div>
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


    <script type="text/javascript">

        var timeline;
        var data;

        // Called when the Visualization API is loaded.
        function drawVisualization() {
            // Create a JSON data table
            data = [
                {
                    'start': 0,
                    'end': 10,
                    'content': 'Walk',
                    'editable': true
                },
                {
                    'start': 5,
                    'end': 15,
                    'content': 'run',
                    'editable': true
                }
            ];

            // specify options
            var options = {
                'width': '100%',
                'height': '30vh',
                'showCustomTime': true
            };

            // Instantiate our timeline object.
            timeline = new links.Timeline(document.getElementById('mytimeline'), options);

            // cancel any running animation as soon as the user changes the range
            links.events.addListener(timeline, 'rangechange', function (properties) {
                animateCancel();
            });

            // Draw our timeline with the created data and options
            timeline.draw(data);

            timeline.setVisibleChartRange(0, 2);
        }

        drawVisualization();
        // create a simple animation
        var animateTimeout = undefined;
        var animateFinal = undefined;

        function animateTo(date) {
            // get the new final date
            animateFinal = date.valueOf();
            timeline.setCustomTime(date);

            // cancel any running animation
            animateCancel();

            // animate towards the final date
            var animate = function () {
                var range = timeline.getVisibleChartRange();
                var current = (range.start.getTime() + range.end.getTime()) / 2;
                var width = (range.end.getTime() - range.start.getTime());
                var minDiff = Math.max(width / 1000, 1);
                var diff = (animateFinal - current);
                if (Math.abs(diff) > minDiff) {
                    // move towards the final date
                    var start = new Date(range.start.getTime() + diff / 4);
                    var end = new Date(range.end.getTime() + diff / 4);
                    timeline.setVisibleChartRange(start, end);

                    // start next timer
                    animateTimeout = setTimeout(animate, 50);
                }
            };
            animate();
        }

        function animateCancel() {
            if (animateTimeout) {
                clearTimeout(animateTimeout);
                animateTimeout = undefined;
            }
        }

        function go() {
            // interpret the value as a date formatted as "yyyy-MM-dd"
            var v = document.getElementById('animateDate').value.split('-');
            var date = new Date(v[0], v[1], v[2]);
            if (date.toString() == "Invalid Date") {
                alert("Invalid Date");
            } else {
                animateTo(date);
            }
        }


    </script>
@endpush
