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
    <div  class="row">
        <div class="col-12">
            <livewire:inc.timeline/>

            <div class="toolbar">
                <button class="button mat-icon material-icons mat-icon-no-color" title="Timeline selection mode"
                        onclick="selectMode()">tab_unselected</button>
                <button class="button mat-icon material-icons mat-icon-no-color"
                        title="Timeline pan mode with the keyframe selection." onclick="panMode(true)">pan_tool_alt</button>
                <button class="button mat-icon material-icons mat-icon-no-color" title="Timeline pan mode non interactive"
                        onclick="panMode(false)">pan_tool</button>
                <button class="button mat-icon material-icons mat-icon-no-color"
                        title="Timeline zoom mode. Also ctrl + scroll can be used." onclick="zoomMode()">search</button>
                <button class="button mat-icon material-icons mat-icon-no-color" title="Only view mode."
                        onclick="noneMode()">visibility</button>
                <div style="width: 1px; background: gray; height: 100%"></div>
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
                <div style="flex: 1"></div>
                <button class="flex-left button mat-icon material-icons mat-icon-no-color" title="Remove selected keyframe"
                        onclick="removeKeyframe()">close</button>
                <button class="flex-left button mat-icon material-icons mat-icon-no-color" title="Add new track with the keyframe"
                        onclick="addKeyframe()">add</button>

                <label id="currentTime" class="d-none"></label>
            </div>
            <div class="timeline_Area">
                <div class="outline">
                    <div class="outline-header" id="outline-header"></div>
                    <div class="outline-scroll-container" id="outline-scroll-container" onwheel="outlineMouseWheel(arguments[0])">
                        <div class="outline-items" id="outline-container"></div>
                    </div>
                </div>
                <div id="timeline"></div>
            </div>
        </div>

    </div>


</div>
