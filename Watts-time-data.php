<?php
include 'amChartsData.php';

if (isset($_GET["date1"])) {$date1 = $_GET["date1"];}
else {$date1 = "2009-06-20";}
if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = 1;}
if ($days > 0) {$dir = "+";}
else {$dir = "-"; $days = abs($days);}

$oDate = new DateTime ($date1);
$oChart = new amChartsData;
$sTitle = 'Electricity use for '.$days.' days from '.$oDate->format("D Y-m-d");

for ($i=1; $i<=4; $i++) {
	if (!isset($_GET["date".$i])) {break;}
	$sDate = $_GET["date".$i];
	if ($sDate == "") {break;}

	$oDate = new DateTime ($sDate);
	$sDate = $oDate->format("Y-m-d");
	
	$sql = <<<EOT
select ReadingHour, max(W), min(W), truncate(avg(W),0)
from (
  select Date(ReadingTime) as ReadingDate, DATE_FORMAT(ReadingTime,'%H') as ReadingHour
  , truncate(avg(UsedWatts),0) as W
  from Watts
  where ReadingTime between '$sDate'-INTERVAL ($days+0) DAY and '$sDate'
  group by Date(ReadingTime), DATE_FORMAT(ReadingTime,'%H')
  having avg(UsedWatts) is not null
) as  WattsDate
group by ReadingHour
EOT;
	//echo $sql;
	
	$oChart->use_SQL($sql);
	//$key = $oDate->format("D d M")."<br/> (".intval(divide($oChart->get_total(1),$oChart->get_count(1))*24).")";
	//$oChart->set_GraphAttribute($i+1,"title",$key);
}
//$sTitle = $sTitle." (average=".intval($nSumTot/$iSumTot*24)." kWh/d)";

$oChart->close_SQL();
header("Content-type: text/xml");
echo $oChart->get_data();

function divide ($top, $bot) 
{
	if ($bot == 0) {return 0;}
	else {return ($top/$bot);}
}
?>
