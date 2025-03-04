<?php
require_once('../load.php');

$chartId = $_POST['id'];
global $wpdb;
require_db();
$min = $wpdb->get_var("SELECT data_time FROM chart_$chartId ORDER BY data_time ASC LIMIT 1");
$max = $wpdb->get_var("SELECT data_time FROM chart_$chartId ORDER BY data_time DESC LIMIT 1");
$count = $wpdb->get_var("SELECT count(*) FROM chart_$chartId");
$min = substr($min,0,10);
$max = substr($max,0,10);
$min = str_replace('-','/',$min);
$max = str_replace('-','/',$max);
if($count>0)
	echo "(".$min." - ".$max." ; Count=$count)";

?>