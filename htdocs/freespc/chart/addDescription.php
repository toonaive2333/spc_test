<?php
require_once('../load.php');

$chartId = $_COOKIE['chart_id'];
$pointId = $_POST['pointId'];
$description = trim($_POST['description']);
if($description == "")
	return;
$remark .= ".............................................................";
$remark .= ".............................................................";
$remark .= "\n";
$remark .= $description;
$remark .= " <font color=\'#6699FF\'>(".$_COOKIE['login_name']." ".date('y-m-d H:i').")<\/font>\n";
global $wpdb;
require_db();
$wpdb->query("UPDATE chart_$chartId SET remark=concat('$remark',remark),status=2 WHERE id=$pointId ");//status=0:narmal,1:bad,2:commeded
echo 1;
?>