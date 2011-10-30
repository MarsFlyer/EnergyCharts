<?php
include 'amChartsData.php';

if (isset($_GET["days"])) {$days = $_GET["days"];}
else {$days = 180;}
if (isset($_GET["temp"])) {$temp = $_GET["temp"];}
else {$temp = 19;}

$oChart = new amChartsData;
//$sTitle = "Gas use for ".$days." days up to ".$date1;

for ($i=1; $i<=4; $i++) {
	if (!isset($_GET["date".$i])) {break;}
	$sDate = $_GET["date".$i];
	if ($sDate == "") {break;}

	$oDate = new DateTime ($sDate);
	$sDate = $oDate->format("Y-m-d");
	
	$sql = <<<EOT
SELECT w.WeatherDate
  ,round(KWH*($temp-TempAvg)/(
	  select sum($temp-TempAvg)
	  FROM weather w2
	  where w2.WeatherDate >= m.DateFrom
	   and w2.WeatherDate < m.DateTo
	   and w2.Location = 'London'
	   and w2.TempAvg < $temp
	--  having count(*) > DateDiff(m.DateTo, m.DateFrom) - 4
	  ),1) 'Allocated'
  ,(select group_concat(e.Description) from Event e
    	where date(e.EventTime) = date(w.WeatherDate))
  , ($temp-w.TempAvg) 'HDD'
  ,round(KWH/(
	  select sum($temp-TempAvg)
	  FROM weather w2
	  where w2.WeatherDate >= m.DateFrom
	   and w2.WeatherDate < m.DateTo
	   and w2.Location = 'London'
	   and w2.TempAvg < $temp
	--  having count(*) > DateDiff(m.DateTo, m.DateFrom) - 4
	  ),1) 'perHDD'
 -- , w.TempAvg
-- , m.KWH,
-- , m.DateTo
-- , w.*, m.*
FROM meter_reading m, weather w
where w.WeatherDate >= m.DateFrom
 and w.WeatherDate < m.DateTo
 and m.ReadingType = 'GAS'
-- and (m.Status <> 0 or m.Status is null) 
 and w.Location = 'London'
 and w.TempAvg < $temp
 and m.DateFrom >= '$sDate'-INTERVAL ($days+0) DAY
 and m.DateTo < '$sDate'
-- and m.DateFrom >= '2007-01-01'
-- and m.DateTo < '2009-12-01'
EOT;
//echo $sql;
	
	$oChart->set_movingAverage(1,21,0);
	$oChart->set_bulletCol(2);
	$oChart->use_SQL($sql);
	$j=4;
	$sKey = "kWh per day";
	$oChart->set_GraphAttribute(($i-1)*$j+1,"title",$sKey);
	$sKey = "Moving Average 21 days";
	$oChart->set_GraphAttribute(($i-1)*$j+2,"title",$sKey);
	$sKey = "Heating Degrees";
	$oChart->set_GraphAttribute(($i-1)*$j+3,"title",$sKey);
	$sKey = "per HDDays";
	$oChart->set_GraphAttribute(($i-1)*$j+4,"title",$sKey);
}
//$sTitle = $sTitle." (average=".intval($nSumTot/$iSumTot*24)." kWh/d)";

$oChart->close_SQL();
header("Content-type: text/xml");
echo $oChart->get_data();
?>
