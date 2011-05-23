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

for ($i=0; $i<$days; $i++) {
	$nSumDay = 0;
	$iSumDay = 0;

	//echo $i." ".$oDate->format("D d M")."<br/>";
	$date2 = $oDate->format("Y-m-d");

	$sql = <<<EOT
select 5Min, UsedWatts 
from Watts 
 right join Day on time(ReadingTime) >= TimeFrom and time(ReadingTime) <= timeto
 and ReadingTime between '$date2' and '$date2'+INTERVAL 1 DAY
 and UsedWatts is not null
order by 5Min
EOT;
	//echo $sql;
 	
	$oChart->use_SQL($sql);
	//$key = $oDate->format("D d M")."<br/> (".round(divide($oChart->get_total(1),$oChart->get_count(1))*24/1000,1)." kWh)";
	$key = $oDate->format("D d M")."<br/> (".round($oChart->get_total(1)/1000/(60/5),1)." kWh)";
	$oChart->set_GraphAttribute($i+1,"title",$key);

	$oDate->modify($dir."1 day");
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
