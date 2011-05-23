<?php
include 'amChartsData.php';

if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = 180;}
if (isset($_GET["low"])) {$low = $_GET["low"];}
else {$low = 150;}
if (isset($_GET["high"])) {$high = $_GET["high"];}
else {$high = 1500;}

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
select date(ReadingTime)
  , truncate(avg(UsedWatts)*24,0) as kWh
  ,(select group_concat(e.Description) from Event e
    where date(e.EventTime) = date(w.ReadingTime)) as Event
  , truncate(avg(case when UsedWatts < $low then UsedWatts else 0 end)*24,0) as Low
  , truncate(avg(case when UsedWatts < $high then UsedWatts else 0 end)*24,0) as Medium
  -- , truncate(avg(case when (UsedWatts >= $low and UsedWatts < $high) then UsedWatts else 0 end)*24,0) as Medium
 -- , truncate(avg(case when UsedWatts >= $high then UsedWatts else 0 end)*24,0) as High
 from Watts w
 where ReadingTime between '$sDate'-INTERVAL ($days+0) DAY and '$sDate'
  and UsedWatts is not null
 group by date(ReadingTime) having count(*)>100
 order by date(ReadingTime)
EOT;
	//echo $sql;
	
	$iCols = 4;
	$oChart->set_movingAverage(1,21,0);
	$oChart->set_bulletCol(2);
	$oChart->use_SQL($sql);
	$sKey = "Average Watt hours per day";
	$oChart->set_GraphAttribute(($i-1)*$iCols+1,"title",$sKey);
	$sKey = "Moving Average 21 days";
	$oChart->set_GraphAttribute(($i-1)*$iCols+2,"title",$sKey);
	$sKey = "Low (".$low."W)";
	$oChart->set_GraphAttribute(($i-1)*$iCols+3,"title",$sKey);
	$sKey = "Medium (".$high."W)";
	$oChart->set_GraphAttribute(($i-1)*$iCols+4,"title",$sKey);
}
//$sTitle = $sTitle." (average=".intval($nSumTot/$iSumTot*24)." kWh/d)";

$oChart->close_SQL();
header("Content-type: text/xml");
echo $oChart->get_data();
?>
