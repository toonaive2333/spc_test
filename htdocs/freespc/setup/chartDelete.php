<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");

$id = $_GET['id'];
if(empty($id))
	tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
global $wpdb;
require_db();
require_once('../includes/chart.class.php');
$chart = new Chart($id,true);	
if( $chart->checkExist() ){
	$chart->deleteSelf();
}

echo "<script>parent.location.href='myTeams.php';</script>";
?>