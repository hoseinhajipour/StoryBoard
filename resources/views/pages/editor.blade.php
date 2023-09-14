<div>
    <div class="row">
        <div class="col-8">
            <canvas id="renderCanvas"></canvas>
        </div>
        <div class="col-4 height_ctrl">
            <livewire:inc.asset-loader/>
            <livewire:inc.tree-view-objects/>
            <livewire:inc.lip-sync-window/>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <livewire:inc.timeline/>

            <div class="toolbar">
                <button class="button mat-icon material-icons mat-icon-no-color"
                        title="Use external player to play\stop the timeline. For the demo simple setInterval is used."
                        onclick="onPlayClick()">
                    play_arrow
                </button>
                <button class="button mat-icon material-icons mat-icon-no-color"
                        title="Use external player to play\stop the timeline. For the demo simple setInterval is used."
                        onclick="onPauseClick()">
                    pause
                </button>

                <button class="button mat-icon material-icons mat-icon-no-color"
                        title="Use external player to play\stop the timeline. For the demo simple setInterval is used."
                        onclick="onStopClick()">
                    stop
                </button>
                <div style="flex: 1"></div>
                <button class="flex-left button mat-icon material-icons mat-icon-no-color"
                        title="Remove selected keyframe"
                        onclick="removeKeyframe()">close
                </button>
                <button class="flex-left button mat-icon material-icons mat-icon-no-color"
                        title="Add new track with the keyframe"
                        onclick="addKeyframe()">add
                </button>

                <label id="currentTime" class="d-none"></label>
            </div>
            <div class="timeline_Area">
                <div class="outline">
                    <div class="outline-header" id="outline-header"></div>
                    <div class="outline-scroll-container" id="outline-scroll-container"
                         onwheel="outlineMouseWheel(arguments[0])">
                        <div class="outline-items" id="outline-container"></div>
                    </div>
                </div>
                <div id="timeline"></div>
            </div>
        </div>

    </div>

    @push('script')
        @if($project->file)
            <script>

                BABYLON.SceneLoader.Load("", "{{url($project->file)}}", engine, function (newscene) {
                    scene.dispose();


                    scene = newscene;
                    console.log(scene);
                });
            </script>
        @endif
    @endpush
</div>
