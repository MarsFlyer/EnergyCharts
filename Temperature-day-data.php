<?php
include 'amChartsData.php';

if (isset($_GET["date1"])) {$date1 = $_GET["date1"];}
else {$date1 = "2009-06-20";}
if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = 180;}

$oChart = new amChartsData;
$sTitle = "Temperature for ".$days." days up to ".$date1;

for ($i=1; $i<=2; $i++) {
	
	switch ($i) {
		case 1:
			$sDate = $date1; break;
		case 2:
			if (!isset($_GET["date2"]) || $_GET["date2"]=="") {
				break 2;
			} else {
				$sDate = $_GET["date2"];
				break;
			}
	}
	$oDate = new DateTime ($sDate);
	$sDate = $oDate->format("Y-m-d");
	$sql = <<<EOT
select TimeFrom /*5Min*/, temp1, temp2, light, gas*114.2
from temperature right join day 
	on time(ReadingTime) >= TimeFrom and time(ReadingTime) <= timeto
 and ReadingTime between '$date1' and '$date1'+INTERVAL 1 DAY
 order by 5Min
EOT;
	//echo $sql;
	
	//$oChart->set_movingAverage(5,14,1);
	//$oChart->set_bulletCol(6);
	$oChart->use_SQL($sql);
	$sKey = "Inside Temperature";
	$oChart->set_GraphAttribute(($i-1)*2+1,"title",$sKey);
	$sKey = "Outside Temperature";
	$oChart->set_GraphAttribute(($i-1)*2+2,"title",$sKey);
	$sKey = "Light";
	$oChart->set_GraphAttribute(($i-1)*2+3,"title",$sKey);
	$sKey = $oDate->format("D d M")."<br/> (".round($oChart->get_total(4)/1000,1)." kWh)";
	$oChart->set_GraphAttribute(($i-1)*2+4,"title",$sKey);
	//$sKey = "Outside Min";
	//$oChart->set_GraphAttribute(($i-1)*2+4,"title",$sKey);
	//$sKey = "Temperature";
	//$oChart->set_GraphAttribute(($i-1)*2+5,"title",$sKey);
	//$sKey = "Moving Average 21 days";
	//$oChart->set_GraphAttribute(($i-1)*2+6,"title",$sKey);
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
