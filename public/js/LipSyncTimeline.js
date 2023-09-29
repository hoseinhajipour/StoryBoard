var wavesurfer;
var wsRegions;
var topTimeline;
var bottomTimline;
var phonemes;
var audio_url;

import WaveSurfer from 'https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.esm.js'
import RegionsPlugin from 'https://unpkg.com/wavesurfer.js@7/dist/plugins/regions.esm.js'
/*
wavesurfer = WaveSurfer.create({
    container: '#waveform',
    waveColor: 'violet',
});

 */

/*
wsRegions.enableDragSelection({
    color: 'rgba(255, 0, 0, 0.1)',
})
*/

document.addEventListener('initAudio', () => {
    console.log("initAudio");

    $("#waveform").html("");
    wavesurfer = WaveSurfer.create({
        container: '#waveform',
        waveColor: 'violet',
    });
    wsRegions = wavesurfer.registerPlugin(RegionsPlugin.create());
    wsRegions.on('region-clicked', (region, e) => {
        //  region.content.innerText = "a";
        openDialoglip_icons(region);
    })

    wsRegions.on('region-updated', (region) => {
        console.log('Updated region', region)
    });

    // Get a reference to the button element
    var button = document.getElementById('AppendToTimeLine');

    // Add a click event listener
    button.addEventListener('click', function () {
        var new_phonemes = [];
        wsRegions.regions.forEach(region => {
            new_phonemes.push({
                start: region.start,
                duration: region.end - region.start,
                content: region.content.innerText,
            })
        });
        var audioDuration = wavesurfer.getDuration();
        //  lipSync(new_phonemes, audioDuration);

        var start_frame = millisecondsToFrames(timeline.getTime());
        var audio_url = $("#current_audio_url").val();
        var title = $("#lipsync_title").val();
        var _HeadMesh = searchForBlendShape(selectedMesh);


        lipSync_(new_phonemes, audioDuration, _HeadMesh, title, start_frame, audio_url)
    });


    var play = document.getElementById('playLipSyncNow');

    play.addEventListener('click', function () {
        wavesurfer.playPause();
        document.dispatchEvent(new Event("playAnim"));
    });

});


window.addEventListener('conversationHistoryLoaded', function (event) {
    phonemes = JSON.parse(event.detail.phonemes);
    audio_url = event.detail.audio_url;
    $("#current_audio_url").val(audio_url);
    wavesurfer.load(audio_url);
    wavesurfer.on('ready', function () {

        const slider = document.querySelector('input[type="range"]')

        slider.addEventListener('input', (e) => {
            const minPxPerSec = e.target.valueAsNumber
            wavesurfer.zoom(minPxPerSec)
        })

        phonemes.forEach(phoneme => {
            wsRegions.addRegion({
                start: phoneme.start,
                end: phoneme.start + phoneme.duration,
                content: phoneme.content,
                drag: true,
                resize: true,
            })
        });
    });
});


