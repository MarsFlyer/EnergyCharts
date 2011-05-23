<?php
include 'amChartsData.php';

if (isset($_GET["date1"])) {$date1 = $_GET["date1"];}
else {$date1 = "2009-06-20";}
if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = 180;}
if (isset($_GET["watts"])) {$iWatts = $_GET["watts"];}
else {$iWatts = 100;}

$oChart = new amChartsData;
$sTitle = "Electricity use for ".$days." days up to ".$date1;

for ($i=1; $i<=8; $i++) {
	if (!isset($_GET["date".$i])) {break;}
	$sDate = $_GET["date".$i];
	//echo ($i);
	//echo ($sDate);
	if ($sDate == "") {break;}

	$oDate = new DateTime ($sDate);
	$sDate = $oDate->format("Y-m-d");
	
	// Histogram for each period:
	$sql = <<<EOT
select round(round(UsedWatts/$iWatts)*$iWatts+$iWatts/2,0), round(sum(UsedWatts)/12/(
 select count(*)
 from (
 select date(ReadingTime)
 from Watts
 where ReadingTime between '$sDate'-INTERVAL $days DAY and '$sDate'
   and UsedWatts is not null
 group by date(ReadingTime)
 having count(*) > 100) as days))
from Watts
where ReadingTime between '$sDate'-INTERVAL $days DAY and '$sDate'
 and UsedWatts is not null
group by round(UsedWatts/$iWatts)*$iWatts
order by round(UsedWatts/$iWatts)*$iWatts
EOT;
//echo ($sql);	
	$oChart->set_seriesStep($iWatts);
	$oChart->set_sum(1);
	$oChart->use_SQL($sql);
	$sKey = $sDate." for ".$days." days";
	$oChart->set_GraphAttribute(($i-1)*2+1,"title",$sKey);
	$sKey = "Cummulative";
	$oChart->set_GraphAttribute(($i-1)*2+2,"title",$sKey);
}
//$sTitle = $sTitle." (average=".intval($nSumTot/$iSumTot*24)." kWh/d)";

$oChart->close_SQL();
header("Content-type: text/xml");
echo $oChart->get_data();
?>
