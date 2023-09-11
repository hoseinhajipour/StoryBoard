<div class="w-100 ">
    <style>
        .app-container {
            background-color: #1e1e1e;
            scrollbar-color: gray #161616;
            color: #adadad;
            font-size: 12px;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        #timeline {
            box-sizing: border-box;
            flex-grow: 8;
            width: 100%;
            height: 100%;
            scrollbar-color: gray #161616;
        }

        ::-webkit-scrollbar {
            background: #161616;
            color: gray;
        }

        ::-webkit-scrollbar-thumb {
            background: gray;
        }

        ::-webkit-scrollbar-corner {
            background: #161616;
        }

        main {
            display: grid;
            /*grid-template-columns: ;*/
            height: 100%;
            width: 100%;
        }

        .button {
            padding: 0px;
            width: 44px;
            min-width: 44px;
            margin-right: 5px;
            color: #adadad;
            background: transparent;
            border: none;
        }

        .button:focus {
            outline: 0;
            border: none;
        }

        .button:hover {
            background: #201616;
        }

        .button:focus {
            border: none;
        }

        main {
            flex-grow: 4;
            height: 0px;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
        }

        footer {
            display: flex;
            height: 45%;
            max-height: 70%;
        }

        .toolbar {
            background-color: #383838;
            padding-left: 44px;
            max-height: 36px;
            height: 36px;
            position: relative;
            overflow: hidden;
            display: flex;
            height: 36px;
            background-color: #3c3c3c;
        }

        .outline-header {
            height: 30px;
        }

        .outline-scroll-container {
            overflow: hidden;
        }

        .outline-node {
            padding-left: 20px;
            font-size: 12px !important;
            display: flex;
            align-items: center;
            width: 100%;
            font-family: Roboto, 'Helvetica Neue', sans-serif;
            color: white;
            user-select: none;
            height: 30px;
        }

        .outline-node:hover {
            background-color: #3399ff;
        }

        .links {
            display: flex;
            align-items: center;
        }

        a {
            font-family: Roboto, 'Helvetica Neue', sans-serif;
            color: white;
            margin-right: 30px;
        }

        .logs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: 100%;
        }

        .output {
            height: 100%;
            width: 100%;
        }

        .outline {
            width: 250px;
            min-width: 150px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            align-items: stretch;
            align-content: stretch;
        }

        .content {
            overflow: scroll;
        }
    </style>

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
        <div class="links">
            <a class="git-hub-link" href="https://github.com/ievgennaida/animation-timeline-control">GitHub</a>
        </div>
    </div>
    <footer>
        <div class="outline">
            <div class="outline-header" id="outline-header"></div>
            <div class="outline-scroll-container" id="outline-scroll-container" onwheel="outlineMouseWheel(arguments[0])">
                <div class="outline-items" id="outline-container"></div>
            </div>
        </div>
        <div id="timeline"></div>
    </footer>


</div>

