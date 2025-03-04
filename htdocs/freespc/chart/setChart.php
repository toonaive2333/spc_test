<?php
require_once('../load.php');
require_once('../includes/chart.class.php');
$chart = new Chart($_POST['id'],true);	
if( $chart->checkExist() )
	$parameters = $chart->getParameters();
		
try{
	setcookie("chart_id",$_POST['id'],time()+3600);
	setcookie("chart_type",$_POST['type'],time()+3600);
	setcookie("sample_size",$parameters['sample_size'],time()+3600);
	setcookie("chart_minTime",$_POST['minTime'],time()+3600);
	setcookie("chart_maxTime",$_POST['maxTime'],time()+3600);
	//setcookie("chart_name",unescape($_POST['chartName']),time()+3600);
	setcookie("chart_name",($_POST['chartName']),time()+3600);
	echo 1;
}catch(Exception $e){
	return $e->getMessage();
}
?>