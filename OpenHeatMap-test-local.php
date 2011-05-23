<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>OpenHeatMap - Test</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/OpenHeatMap/jquery.openheatmap.js"></script>
<script type='text/javascript'>
$(document).ready(function()
{
    $('#openheatmap_container').insertOpenHeatMap({
        width: 1000,
        height: 800,
        source: '/OpenHeatMap/openheatmap.swf',
    });
});

function onMapCreated()
{
    var map = $.getOpenHeatMap();

    map.loadWaysFromFile('http://static.openheatmap.com/world_countries.osm');
    map.loadValuesFromFile('OpenHeatMap-test.csv');
    
    map.setSetting('show_map_tiles', true);
}
</script>
</head>
<body>
<div id="openheatmap_container"></div>
</body>
</html>
