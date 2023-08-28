<div class="container">
    <script src="https://unpkg.com/wavesurfer.js@4.0.0/dist/wavesurfer.min.js"></script>
    <script src="https://unpkg.com/wavesurfer.js@4.0.0/dist/plugin/wavesurfer.regions.min.js"></script>


    <div id="waveform">
        <!-- the waveform will be rendered here -->
    </div>

    <button class="btn btn-primary" onclick="loadAudio()">loadAudio</button>
    <button class="btn btn-primary" onclick="phonemesDetection()">phonemesDetection</button>

    <script>
        var wavesurfer;
        var wsRegions;
        var topTimeline;
        var bottomTimline;
        var phonemes;

        function loadAudio() {
            wavesurfer = WaveSurfer.create({
                container: '#waveform',
                waveColor: 'violet',
                maxCanvasWidth: 200,
                hideScrollbar: false,
                minPxPerSec: 1,
                maxPxPerSec: 50,
                plugins: [
                    WaveSurfer.regions.create(),

                ]
            });

            wavesurfer.load('audio/game.wav');

            wavesurfer.on('redraw', function () {
                if (phonemes) {
                    // Create regions for phonemes
                    phonemes.forEach(phoneme => {
                        wavesurfer.addRegion({
                            start: phoneme.start,
                            end: phoneme.start + phoneme.duration,
                            content: phoneme.content,
                            color: 'hsla(100, 100%, 30%, 0.1)'
                        });
                    });
                }
            });

        }

    </script>

    <script>
        function phonemesDetection() {
            $.ajax({
                url: 'audio/phonemes.txt',
                method: 'GET',
                dataType: 'text',
                success: function (data) {
                    const lines = data.trim().split('\n');
                    phonemes = lines.map(line => {
                        const [start, duration , content] = line.split(' ');
                        return {
                            start: parseFloat(start),
                            duration: parseFloat(duration ),
                            content: content,
                        };
                    });

                    wavesurfer.drawBuffer();
                    //  console.log('const phonemes =', JSON.stringify(phonemes, null, 4));
                },
                error: function (xhr, status, error) {
                    console.error('Error reading URL:', error);
                }
            });
        }
    </script>
</div>
