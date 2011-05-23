<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
if (isset($_GET["date1"])) {$date1 = $_GET["date1"];}
else {$date1 = "2009-07-06";}
if (isset($_GET["date2"])) {$date2 = $_GET["date2"];}
else {$date2 = "";}
if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = "";}
$oDate = new DateTime ($date1);
?>
<title>Temperature & Gas for a day - <?php echo $oDate->format("D d M Y");?></title>
	<link type="text/css" href="/jquery/ui/themes/base/ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="/jquery/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="/jquery/ui/ui.core.js"></script>
	<script type="text/javascript" src="/jquery/ui/ui.datepicker.js"></script>

	<link type="text/css" href="../demos.css" rel="stylesheet" />
	<script type="text/javascript">
	$(function() {
		$("#date1").datepicker({ dateFormat: 'yy-mm-dd' });
	});
	</script>
</head>
<body>
	<form action="Temperature-day.php" class="long">
		<label for="date1">Date</label>
		<input id="date1" name="date1" type="text" value="<?php echo $date1?>" size="11">
<!-- 	<label for="days">Number of days</label>
		<input id="days" name="days" type="text" value="<?php echo $days?>" size="3"> -->
		<input type="submit" value="Refresh"/>&nbsp;
		<a href="Temperature-day.php?date1=<?php $oDate->modify("-1 day"); echo $oDate->format("Y-m-d");?>">&lt;&nbsp;</a>&nbsp;
		<a href="Temperature-day.php?date1=<?php $oDate->modify("+2 day"); echo $oDate->format("Y-m-d");?>">&gt;&nbsp;</a>
	</form>

	<!-- amline script-->
  	<script type="text/javascript" src="/amcharts/amline/swfobject.js"></script>
	<div id="flashcontent">
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("/amcharts/amline/amline.swf", "amline", "800", "450", "8", "#FFFFFF");
		so.addVariable("path", "/amcharts/amline");
		so.addVariable("settings_file", encodeURIComponent("Temperature-day.xml?rand=<?php echo mktime();?>"));                // you can set two or more different settings files here (separated by commas)
		so.addVariable("data_file", encodeURIComponent("Temperature-day-data.php?date1=<?php echo $date1 ?>&days=<?php echo $days ?>&date2=<?php echo $date2 ?>"));
		
	//	so.addVariable("chart_data", encodeURIComponent("data in CSV or XML format"));                    // you can pass chart data as a string directly from this file
	//	so.addVariable("chart_settings", encodeURIComponent("<settings>...</settings>"));                 // you can pass chart settings as a string directly from this file
	//	so.addVariable("additional_chart_settings", encodeURIComponent("<settings>...</settings>"));      // you can append some chart settings to the loaded ones
	//  so.addVariable("loading_settings", "LOADING SETTINGS");                                           // you can set custom "loading settings" text here
	//  so.addVariable("loading_data", "LOADING DATA");                                                   // you can set custom "loading data" text here
	//	so.addVariable("preloader_color", "#999999");

		so.addParam("wmode", "transparent");
		so.write("flashcontent");
		// ]]>
	
	function amClickedOnBullet(chart_id, graph_index, value, series, url, description)
	{
	//	alert( 'Link to '+value+' '+series );
	//	window.open ('Temperature-day.php?date1='+series);
	}
	</script>
	<!-- end of amline script -->
</body>
</html>
