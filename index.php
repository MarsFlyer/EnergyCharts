<html>
<head>
<?php
if (isset($_GET["date1"])) {
	$oDate = new DateTime ($_GET["date1"]);
}
else {
	$oDate = new DateTime();
	$oDate->modify("-1 week");
}
if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = 180;}
$sDate1 = $oDate->format("Y-m-d");
$oDate->modify("-1 week");
$sDate2 = $oDate->format("Y-m-d");

?>
<title>Energy information for the House</title>
</head>
<body>
<h1>Electricity</h1>

<p><a href="Watts-days.php?date1=<?php echo $sDate1 ?>">
1 year, average Watt hours by day + moving average 21 days.
</a></p>

<p><a href="Watts-year.php?date1=<?php echo $sDate1 ?>">
1 year, average Watt hours by week.
</a></p>

<p><a href="Watts.php?date1=<?php echo $sDate1 ?>&days=7">
Each day of the week, average Watts by 5 minute.
</a></p>

<p><a href="Watts-weeks.php?date1=<?php echo $sDate1 ?>">
2 weeks, week days &amp; weekends, average Watts by 1 hour.
</a></p>

<p><a href="Watts-dow.php?date1=<?php echo $sDate1 ?>">
4 weeks, average Watt hours by day of week.
</a></p>

<p><a href="Watts-histogram.php?date1=<?php echo $sDate1 ?>">
n days, Histogram of Watt hours by Watt.
</a></p>

<h1>Gas</h1>

<p><a href="Gas-days.php?date1=<?php echo $sDate1 ?>&days=<?php echo $days ?>">
Gas use from readings allocated by Heating Degrees.
</a></p>

<h1>Temperature - Solar Gain</h1>

<p><a href="Temperature-days.php?date1=<?php echo $sDate1 ?>&days=<?php echo $days ?>">
Daily Temperature (min &amp; max).
</a></p>

<p><a href="Temperature-day.php?date1=<?php echo $sDate1 ?>&date2=<?php echo $sDate2 ?>">
Two charts comparing two days, Temperature &amp; Light by 5 minutes.
</a></p>

</body>
</html>
