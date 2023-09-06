<div class="w-100">
    <button class="btn btn-primary" id="play-button">Play</button>
    <div class="timeline-container">

        <input type="range" class="form-control" value="0"
               onchange="updateFrame()"
               id="timeline-slider" min="0" max="100" step="1">

    </div>

</div>

@push('script')

    <script>
        const slider = document.getElementById('timeline-slider');
        const playButton = document.getElementById('play-button');
        let playing = false;
        let playInterval;
        var MasteranimationGroup = new BABYLON.AnimationGroup("master");


        // Function to start or stop the timeline playback
        function togglePlay() {
            if (playing) {
                playButton.textContent = 'Play';
                playing = false;
                MasteranimationGroup.stop();
            } else {
                playButton.textContent = 'Pause';
                playing = true;

                MasteranimationGroup.play();

                slider.max = MasteranimationGroup.animatables[0].toFrame;

                // clear old interval in case playground is run more than once
                if (window.interval) {
                    clearInterval(window.interval);
                }

                // log frame every 500 ms
                window.interval = setInterval(() => {
                    if (MasteranimationGroup.animatables[0]) {
                        slider.value = MasteranimationGroup.animatables[0].masterFrame;
                    }

                }, 500);
            }
        }

        function updateFrame() {
          //  MasteranimationGroup.play();
       //     MasteranimationGroup.animatables[0].masterFrame = slider.value;

            MasteranimationGroup.goToFrame(slider.value);
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
@endpush
<!--
<script type="module">

    import {Timeliner} from './timeliner/src/timeliner.js'

    var target = {
        x: 0,
        y: 0,
        rotate: 0
    };

    // initialize timeliner
    var timeliner = new Timeliner(target);


    timeliner.load({
        "version": "animator",
        "modified": "Mon Dec 08 2014 10:41:11 GMT+0800 (SGT)",
        "title": "Untitled",
        "layers": []
    });
    function animate() {
        requestAnimationFrame(animate);
    }

    animate();


</script>
==>
