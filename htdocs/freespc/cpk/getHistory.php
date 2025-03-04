<?php
$needAuthenticate = true;
require_once('../load.php');
require_once('../includes/chart.class.php');
require_once('unbiasing.php');
$chartTypes = array('Xbar-R','Xbar-S','I-MR','P-Chart','NP-Chart','U-Chart','C-Chart');

$requestId = $_POST['id'];
$isTeam = $_POST['isTeam'];
$chartType = $_POST['chartType'];
$chartName = $_POST['chartName'];
$minDate = $_POST['minTime'];
$maxDate = $_POST['maxTime'];

global $wpdb;
require_db();

if($isTeam == 0){
	if($chartType<TYPE_P){//xr,imr,xs
		echo "<table width='100%' border='1' bordercolor='#999999' style='border-collapse:collapse;' class='ooc_table'>
	<tr style='background-color:#DDEEFF;' align='center' height='20'><td><b>Chart</b></td><td><b>Chart Type</b></td><td><b>Sample count</b></td><td><b>Cpk</b></td><td><b>LSL</b></td><td><b>USL</b></td><td><b>Mean</b></td><td><b>Sigma</b></td><td><b>Cp</b></td><td><b>Ca</b></td><td><b>Cpl</b></td><td><b>Cpu</b></td><td><strong>Chart</strong></td></tr>";
		$cpk = getCPK($requestId);
		if($cpk['count'])
			$chartBt = "<a href='".$ABS_PATH."chart/accessChart2.php?chartId=".$requestId."&minTime=$minDate&maxTime=$maxDate' target=_blank><img src='".$ABS_PATH."img/little_chart2.gif' border=0 title='Chart' alt='Chart'/></a>";
		echo "<tr align='center' height='20'><td> $chartName </td><td> ".$chartTypes[$cpk['type']-1]." </td><td> ".$cpk['count']." </td><td> ".$cpk['cpk']." </td><td> ".$cpk['lsl']." </td><td> ".$cpk['usl']." </td><td> ".$cpk['mean']." </td><td> ".$cpk['sigma']." </td><td> ".$cpk['cp']." </td><td> ".$cpk['ca']." </td><td> ".$cpk['cpl']." </td><td> ".$cpk['cpu']." </td><td>$chartBt</td></tr>";
		echo "</table><br>";
	}elseif($chartType>TYPE_IMR){
		echo "<table width='100%' border='1' bordercolor='#999999' style='border-collapse:collapse;' class='ooc_table'>
	<tr style='background-color:#DDEEFF;' align='center' height='20'><td><b>Chart</b></td><td><b>Chart Type</b></td><td><b>Sample count</b></td><td><b>Proportion/Mean</b></td><td><strong>Chart</strong></td></tr>";
		$cpk = getCPK($requestId);
		if($cpk['count'])
			$chartBt = "<a href='".$ABS_PATH."chart/accessChart2.php?chartId=".$requestId."&minTime=$minDate&maxTime=$maxDate' target=_blank><img src='".$ABS_PATH."img/little_chart2.gif' border=0 title='Chart' alt='Chart'/></a>";
		echo "<tr align='center' height='20'><td> $chartName </td><td> ".$chartTypes[$cpk['type']-1]." </td><td> ".$cpk['count']." </td><td> ".$cpk['proportion']." </td><td>$chartBt</td></tr>";
		echo "</table><br>";
	}
}else if($isTeam == 1){
	$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=$requestId AND chart_type<4 ORDER BY CONVERT(name USING gbk)", ARRAY_A);
	if(is_array($charts)){
		echo "<span class='tips'>Variables Charts</span>";
		echo "<table width='100%' border='1' bordercolor='#999999' style='border-collapse:collapse;' class='ooc_table'>
	<tr style='background-color:#DDEEFF;' align='center' height='20'><td><b>Chart</b></td><td><b>Chart Type</b></td><td><b>Sample count</b></td><td><b>Cpk</b></td><td><b>LSL</b></td><td><b>USL</b></td><td><b>Mean</b></td><td><b>Sigma</b></td><td><b>Cp</b></td><td><b>Ca</b></td><td><b>Cpl</b></td><td><b>Cpu</b></td><td><strong>Chart</strong></td></tr>";		
		foreach($charts as $chart){
			$cpk = getCPK($chart['id']);
			if($cpk['count'])
				$chartBt = "<a href='".$ABS_PATH."chart/accessChart2.php?chartId=".$chart['id']."&minTime=$minDate&maxTime=$maxDate' target=_blank><img src='".$ABS_PATH."img/little_chart2.gif' border=0 title='Chart' alt='Chart'/></a>";
			echo "<tr align='center' height='20'><td> ".$chart['name']." </td><td> ".$chartTypes[$cpk['type']-1]." </td><td> ".$cpk['count']." </td><td> ".$cpk['cpk']." </td><td> ".$cpk['lsl']." </td><td> ".$cpk['usl']." </td><td> ".$cpk['mean']." </td><td> ".$cpk['sigma']." </td><td> ".$cpk['cp']." </td><td> ".$cpk['ca']." </td><td> ".$cpk['cpl']." </td><td> ".$cpk['cpu']." </td><td>$chartBt</td></tr>";
		}
		echo "</table><br>";
	}
	
	$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=$requestId AND chart_type>3 ORDER BY CONVERT(name USING gbk)", ARRAY_A);
	if(is_array($charts)){
		echo "<span class='tips'>Attributes Charts</span>";
		echo "<table width='100%' border='1' bordercolor='#999999' style='border-collapse:collapse;' class='ooc_table'>
	<tr style='background-color:#DDEEFF;' align='center' height='20'><td><b>Chart</b></td><td><b>Chart Type</b></td><td><b>Sample count</b></td><td><b>Proportion/Mean</b></td><td><strong>Chart</strong></td></tr>";	
		foreach($charts as $chart){
			$cpk = getCPK($chart['id']);
			if($cpk['count'])
				$chartBt = "<a href='".$ABS_PATH."chart/accessChart2.php?chartId=".$chart['id']."&minTime=$minDate&maxTime=$maxDate' target=_blank><img src='".$ABS_PATH."img/little_chart2.gif' border=0 title='Chart' alt='Chart'/></a>";
			echo "<tr align='center' height='20'><td> ".$chart['name']." </td><td> ".$chartTypes[$cpk['type']-1]." </td><td> ".$cpk['count']." </td><td> ".$cpk['proportion']." </td><td>$chartBt</td></tr>";
		}
		echo "</table><br>";
	}
}

