<?php
ob_start();
require_once('../load.php');
require_once('../includes/chart.class.php');
$chartId = $_GET['chartId'];
$pointId = $_GET['pointId'];
$chart = new Chart($chartId,true);
if( $chart->checkExist() )
	$parameters = $chart->getParameters();
	
global $wpdb;
require_db();
$minTimes = $wpdb->get_col("SELECT data_time FROM chart_$chartId WHERE id<$pointId ORDER BY data_time DESC LIMIT 50");
$maxTimes = $wpdb->get_col("SELECT data_time FROM chart_$chartId WHERE id>$pointId ORDER BY data_time ASC LIMIT 50");
if(count($minTimes)<1)
	$minTimes = $wpdb->get_col("SELECT data_time FROM chart_$chartId WHERE id=$pointId");
if(count($maxTimes)<1)
	$maxTimes = $wpdb->get_col("SELECT data_time FROM chart_$chartId WHERE id=$pointId");
	
$mxTimes = strtotime($maxTimes[count($maxTimes)-1]);
$mxTimes = $mxTimes+3600;

$minTimes = str_replace('-','/',$minTimes[count($minTimes)-1]);
$maxTimes = date('Y/m/d H:i:s',$mxTimes);

$mins = explode(" ",$minTimes);
$maxs = explode(" ",$maxTimes);
$minDate = $mins[0];
$maxDate = $maxs[0];
$minTime = $mins[1];
$maxTime = $maxs[1];
$minTime = explode(":",$minTime);
$minTime = (int)$minTime[0].":00";
$maxTime = explode(":",$maxTime);
$maxTime = (int)$maxTime[0].":30";
try{
	setcookie("chart_id",$chartId,time()+3600*24*10);
	setcookie("chart_type",$parameters['type'],time()+3600*24*10);
	setcookie("chart_minTime","$minDate"." "."$minTime",time()+3600*24*10);
	setcookie("chart_maxTime","$maxDate"." "."$maxTime",time()+3600*24*10);
	setcookie("chart_name",($parameters['name']),time()+3600*24*10);
	redirect("../chart/");
}catch(Exception $e){
	return $e->getMessage();
}
?>