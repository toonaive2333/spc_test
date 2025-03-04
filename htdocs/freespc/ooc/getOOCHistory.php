<?php
$needAuthenticate = true;
require_once('../load.php');
require_once('../includes/chart.class.php');
$chartTypes = array('Xbar-R','Xbar-S','I-MR','P-Chart','NP-Chart','U-Chart','C-Chart');

$requestId = $_POST['id'];
$isTeam = $_POST['isTeam'];
$minTime = $_POST['minTime'];
$maxTime = $_POST['maxTime'];
	
global $wpdb;
require_db();
if($isTeam == 0){
	$points = $wpdb->get_results("SELECT status,against,data_time FROM chart_$requestId WHERE data_time BETWEEN '$minTime' AND '$maxTime' ORDER BY data_time ASC", ARRAY_A);
}else if($isTeam == 1){
	$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=$requestId ORDER BY CONVERT(name USING gbk)", ARRAY_A);
}
if($isTeam == 0){
?>
<table width="100%" border="1" bordercolor="#999999" style="border-collapse:collapse;" class="ooc_table">
	<tr style="background-color:#DDEEFF;" align="center" height="20"><td><b>Sample count</b></td><td><b>OOC count</b></td><td><b>OOC Rate</b></td><td><b>待处理OOC</b></td><td><b>Delay</b></td><td><b>Chart</b></td><td><b>Chart Type</b></td></tr>
<?php
	$total = 0;
	$unDescribed = 0;
	$gotFirst = false;
	if(is_array($points))
		foreach($points as $point){
			if($point['against']>0){
				$total++;
				if($point['status']<2){
					$unDescribed++;
					if(!$gotFirst){
						$first = $point['data_time'];
						$gotFirst = true;
					}
				}
			}
		}
	if(!empty($first)){
		$earlistDate = strtotime($first);
		$delay = compareDate(time(),$earlistDate);
	}
	
	$rate = 0;
	if(count($points)>0)
		$rate = round($total*100/count($points),2)."%";
	$chart = new Chart($requestId,true);
	$parameters = $chart->getParameters();
	$check = "";
	if(count($points)>0)
		$check = "<a href='".$ABS_PATH."chart/accessChart2.php?chartId=".$requestId."&minTime=$minTime&maxTime=$maxTime' target=_blank><img src='".$ABS_PATH."img/little_chart2.gif' border=0 title='Chart' alt='Chart'/></a>";
	echo "<tr align=center><td>".count($points)."</td><td>".$total."</td><td>$rate</td><td>".$unDescribed."</td><td>".$delay."</td><td>".$check."</td><td>".$chartTypes[$parameters['type']-1]."</td></tr></table><br>";
		
}else if($isTeam == 1){
?>
<table width="100%" border="1" bordercolor="#999999" style="border-collapse:collapse;" class="ooc_table">
	<tr style="background-color:#DDEEFF;" align="center" height="20"><td><b>Chart</b></td><td><b>Sample count</b></td><td><b>OOC count</b></td><td><b>OOC Rate</b></td><td><b>待处理OOC</b></td><td><b>Delay</b></td><td><b>Chart</b></td><td><b>Chart Type</b></td></tr>
<?php
	$totalCount = 0;
	$totalOOCCount = 0;
	foreach($charts as $chart){
		$results = $wpdb->get_results("SELECT status,against,data_time FROM chart_".$chart['id']." WHERE data_time BETWEEN '$minTime' AND '$maxTime' ORDER BY data_time ASC",ARRAY_A);
		$total = 0;
		$unDescribed = 0;
		$gotFirst = false;
		$first = "";
		$delay = "";
		if(is_array($results)){
			foreach($results as $result){
				if($result['against']>0){
					$total++;
					if($result['status']<2){
						$unDescribed++;
						if(!$gotFirst){
							$first = $result['data_time'];
							$gotFirst = true;
						}
					}
				}
			}
		}
		if(!empty($first)){
			$earlistDate = strtotime($first);
			$delay = compareDate(time(),$earlistDate);
		}
		$rate = 0;
		if(count($results)>0)
			$rate = round($total*100/count($results),2)."%";
		$c = new Chart($chart['id'],true);
		$parameters = $c->getParameters();
		$check = "";
		if(count($results)>0)
			$check = "<a href='".$ABS_PATH."chart/accessChart2.php?chartId=".$chart['id']."&minTime=$minTime&maxTime=$maxTime' target=_blank><img src='".$ABS_PATH."img/little_chart2.gif' border=0 title='Chart' alt='Chart'/></a>";
		echo "<tr align=center><td>".$chart['name']."</td><td>".count($results)."</td><td>".$total."</td><td>$rate</td><td>".$unDescribed."</td><td>".$delay."</td><td>".$check."</td><td>".$chartTypes[$parameters['type']-1]."</td></tr>";
		
		$totalCount += count($results);
		$totalOOCCount += $total;
		
	}	
	echo "</table>";
	$totalRate = 0;
	if($totalCount>0)
		$totalRate = round($totalOOCCount*100/$totalCount,2)."%";
	echo "<div style='margin-top:6px'><b>Total:</b> Chart_count=".count($charts)."   Sample_count=$totalCount ";
	echo " OOC_count=$totalOOCCount ";
	echo " OOC_rate=$totalRate</div><br>";
}
?>
