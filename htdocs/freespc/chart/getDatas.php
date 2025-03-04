<?php
header('Content-type: text/xml');
require_once('../load.php');

$id = $_COOKIE["chart_id"];
$type = $_COOKIE["chart_type"];
$minDate = $_COOKIE["chart_minTime"];
$maxDate = $_COOKIE["chart_maxTime"];

require_once('../includes/chart.class.php');
$chart = new Chart($id,true);	
if( $chart->checkExist() ){
	$parameters = $chart->getParameters();
}

global $wpdb;
require_db();

$datas = $wpdb->get_results("SELECT * FROM chart_$id WHERE data_time BETWEEN '$minDate' AND '$maxDate' ORDER BY data_time ASC LIMIT 1000", ARRAY_A);
echo  "<?xml version='1.0' encoding='utf-8'?>";
echo "<chart id='$id' type='$type' count='".count($datas)."' sampleSize='".$parameters['sample_size']."' usl='".$parameters['usl']."' lsl='".$parameters['lsl']."'>";
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
				echo "<value>".$data[$valueType]."</value>";
				echo "<ucl>".$data['ucl']."</ucl>";
				echo "<lcl>".$data['lcl']."</lcl>";
				echo "<value2>".$data['stat_value']."</value2>";
				echo "<ucl_2>".$data['ucl_2']."</ucl_2>";
				echo "<status>".$data['status']."</status>";
				echo "<against>".$data['against']."</against>";
				echo "<data_time>".$data['data_time']."</data_time>";
				echo "</data>";
			}
		break;
		case TYPE_P:
		case TYPE_NP:			
		case TYPE_U:
		case TYPE_C:
			$valueType = 'rate';
			if($type == TYPE_NP || $type == TYPE_C)
				$valueType = 'ng_count';
			foreach($datas as $data){
				echo "<data id='".$data['id']."'>";
				echo "<value>".$data[$valueType]."</value>";
				echo "<ucl>".$data['ucl']."</ucl>";
				echo "<lcl>".$data['lcl']."</lcl>";
				echo "<cl>".$data['cl']."</cl>";
				echo "<status>".$data['status']."</status>";
				echo "<against>".$data['against']."</against>";
				echo "<data_time>".$data['data_time']."</data_time>";
				echo "</data>";
			}
		break;
	}
}
echo "</datas>";
echo "</chart>";
?>