<?php
header('Content-type: text/xml');
require_once('../load.php');

$id = $_COOKIE["chart_id"];
$type = $_COOKIE["chart_type"];
$minDate = $_COOKIE["chart_minTime"];
$maxDate = $_COOKIE["chart_maxTime"];

global $wpdb;
require_db();

$datas = $wpdb->get_results("SELECT * FROM chart_$id WHERE data_time BETWEEN '$minDate' AND '$maxDate' ORDER BY data_time ASC LIMIT 1000", ARRAY_A);
echo  "<?xml version='1.0' encoding='utf-8'?>";
echo "<chart id='$id' type='$type' count='".count($datas)."'>";
echo "<datas>";
if(is_array($datas)){
	switch($type){
		case TYPE_P:
		case TYPE_NP:	
		case TYPE_U:		
			foreach($datas as $data){
				echo "<data id='".$data['id']."'>";
				echo "<ng_count>".$data['ng_count']."</ng_count>";
				echo "<total_count>".$data['total_count']."</total_count>";
				echo "</data>";
			}
		break;
		case TYPE_C:		
			foreach($datas as $data){
				echo "<data id='".$data['id']."'>";
				echo "<ng_count>".$data['ng_count']."</ng_count>";
				echo "</data>";
			}
		break;
	}
}
echo "</datas>";
echo "</chart>";
?>