@push('script')
    <script>
        var outlineContainer = document.getElementById('outline-container');

        function generateModel() {
            const groupA = {
                style: {
                    fillColor: '#6B9080',
                    marginTop: 4,
                },
                keyframesStyle: {
                    shape: "rect"
                }
            };
            const groupB = {
                style: {
                    marginTop: 6,
                },
            };
            let rows = [
                {
                    selected: false,
                    draggable: false,

                    keyframes: [
                        {
                            val: 40,
                            shape: 'rhomb',
                        },
                        {
                            shape: 'rhomb',
                            val: 3000,
                            selected: false,
                        },
                    ],
                },
                {
                    selected: false,
                    keyframes: [
                        {
                            style:{
                                cursor: 'default',
                            },
                            val: 2000,
                        },
                        {
                            val: 2500,
                        },
                        {
                            val: 2600,
                        },
                    ],
                },
                {
                    keyframes: [
                        {
                            val: 1000,
                        },
                        {
                            val: 1500,
                        },
                        {
                            val: 2000,
                        },
                    ],
                },
                {
                    title: 'Groups (Limited)',
                    keyframes: [
                        {
                            val: 40,
                            max: 850,
                            group: 'a',
                        },
                        {
                            val: 800,
                            max: 900,
                            group: 'a',
                        },
                        {
                            min: 1000,
                            max: 3400,
                            val: 1900,
                            group: 'b',
                        },
                        {
                            val: 3000,
                            max: 3500,
                            group: 'b',
                        },
                        {
                            min: 3500,
                            val: 4000,
                            group: 'c',
                        },
                    ],
                },
                {
                    title: 'Groups Different Styles',
                    keyframes: [
                        {
                            val: 100,
                            max: 850,
                            group: groupA,
                        },
                        {
                            val: 500,
                            max: 900,
                            group: groupA,
                        },
                        {
                            min: 900,
                            max: 3400,
                            val: 1900,
                            group: groupB,
                        },
                        {
                            val: 4000,
                            group: groupB,
                        },
                    ],
                },
                {
                    keyframes: [
                        {
                            val: 100,
                        },
                        {
                            val: 3410,
                        },
                        {
                            val: 2000,
                        },
                    ],
                },
                {
                    title: 'Keyframe Style Customized',
                    style: {
                        groupsStyle: {
                            height: 5,
                            marginTop: "auto"
                        },
                        keyframesStyle: {
                            shape: 'rect',
                            width: 5,
                            height: 20,
                        }
                    },
                    keyframes: [
                        {
                            val: 90,
                        },
                        {
                            val: 3000,
                        },
                    ],
                },
                {},
                {
                    title: 'Max Value (Not Draggable)',
                    max: 4000,
                    keyframes: [
                        {
                            style: {
                                width: 4,
                                height: 20,
                                group: 'block',
                                shape: 'rect',
                                fillColor: 'Red',
                                strokeColor: 'Black',
                            },
                            val: 4000,
                            selectable: false,
                            draggable: false,
                        },
                        {
                            val: 1500,
                        },
                        {
                            val: 2500,
                        },
                    ],
                },
                {},
                {},
                {
                    title: 'Custom Height',
                    style: {
                        height: 100,
                        keyframesStyle: {
                            shape: 'rect',
                            width: 4,
                            height: 70,
                        },
                    },

                    keyframes: [
                        {
                            val: 40,
                            max: 850,
                            group: 'a',
                        },
                        {
                            val: 8600,
                            group: 'a',
                        },
                    ],
                },
            ];
            return rows;
        }
        const rows = generateModel();
        var timeline = new timelineModule.Timeline();
        timeline.initialize({ id: 'timeline', headerHeight: 45 });
        timeline.setModel({ rows: rows });

        // Select all elements on key down
        document.addEventListener('keydown', function (args) {
            if (args.which === 65 && timeline._controlKeyPressed(args)) {
                timeline.selectAllKeyframes();
                args.preventDefault();
            }
        });
        var logMessage = function (message, logPanel = 1) {
            if (message) {
                let el = document.getElementById('output' + logPanel);
                el.innerHTML = message + '<br/>' + el.innerHTML;
            }
        };

        var logDraggingMessage = function (object, eventName) {
            if (object.elements) {
                logMessage('Keyframe value: ' + object.elements[0].val + '. Selected (' + object.elements.length + ').' + eventName);
            }
        };

        timeline.onTimeChanged(function (event) {
            showActivePositionInformation();
        });
        function showActivePositionInformation() {
            if (timeline) {
                const fromPx = timeline.scrollLeft;
                const toPx = timeline.scrollLeft + timeline.getClientWidth();
                const fromMs = timeline.pxToVal(fromPx - timeline._leftMargin());
                const toMs = timeline.pxToVal(toPx - timeline._leftMargin());
                let positionInPixels = timeline.valToPx(timeline.getTime()) + timeline._leftMargin();
                let message = 'Timeline in ms: ' + timeline.getTime() + 'ms. Displayed from:' + fromMs.toFixed() + 'ms to: ' + toMs.toFixed() + 'ms.';
                message += '<br>';
                message += 'Timeline in px: ' + positionInPixels + 'px. Displayed from: ' + fromPx + 'px to: ' + toPx + 'px';
                document.getElementById('currentTime').innerHTML = message;
            }
        }
        timeline.onSelected(function (obj) {
            logMessage('Selected Event: (' + obj.selected.length + '). changed selection :' + obj.changed.length, 2);
        });
        timeline.onDragStarted(function (obj) {
            logDraggingMessage(obj, 'dragstarted');
        });
        timeline.onDrag(function (obj) {
            logDraggingMessage(obj, 'drag');
        });
        timeline.onKeyframeChanged(function (obj) {
            console.log('keyframe: ' + obj.val);
        });
        timeline.onDragFinished(function (obj) {
            logDraggingMessage(obj, 'dragfinished');
        });
        timeline.onMouseDown(function (obj) {
            var type = obj.target ? obj.target.type : '';
            logMessage('mousedown:' + obj.val + '.  target:' + type + '. ' + Math.floor(obj.pos.x) + 'x' + Math.floor(obj.pos.y), 2);
        });
        timeline.onDoubleClick(function (obj) {
            var type = obj.target ? obj.target.type : '';
            logMessage('doubleclick:' + obj.val + '.  target:' + type + '. ' + Math.floor(obj.pos.x) + 'x' + Math.floor(obj.pos.y), 2);
        });

        timeline.onScroll(function (obj) {
            var options = timeline.getOptions();
            if (options) {
                // Synchronize component scroll renderer with HTML list of the nodes.
                if (outlineContainer) {
                    outlineContainer.style.minHeight = obj.scrollHeight + 'px';
                    document.getElementById('outline-scroll-container').scrollTop = obj.scrollTop;
                }
            }
            showActivePositionInformation();
        });
        timeline.onScrollFinished(function (obj) {
            // Stop move component screen to the timeline when user start manually scrolling.
            logMessage('on scroll finished', 2);
        });
        generateHTMLOutlineListNodes(rows);

        /**
         * Generate html for the left menu for each row.
         * */
        function generateHTMLOutlineListNodes(rows) {
            var options = timeline.getOptions();
            var headerElement = document.getElementById('outline-header');
            headerElement.style.maxHeight = headerElement.style.minHeight = options.headerHeight + 'px';
            // headerElement.style.backgroundColor = options.headerFillColor;
            outlineContainer.innerHTML = '';
            rows.forEach(function (row, index) {
                var div = document.createElement('div');
                div.classList.add('outline-node');
                const h = (row.style ? row.style.height : 0) || options.rowsStyle.height;
                div.style.maxHeight = div.style.minHeight = h + 'px';
                div.style.marginBottom = options.rowsStyle.marginBottom + 'px';
                div.innerText = row.title || 'Track ' + index;
                outlineContainer.appendChild(div);
            });
        }

        /*Handle events from html page*/
        function selectMode() {
            if (timeline) {
                timeline.setInteractionMode('selector');
            }
        }
        function zoomMode() {
            if (timeline) {
                timeline.setInteractionMode('zoom');
            }
        }
        function noneMode() {
            if (timeline) {
                timeline.setInteractionMode('none');
            }
        }

        function removeKeyframe() {
            if (timeline) {
                // Add keyframe
                const currentModel = timeline.getModel();
                if (currentModel && currentModel.rows) {
                    currentModel.rows.forEach((row) => {
                        if (row.keyframes) {
                            row.keyframes = row.keyframes.filter((p) => !p.selected);
                        }
                    });
                }

                timeline.setModel(currentModel);
            }
        }
        function addKeyframe() {
            if (timeline) {
                // Add keyframe
                const currentModel = timeline.getModel();
                currentModel.rows.push({ keyframes: [{ val: timeline.getTime() }] });
                timeline.setModel(currentModel);

                // Generate outline list menu
                generateHTMLOutlineListNodes(currentModel.rows);
            }
        }
        function panMode(interactive) {
            if (timeline) {
                timeline.setInteractionMode(interactive ? 'pan' : 'nonInteractivePan');
            }
        }
        // Set scroll back to timeline when mouse scroll over the outline
        function outlineMouseWheel(event) {
            if (timeline) {
                this.timeline._handleWheelEvent(event);
            }
        }
        playing = false;
        playStep = 50;
        // Automatic tracking should be turned off when user interaction happened.
        trackTimelineMovement = false;
        function onPlayClick(event) {
            playing = true;
            trackTimelineMovement = true;
            if (timeline) {
                this.moveTimelineIntoTheBounds();
                // Don't allow to manipulate timeline during playing (optional).
                timeline.setOptions({ timelineDraggable: false });
            }
        }
        function onPauseClick(event) {
            playing = false;
            if (timeline) {
                timeline.setOptions({ timelineDraggable: true });
            }
        }

        function moveTimelineIntoTheBounds() {
            if (timeline) {
                if (timeline._startPos || timeline._scrollAreaClickOrDragStarted) {
                    // User is manipulating items, don't move screen in this case.
                    return;
                }
                const fromPx = timeline.scrollLeft;
                const toPx = timeline.scrollLeft + timeline.getClientWidth();

                let positionInPixels = timeline.valToPx(timeline.getTime()) + timeline._leftMargin();
                // Scroll to timeline position if timeline is out of the bounds:
                if (positionInPixels <= fromPx || positionInPixels >= toPx) {
                    this.timeline.scrollLeft = positionInPixels;
                }
            }
        }
        function initPlayer() {
            setInterval(() => {
                if (playing) {
                    if (timeline) {
                        timeline.setTime(timeline.getTime() + playStep);
                        moveTimelineIntoTheBounds();
                    }
                }
            }, playStep);
        }
        // Note: this can be any other player: audio, video, svg and etc.
        // In this case you have to synchronize events of the component and player.
        initPlayer();
        showActivePositionInformation();
        window.onresize = showActivePositionInformation;
    </script>

@endpush
