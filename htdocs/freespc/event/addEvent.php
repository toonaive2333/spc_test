<?php
require_once('../load.php');

$title = trim($_POST['title']);
$description = $_POST['description'];
$source = $_POST['source'];
$team = $_POST['team'];
$source = str_replace("\n", " ", $source);
$source = str_replace("\r", " ", $source);
$source = str_replace("@@@", "&", $source);
global $wpdb;
require_db();
$data = array( 'title'=>$title, 'team'=>$team, 'description'=>$description, 'source'=>$source, 'creator'=>$_COOKIE['login_name'], 'createTime'=>date('y-m-d H:i:s'), 'updater'=>$_COOKIE['login_name'], 'lastTime'=>date('y-m-d H:i:s') );
$wpdb->insert( 'events', $data );
echo((int)$wpdb->insert_id);
?>