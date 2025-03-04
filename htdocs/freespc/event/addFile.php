<?php
require_once('../load.php');

$event = $_POST['event'];
$fileName = $_POST['fileName'];
$linkName = $_POST['linkName'];
$content =  $_POST['content'];

$data = array( 'fileName'=>$fileName, 'linkName'=>$linkName, 'event'=>$event, 'description'=>$content, 'updater'=>$_COOKIE['login_name'], 'lastTime'=>date('y-m-d H:i:s') );
global $wpdb;
require_db();
$wpdb->insert( 'files', $data );
$data = array('updater'=>$_COOKIE['login_name'], 'lastTime'=>date('y-m-d H:i:s'));
$where = array('id'=>$event);
$wpdb->update( 'events', $data, $where );
echo 1;
?>