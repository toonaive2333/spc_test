<?php
require_once('../load.php');

$event = $_POST['event'];
$teamId = $_POST['teamId'];
$task = $_POST['task'];
$expire =  $_POST['expire'];
$responser = $_POST['responser'];
$rEmail = $_POST['rEmail'];
$description = $_POST['description'];
$expiration = NULL;
switch($expire){
	case 'later':
		$expiration = NULL;
	break;
	case 'today':
		$expiration = date("Y/m/d");
	break;
	case 'tomorrow':
		$tomorrow = time()+3600*24;
		$expiration = date("Y/m/d",$tomorrow);
	break;
	case 'thisweek':
		$a = getdate(time());
		$expiration = date("Y/m/d",time()+24*3600*(6-$a['wday']));
	break;
	case 'nextweek':
		$a = getdate(time());
		$expiration = date("Y/m/d",time()+24*3600*(13-$a['wday']));
	break;
	default:
		if(strtotime($expire)){
			$expiration = $expire;
		}
	break;	
}
$data = array( 'title'=>$task, 'event'=>$event, 'team'=>$teamId, 'responser'=>$responser, 'rEmail'=>$rEmail, 'description'=>$description, 'creator'=>$_COOKIE['login_name'], 'cEmail'=>$_COOKIE['login_email'], 'createTime'=>date('y-m-d H:i:s'), 'updater'=>$_COOKIE['login_name'], 'uEmail'=>$_COOKIE['login_email'], 'lastTime'=>date('y-m-d H:i:s') );
if(!$expiration == NULL){
	$data = $data+array( 'expiration'=>$expiration );
}

global $wpdb;
require_db();
$wpdb->insert( 'tasks', $data );
$data = array('updater'=>$_COOKIE['login_name'], 'lastTime'=>date('y-m-d H:i:s'));
$where = array('id'=>$event);
$wpdb->update( 'events', $data, $where );
echo 1;
?>