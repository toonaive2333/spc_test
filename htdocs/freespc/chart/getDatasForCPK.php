<?php
header('Content-type: text/xml');
require_once('../load.php');

$id = $_COOKIE["chart_id"];
$type = $_COOKIE["chart_type"];
$minDate = $_COOKIE["chart_minTime"];
$maxDate = $_COOKIE["chart_maxTime"];
$sampleSize = $_COOKIE['sample_size'];

require_once('../includes/chart.class.php');
$chart = new Chart($id,true);	
if( $chart->checkExist() ){
	$parameters = $chart->getParameters();
}
global $wpdb;
require_db();

$datas = $wpdb->get_results("SELECT * FROM chart_$id WHERE data_time BETWEEN '$minDate' AND '$maxDate'", ARRAY_A);
echo  "<?xml version='1.0' encoding='utf-8'?>";
echo "<chart id='$id' type='$type' count='".count($datas)."' sampleSize='$sampleSize' usl='".$parameters['usl']."' lsl='".$parameters['lsl']."'>";
echo "<datas>";
if(is_array($datas)){
	switch($type){
		case TYPE_XR:
		case TYPE_XS:			
		case TYPE_IMR:
			$valueType = 'xbar';
			if($type == TYPE_IMR)
				$valueType = 'x_1';
			foreach($datas as $data){
				echo "<data id='".$data['id']."'>";
				for($i=1;$i<=$sampleSize;$i++){
					echo "<v_$i>".$data["x_$i"]."</v_$i>";
				}
				echo "<xbar>".$data[$valueType]."</xbar>";
				echo "<stat>".$data['stat_value']."</stat>";
				echo "</data>";
			}
		break;
	}
}
echo "</datas>";
echo "</chart>";
?>