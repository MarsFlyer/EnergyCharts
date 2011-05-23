<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
if (isset($_GET["date1"])) {$date1 = $_GET["date1"];}
else {$date1 = "2009-07-06";}
if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = "1";}
$oDate = new DateTime ($date1);
?>
<title>Watts for a day - <?php echo $oDate->format("D d M Y");?></title>
	<link rel="stylesheet" type="text/css" href="/css/calendar.css" media="screen" />
	<script type="text/javascript" src="/js/mootools.js"></script>
	<script type="text/javascript" src="/js/calendar.js"></script>
	<script type="text/javascript">		
	//<![CDATA[
		window.addEvent('domready', function() { 
			myCal2 = new Calendar({ date1: 'Y-m-d' }, { classes: ['alternate'], navigation: 2 });
		});
	//]]>
	</script>
</head>
<body>
 	<form action="Watts-day.php" class="long">
		<label for="date1">Date from</label>
		<input id="date1" name="date1" type="text" value="<?php echo $date1?>">
		<label for="days">Number of days</label>
		<input id="days" name="days" type="text" value="<?php echo $days?>">
		<input type="submit" value="Refresh"/>
		<a href="Watts-day.php?date1=<?php $oDate->modify("-7 day"); echo $oDate->format("Y-m-d");?>">&lt;&lt;&nbsp;</a>&nbsp;
		<a href="Watts-day.php?date1=<?php $oDate->modify("+6 day"); echo $oDate->format("Y-m-d");?>">&lt;&nbsp;</a>&nbsp;
		<a href="Watts-day.php?date1=<?php $oDate->modify("+2 day"); echo $oDate->format("Y-m-d");?>">&gt;&nbsp;</a>&nbsp;
		<a href="Watts-day.php?date1=<?php $oDate->modify("+6 day"); echo $oDate->format("Y-m-d");?>">&gt;&gt;&nbsp;</a>
	</form>

<!-- amline script-->
  <script type="text/javascript" src="/amcharts/amline/swfobject.js"></script>
	<div id="flashcontent">
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("/amcharts/amline/amline.swf", "amline", "900", "500", "8", "#FFFFFF");
		so.addVariable("path", "/amcharts/amline");
		so.addVariable("settings_file", encodeURIComponent("Watts-day.xml?rand=<?php echo mktime();?>"));                // you can set two or more different settings files here (separated by commas)
		so.addVariable("data_file", encodeURIComponent("Watts-day-data.php?date1=<?php echo $date1 ?>&days=<?php echo $days ?>"));
		
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