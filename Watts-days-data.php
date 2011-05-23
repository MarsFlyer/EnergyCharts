<?php
include 'amChartsData.php';

if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = 180;}

$oChart = new amChartsData;
//$sTitle = "Electricity use for ".$days." days up to ".$date1;

for ($i=1; $i<=4; $i++) {
	if (!isset($_GET["date".$i])) {break;}
	$sDate = $_GET["date".$i];
	if ($sDate == "") {break;}

	$oDate = new DateTime ($sDate);
	$sDate = $oDate->format("Y-m-d");
	
	// Total for each day:
	$sql = <<<EOT
select date(ReadingTime), truncate(avg(UsedWatts)*24,0)
  ,(select group_concat(e.Description) from Event e
    where date(e.EventTime) = date(w.ReadingTime))
 from Watts w
 where ReadingTime between '$sDate'-INTERVAL ($days+0) DAY and '$sDate'
  and UsedWatts is not null
 group by date(ReadingTime) having count(*)>100
 order by date(ReadingTime)
EOT;
	//echo $sql;
	
	$oChart->set_movingAverage(1,21,0);
	$oChart->set_bulletCol(2);
	$oChart->use_SQL($sql);
	$sKey = "Average Watt hours per day";
	$oChart->set_GraphAttribute(($i-1)*2+1,"title",$sKey);
	$sKey = "Moving Average 21 days";
	$oChart->set_GraphAttribute(($i-1)*2+2,"title",$sKey);
}
//$sTitle = $sTitle." (average=".intval($nSumTot/$iSumTot*24)." kWh/d)";

$oChart->close_SQL();
header("Content-type: text/xml");
echo $oChart->get_data();
?>
