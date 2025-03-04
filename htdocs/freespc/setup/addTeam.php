<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");
require_once('../includes/sendmail.php');

$name = trim($_POST['name']);
$description = trim($_POST['description']);
$members = $_POST['members'];
$action = $_GET['action'];
$team_id = $_GET['id'];

if(empty($name) || empty($description))
	tx_die("输入不能为空，请返回确认<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");

global $wpdb;
require_db();

if($action == 'new'){
	//新建
	
	//是否重名
	existName();
	//增加到teams表
	$data = array( 'name'=>$name, 'description'=>$description, 'members'=>count($members) );
	$wpdb->insert( 'teams', $data );
	$new_team_id = (int) $wpdb->insert_id;
	if(is_array($members)){
		foreach($members as $member_id){
			$data = array( 'member_id'=>$member_id, 'team_id'=>$new_team_id );
			$wpdb->insert( 'members_team', $data );
		}		
	}
	tx_die("创建Team成功，您现在可以为该Team<a href='createChart.php?team_id=$new_team_id'>创建Chart</a><br><br><br><br><form action='team.php' method='post'><input type=submit value='返回'  class='bt'/></form>","创建Team成功","操作完成");
	
}else if(!empty($team_id)){
	//修改
	$oldName = $wpdb->get_var("SELECT name FROM teams WHERE id=$team_id");
	if(empty($oldName))
		tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
	if($oldName != $name)
		existName();
		
	$data = array( 'name'=>$name, 'description'=>$description, 'members'=>count($members) );
	$where = array( 'id'=>$team_id );
	$wpdb->update( 'teams', $data , $where);
	
	$wpdb->query("DELETE FROM members_team WHERE team_id=$team_id");
	foreach($members as $member_id){
		$data = array( 'member_id'=>$member_id, 'team_id'=>$team_id );
		$wpdb->insert( 'members_team', $data );
	}		
	tx_die("修改Team成功<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>","修改Team完成","操作完成");	
}

function existName(){
	global $wpdb;
	global $name;
	$id = $wpdb->get_var("SELECT id FROM teams WHERE name='$name'");
	if(!empty($id))
		tx_die("你输入的Team名称已经被使用，请返回重新输入<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
}
?>