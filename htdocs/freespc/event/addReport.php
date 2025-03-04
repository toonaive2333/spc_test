<?php
require_once('../load.php');

$event = $_POST['event'];
$title = $_POST['title'];
$content =  $_POST['content'];
$content = str_replace("\r\n","<br>",$content); 
$content = str_replace(" ","&nbsp;",$content); 

$data = array( 'title'=>$title, 'event'=>$event, 'content'=>$content, 'updater'=>$_COOKIE['login_name'], 'lastTime'=>date('y-m-d H:i:s') );
global $wpdb;
require_db();
$wpdb->insert( 'reports', $data );
$data = array('updater'=>$_COOKIE['login_name'], 'lastTime'=>date('y-m-d H:i:s'));
$where = array('id'=>$event);
$wpdb->update( 'events', $data, $where );
echo 1;
?>