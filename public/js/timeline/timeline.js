var video_item_start1 = new Date(1970, 0, 1, 0, 0, 5, 800);
var video_item_end1   = new Date(1970, 0, 1, 0, 0, 13, 200);

var video_item_start2 = new Date(1970, 0, 1, 0, 0, 14, 800);
var video_item_end2   = new Date(1970, 0, 1, 0, 0, 24, 200);

var video_item_start3 = new Date(1970, 0, 1, 0, 0, 35, 800);
var video_item_end3   = new Date(1970, 0, 1, 0, 0, 47, 200);

var limit_min        = new Date(1970, 0, 1, 0, 0, 2,  000);

var timeline_items = new vis.DataSet([
    {
        id: 1,
        group : "group",
        segment_start : 10.5, segment_end  : 20.9,
        video_start   : 0   , video_length : 31.1,
        start: video_item_start1, end: video_item_end1,
        content: "video 1"
    },
    {
        id: 2,
        group : "group",
        segment_start : 10.5, segment_end  : 20.9,
        video_start   : 4.3 , video_length : 18.5,
        start: video_item_start2, end: video_item_end2,
        content: "video 2"
    },
    {
        id: 3,
        group : "group",
        segment_start : 10.5, segment_end  : 20.9,
        video_start   : 7.4 , video_length : 25.7,
        start: video_item_start3, end: video_item_end3,
        content: "video 3"
    }
]);



var items = new vis.DataSet();

// create a data set with groups

var groupCount = 3;
var names = ['John', 'Alston', 'Lee', 'Grant'];
var groups = new vis.DataSet();
for (var g = 0; g < groupCount; g++) {
    groups.add({id: g, content: names[g]});
}



var options = {
    start: new Date(1970, 0, 1, 0, 0, 0, 0),
    end: new Date(1970, 0, 1, 0, 1, 0, 0),

    align: "box",

    rollingMode: {
        follow: false,
        offset: 0.5
    },
    format: {
        minorLabels: {
            millisecond:'SSS',
            second:     's',
            minute:     'HH:mm:ss',
            hour:       'HH:mm:ss',
            weekday:    'HH:mm:ss',
            day:        'HH:mm:ss',
            week:       'HH:mm:ss',
            month:      'HH:mm:ss',
            year:       'HH:mm:ss'
        },
        majorLabels: {
            millisecond:'HH:mm:ss',
            second:     'HH:mm:ss',
            minute:     '',
            hour:       '',
            weekday:    '',
            day:        '',
            week:       '',
            month:      '',
            year:       ''
        }
    },
    editable: {
        add: true,
        updateTime: true,
        updateGroup: true,
        remove: true,
        overrideItems: true
    },
};

var container = document.getElementById('mytimeline');
var timeline = new vis.Timeline(container, timeline_items, options);

timeline.setGroups(groups);

var id = "tick";
timeline.addCustomTime(new Date(1970, 0, 1, 0, 0, 0, 0), id);
timeline.setCustomTimeMarker(null, id, true);

