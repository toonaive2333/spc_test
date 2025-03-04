<?php
require_once('../load.php');

$id = $_POST['id'];
$completed = $_POST['completed'];

global $wpdb;
require_db();
$data = array('complete'=>$completed, 'updater'=>$_COOKIE['login_name'], 'uEmail'=>$_COOKIE['login_email'], 'lastTime'=>date('y-m-d H:i:s'));
$where = array('id'=>$id);
$wpdb->update( 'tasks', $data, $where );
echo $_COOKIE['login_name']."于".date('Y/m/d H:i:s')."完成";
?>