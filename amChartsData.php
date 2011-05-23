<?php
class amChartsData
{
/*	
o	SetMovingAverage (Column integer, Units integer)
o	UseSQL (SQL string)
Run the SQL and create a line for each column
o	GetSum (GraphId integer)
o	SetKey (GraphId integer, Key string)
o	GetXML XML
o	GetData string
*/
	var $mysqli;
	var $aColour;
	var $rows;
	var $charts;
	var $oXML;
	var $nodeSeries;
	var	$nodeGraphs;
	var $nodeLabels;
	var $aTotal;
	var $aCount;
	var $bulletSize;
	var $bulletType;
	var $bulletCol;
	var $maCol;
	var $maCount;
	var $maDP;
	var $seriesStep;
	var $sumCol;
	
	public function __construct()
	{
		$this->aColour = array ("#000099","#FF0000","#FF00FF","#009900","#009999","#990000","#990099","#999900","#0000FF","#00FF00");
		$this->mysqli = new mysqli ('127.0.0.1','root','gromit','energy');
		$sXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><chart></chart>";
		$this->oXml = simplexml_load_string($sXml);
		$this->nodeSeries = $this->oXml->addChild("series","");
		$this->nodeGraphs = $this->oXml->addChild("graphs","");
		$this->nodeLabels = $this->oXml->addChild("labels","");
		$this->charts = 0;
		$this->aTotal = array();
		$this->aCount = array();
		$this->bulletType = "square_outline";
		$this->bulletSize = "10";
		$this->bulletCol = 999;
		$this->maCol = 999;
		$this->seriesStep = "";
		$this->sumCol = 999;
	}
	public function set_bulletCol ($col) {
		$this->bulletCol = $col;
	}
	public function set_movingAverage ($col, $count, $decimals) {
		$this->maCol = $col;
		$this->maCount = $count;
		$this->maDP = $decimals;
	}
	public function set_seriesStep ($step) {
		$this->seriesStep = $step;
	}
	public function set_sum ($col) {
		$this->sumCol = $col;
	}
	public function use_SQL ($sql) {
		///echo $sql;
		$debug = 0;
		$oMaths = new ChartMaths;
		$value = null;
		$label = "below";
		if ($result = $this->mysqli->query($sql)) {	
			if ($debug > 0) {echo " sql:".$sql;}
			$j = 0;  // real row
			$x = 0;  // series
			$last = 0;
			$nodeGraph = array();
			$m = 0;  // graphs
			$last = 0 - $this->seriesStep;
			while ($row = $result->fetch_row()) {
				$k = 0;  // column
				$m = 0;  // graphs  
				// graph 
				foreach ($row as $key => $col) {
					$value = $row[$k];
					if ($debug > 0) {echo ";".$j.",".$k.",".$m.",".$value;}
					if ($k==0) { // First column is series
						// Fill any missing steps
						if ($this->seriesStep <> "") {
							//echo " Fill ".$value;
							for ($step=$last+$this->seriesStep; $step<$value; $step+= $this->seriesStep) {
								//echo " step ".$step;
								// First query - create series:
								if ($this->charts==0) { 
									//echo " xid ".$x;
									$nodeValue = $this->nodeSeries->addChild("value",$step);
									$nodeValue->addAttribute("xid", $x);					
								}
								$x++;
							}
							$last = $value;
						}
						// First query - create series:
						if ($this->charts==0) { 
							$nodeValue = $this->nodeSeries->addChild("value",$value);
							$nodeValue->addAttribute("xid", $x);
						}
					}
					else {  // Data & Description columns.
						$m++;  // graphs
						if ($j==0) {
							// First row - create new graphs:
							// Do not create for bullets.
							if ($k <> $this->bulletCol) {
								if ($debug > 0) {echo " add graph:".$m;}
								//$this->charts++;
								$nodeGraph[$m] = $this->nodeGraphs->addChild("graph","");
								$nodeGraph[$m]->addAttribute("gid", $m+$this->charts);
								// Create totals:
								$this->aTotal[$m] = 0;
								$this->aCount[$m] = 0;
								// Add moving averages.
								if ($k == $this->maCol  || $k == $this->sumCol) {
									if ($debug > 0) {echo ".add MA/sum ".$value;}
									$nodeGraph[$m+1] = $this->nodeGraphs->addChild("graph","");
									$nodeGraph[$m+1]->addAttribute("gid", $m+1+$this->charts);
									//$this->charts++;
								}
							}
						}
						// Create values:
						if (is_numeric($value) && $k <> $this->bulletCol) {
							$nodeValue = $nodeGraph[$m]->addChild("value",$value);
							$nodeValue->addAttribute("xid", $x);
							//$nodeValue->addAttribute("col",substr($col,0,6));
							// Keep totals:
							$this->aTotal[$m] += $value;
							$this->aCount[$m] += 1;
							// Show totals:
							if ($k == $this->sumCol) {
								$value = $this->aTotal[$m];
								$m++;
								$nodeValue = $nodeGraph[$m]->addChild("value",$value);
								$nodeValue->addAttribute("xid", $x);
							}
						}
						// Moving Average:
						if ($k == $this->maCol) {
							$m++;
							// Use current value and number of items:
							$value = $oMaths->roundnull($oMaths->MovingAvg($value, $this->maCount),$this->maDP);
							$nodeValue = $nodeGraph[$m]->addChild("value",$value);
							$nodeValue->addAttribute("xid", $x);
						}
						// Create bullets:
						if ($k == $this->bulletCol) {
							//$m--;
							if ($value <> "") {
								$nodeValue->addAttribute("description", $value);
								$nodeValue->addAttribute("bullet", $this->bulletType);
								$nodeValue->addAttribute("bullet_size", $this->bulletSize);
								if ($label == "below") {$label="above";}  
								else {$label="below";}  
								$nodeValue->addAttribute("label_position", $label);
							}
						}
						// make sure the grpah has at least one value:
						//$nodeValue = $nodeGraph[$k]->addChild("value",0);
						//$nodeValue->addAttribute("xid", $j);
					}
					$k++;  // columns
				}			
				$j++;  // rows
				$x++;  // series
			}
			$this->rows = $x;
			$this->charts += $m;
			///echo $this->charts;
			if ($this->maCol <> 999) {
				// Show guide for last value:
				$value = $oMaths->roundnull($oMaths->MovingAvg(null, $this->maCount),$this->maDP);
				$this->set_Guide($value);
			}
		}	
	}
	public function get_total ($col)
	{
		return $this->aTotal[$col];
	}
	public function get_count ($col)
	{
		return $this->aCount[$col];
	}
	public function set_GraphAttribute ($gid, $attribute, $value) {
		$nodeValues = $this->nodeGraphs->xpath('graph['.$gid.']');
		foreach ($nodeValues as $nodeValue) {
			$nodeValue->addAttribute($attribute,$value);
		}
	}
	public function set_Guide ($value) {
		$nodeValue = $this->oXml->addChild("guides","");
		$nodeValue = $nodeValue->addChild("guide","");
		$nodeValue = $nodeValue->addChild("start_value",$value);
	}
	public function get_XML () {
		return $this->oXml;
	}
	public function get_data () {
		return $this->oXml->asXML();
	}
	public function close_SQL () {
		$this->mysqli->close();
	}
}

class ChartMaths
{
	var $aMoving;
	var $maFull;
	
	public function __construct()
	{
		$this->aMoving = array();
		$this->maFull = false;
	}
	public function MovingAvg ($value, $count) 
	{
		if ($value <> null) {
			$this->aMoving[] = $value;
		}
		while (count($this->aMoving) > $count) {
//		if (count($this->aMoving) > $count || $this->maFull) {
//			$this->maFull = true;
			array_shift($this->aMoving);
		}
		$total = 0;
		$iMA = 0;
		foreach ($this->aMoving as $val) {
			$total += $val;
			$iMA += 1;
		}
		if (count($this->aMoving) >= $count) {return ($total/$iMA);}
		else {return null;}
	}
	public function roundnull ($value, $decimals) 
	{
		if ($value == null) {return null;}
		else {return round($value, $decimals);}
	}
	public function intnull ($value) 
	{
		if ($value == null) {return null;}
		else {return intval($value);}
	}
	
	public function divide ($top, $bot) 
	{
		if ($bot == 0) {return 0;}
		else {return ($top/$bot);}
	}
}
?>