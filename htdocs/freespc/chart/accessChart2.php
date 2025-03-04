<?php
ob_start();
require_once('../load.php');
require_once('../includes/chart.class.php');
$chartId = $_GET['chartId'];
$minTimes = $_GET['minTime'];
$maxTimes = $_GET['maxTime'];
$chart = new Chart($chartId,true);
if( $chart->checkExist() )
	$parameters = $chart->getParameters();
	
$mins = explode(" ",$minTimes);
$maxs = explode(" ",$maxTimes);
$minDate = $mins[0];
$maxDate = $maxs[0];
$minTime = $mins[1];
$maxTime = $maxs[1];

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