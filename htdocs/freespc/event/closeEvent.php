<?php
require_once('../load.php');

$event = $_POST['event'];
$status = $_POST['status'];

global $wpdb;
require_db();
if($status == 0){
	$r = $wpdb->get_var("SELECT count(*) FROM tasks WHERE event=$event AND complete=0");
	if($r>0){
		echo "disable";
		return;
	}
}
if($status == 1)
	$status = 0;
else
	$status = 1;
$data = array('status'=>$status, 'updater'=>$_COOKIE['login_name'], 'lastTime'=>date('y-m-d H:i:s'));
$where = array('id'=>$event);
$wpdb->update( 'events', $data, $where );
echo $_COOKIE['login_name']." |&nbsp;&nbsp;".date('Y/m/d');
?>