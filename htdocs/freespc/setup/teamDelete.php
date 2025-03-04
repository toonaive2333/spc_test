<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");

$id = $_GET['id'];
if(empty($id))
	tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");

global $wpdb;
require_db();
$wpdb->query("DELETE FROM teams WHERE id=$id");
$wpdb->query("DELETE FROM members_team WHERE team_id=$id");

$charts = $wpdb->get_col("SELECT id FROM charts WHERE team=$id");
require_once('../includes/chart.class.php');
foreach($charts as $chartId){
	$chart = new Chart($chartId,true);	
	if( $chart->checkExist() ){
		$chart->deleteSelf();
	} 
}
echo "<script>location.href='myTeams.php';</script>";
?>