function getCPK($chartId){
	$results = array();
	global $wpdb;
	global $minDate;
    global $maxDate;
	$chart = new Chart($chartId,true);	
	if( $chart->checkExist() ){
		$parameters = $chart->getParameters();
	}
	$points = $wpdb->get_results("SELECT * FROM chart_$chartId WHERE data_time BETWEEN '$minDate' AND '$maxDate'", ARRAY_A);
	$sampleSize = $parameters["sample_size"];
	$results = $results+array('type'=>$parameters["type"],'count'=>count($points));
	$sPooled = 0;
	$xBarBar = 0;		
	if(is_array($points)){
		$sPooledSum = 0;
		$xBarSum = 0;
		switch($parameters["type"]){
			case TYPE_XR:				
				$datas = array();			
				foreach($points as $point){					
					for($j=1;$j<$sampleSize+1;$j++){
						$datas[$j-1] = $point["x_$j"];															
					}
					$sPooledSum += getStdevPow2($datas);
					$xBarSum += $point["xbar"];
				}
				$sPooled = sqrt($sPooledSum/count($points));
				$sPooled = $sPooled/unbiasing(count($points)*($sampleSize-1));
				$xBarBar = $xBarSum/count($points);
			break;
			case TYPE_XS:		
				$sPooledSum = 0;
				$xBarSum = 0;
				foreach($points as $point){
					$sPooledSum += pow($point["stat_value"],2);
					$xBarSum += $point["xbar"];
				}
				$sPooled = sqrt($sPooledSum/count($points));
				$sPooled = $sPooled/unbiasing(count($points)*($sampleSize-1));
				$xBarBar = $xBarSum/count($points);
			break;
			case TYPE_IMR:		
				$sPooledSum = 0;
				$xBarSum = 0;
				foreach($points as $point){
					$sPooledSum += $point["stat_value"];
					$xBarSum += $point["x_1"];
				}
				$sPooled = $sPooledSum/(count($points)-1)*0.8865;
				$xBarBar = $xBarSum/count($points);
			break;		
		}		
		
				
		if($parameters["type"] < TYPE_P){
			if($sPooled == 0)return 0;
			$usl = $parameters["usl"];
			$lsl = $parameters["lsl"];
			$Ca = round(($xBarBar-($usl+$lsl)/2)/3/($usl-$lsl),3);
			$Cp = round(($usl-$lsl)/6/$sPooled,3);	
			$Cpu = round(($usl-$xBarBar)/3/$sPooled,3);
			$Cpl = round(($xBarBar-$lsl)/3/$sPooled,3);
			$Cpk = min($Cpu,$Cpl);
			
			$results = $results+array('cpk'=>$Cpk,'usl'=>$usl,'lsl'=>$lsl,'mean'=>round($xBarBar,3),'sigma'=>round($sPooled,3),'ca'=>$Ca,'cp'=>$Cp,'cpu'=>$Cpu,'cpl'=>$Cpl);							
		}
		
		
		//attribute charts
		$ngSum = 0;
		$groupSum = 0;
		if($parameters["type"] > TYPE_IMR){			
			foreach($points as $point){
				$ngSum += $point["ng_count"];
				$groupSum += $point["total_count"];
			}
		}
		
		switch($parameters["type"]){
			case TYPE_P:
				if($groupSum == 0)return 0;
				$results = $results+array('proportion'=>"P-bar = ".round($ngSum/$groupSum,5));		
			break;	
			case TYPE_NP:
				if($groupSum == 0)return 0;
				$results = $results+array('proportion'=> "NP-bar = ".round($ngSum/count($points),5)."<br>P-bar = ".round($ngSum/$groupSum,5));			
			break;
			case TYPE_U:
				if($groupSum == 0)return 0;
				$results = $results+array('proportion'=> "U-bar = ".round($ngSum/count($points),5));			
			break;
			case TYPE_C:
				if($groupSum == 0)return 0;
				$results = $results+array('proportion'=> "C-bar = ".round($ngSum,5));			
			break;
		}
	}
	return $results;
}
//StDev^2
function getStdevPow2($datas){
	$stDevPow2=0;
	$average=0;
	$sum=0;
	for($i=0;$i<count($datas);$i++){
		$sum += $datas[$i];
	}
	$average = $sum/count($datas);
	
	for($i=0;$i<count($datas);$i++){
		$stDevPow2 += pow(($datas[$i]-$average),2);
	}
	return $stDevPow2/(count($datas)-1);
}
?>