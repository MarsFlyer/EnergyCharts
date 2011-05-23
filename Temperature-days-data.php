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
/*	
	// Weather for each day:
	$sql = "select date(WeatherDate), TempAvg";
	$sql .= "  ,(select group_concat(e.Description) from Event e";
	$sql .= "    where date(e.EventTime) = date(w.WeatherDate))";
	$sql .= " from Weather w";
	$sql .= " where WeatherDate between '".$sDate."'-INTERVAL ".($days+0)." DAY and '".$sDate."'";
	$sql .= " order by date(WeatherDate)";
	
	// Temperature sensor for each day:
	$sql = "select date(ReadingTime), max(temp1), max(temp2), min(temp1), min(temp2)";
	$sql .= "  ,(select group_concat(e.Description) from Event e";
	$sql .= "    where date(e.EventTime) = date(t.ReadingTime))";
	$sql .= " from temperature t";
	$sql .= " where ReadingTime between '".$sDate."'-INTERVAL ".($days+0)." DAY and '".$sDate."'";
	$sql .= " group by date(ReadingTime) having count(*)>100";
	$sql .= " order by date(ReadingTime)";
*/	
	$sql = <<<EOT
select dt, max(max1), max(min1), max(max2), max(min2), max(avg1), max(evt)
from (
select date(ReadingTime) as dt
  , max(temp1) as max1, min(temp1) as min1
  , max(temp2) as max2, min(temp2) as min2
  , '' as avg1
  ,(select group_concat(e.Description) from Event e
    where date(e.EventTime) = date(t.ReadingTime)) as evt
 from temperature t
 where ReadingTime between '$sDate'-INTERVAL ($days+0) DAY and '$sDate'
 group by date(ReadingTime) having count(*)>100
UNION
select date(WeatherDate) as a
  , '' as max1, '' as min1
  , '' as max2, '' as min2
  , TempAvg as avg1
  ,(select group_concat(e.Description) from Event e
    where date(e.EventTime) = date(w.WeatherDate)) as evt
 from Weather w
 where WeatherDate between '$sDate'-INTERVAL ($days+0) DAY and '$sDate'
) as u
 group by dt
 order by dt
EOT;
	//echo $sql;
	
	$oChart->set_movingAverage(5,14,1);
	$oChart->set_bulletCol(6);
	$oChart->use_SQL($sql);
	$sKey = "Inside Max";
	$oChart->set_GraphAttribute(($i-1)*2+1,"title",$sKey);
	$sKey = "Inside Min";
	$oChart->set_GraphAttribute(($i-1)*2+2,"title",$sKey);
	$sKey = "Outside Max";
	$oChart->set_GraphAttribute(($i-1)*2+3,"title",$sKey);
	$sKey = "Outside Min";
	$oChart->set_GraphAttribute(($i-1)*2+4,"title",$sKey);
	$sKey = "Temperature";
	$oChart->set_GraphAttribute(($i-1)*2+5,"title",$sKey);
	$sKey = "Moving Average 21 days";
	$oChart->set_GraphAttribute(($i-1)*2+6,"title",$sKey);
}
//$sTitle = $sTitle." (average=".intval($nSumTot/$iSumTot*24)." kWh/d)";

$oChart->close_SQL();
header("Content-type: text/xml");
echo $oChart->get_data();
?>
