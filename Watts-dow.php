<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Watts by day of the week</title>
<?php
if (isset($_GET["date1"])) {$date1 = $_GET["date1"];}
else {$date1 = "2009-07-06";}
if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = "1";}
if (isset($_GET["date2"])) {$date2 = $_GET["date2"];}
else {$date2 = "";}
?>
</head>
<body>
<!-- saved from url=(0013)about:internet -->
<!-- amline script-->
  <script type="text/javascript" src="/amcharts/amline/swfobject.js"></script>
	<div id="flashcontent">
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("/amcharts/amline/amline.swf", "amline", "900", "500", "8", "#FFFFFF");
		so.addVariable("path", "/amcharts/amline");
		so.addVariable("settings_file", encodeURIComponent("Watts-dow.xml?rand=<?php echo mktime();?>"));                // you can set two or more different settings files here (separated by commas)
		so.addVariable("data_file", encodeURIComponent("Watts-dow-data.php?date1=<?php echo $date1 ?>&days=<?php echo $days ?>&date2=<?php echo $date2 ?>"));
		
//	so.addVariable("chart_data", encodeURIComponent("data in CSV or XML format"));                    // you can pass chart data as a string directly from this file
//	so.addVariable("chart_settings", encodeURIComponent("<settings>...</settings>"));                 // you can pass chart settings as a string directly from this file
//	so.addVariable("additional_chart_settings", encodeURIComponent("<settings>...</settings>"));      // you can append some chart settings to the loaded ones
//  so.addVariable("loading_settings", "LOADING SETTINGS");                                           // you can set custom "loading settings" text here
//  so.addVariable("loading_data", "LOADING DATA");                                                   // you can set custom "loading data" text here
//	so.addVariable("preloader_color", "#999999");
		so.write("flashcontent");
		// ]]>
function amClickedOnBullet(chart_id, graph_index, value, series, url, description)
{
//	alert( 'Link to '+value+' '+series );
//	window.open ('Watts-day.php?date1='+series+'&days=7');
}
</script>
<!-- end of amline script -->
</body>
</html>
