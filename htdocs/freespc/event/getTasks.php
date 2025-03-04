<?php
require_once('../load.php');
global $wpdb;
require_db();
$today = strtotime(date("Y-m-d H:i:s"));
//my tasks
if($_COOKIE['login_isAdmin'] == 2){
	$tasks1 = $wpdb->get_results("SELECT tasks.* FROM tasks WHERE complete=0 AND expiration>0 AND rEmail='".$_COOKIE['login_email']."' ORDER BY expiration ASC ", ARRAY_A);
	$tasks2 = $wpdb->get_results("SELECT tasks.* FROM tasks WHERE complete=0 AND expiration IS NULL AND rEmail='".$_COOKIE['login_email']."' ORDER BY lastTime ASC ", ARRAY_A);
}else{
	$tasks1 = $wpdb->get_results("SELECT tasks.* FROM tasks WHERE complete=0 AND expiration>0 ORDER BY expiration ASC ", ARRAY_A);
	$tasks2 = $wpdb->get_results("SELECT tasks.* FROM tasks WHERE complete=0 AND expiration IS NULL ORDER BY lastTime ASC ", ARRAY_A);	
}
if(is_array($tasks1)){
	$tasks = $tasks1;
	if(is_array($tasks2))
		$tasks = array_merge($tasks,$tasks2);
}else{
	$tasks = $tasks2;
}

if($_COOKIE['login_isAdmin'] == 2){
	echo "<div class='label'><strong>我的任务</strong></div>";
}else{
	echo "<div class='label'><strong>所有任务</strong></div>";
}
echo "<div class='hr'></div>";
if(is_array($tasks)){
	echo "<div class='task_list'>";
	foreach($tasks as $task){
		echo "<div><a href='javascript:void(0);' onclick=\"show(".$task['id'].",'task');\">".$task['title']."</a>&nbsp;";
		echo getExpiration($task['expiration']);
		echo "</div>";
	}
}else{
	echo "<img src='../img/ok.gif'/>没有未完成的任务<br>";
}
echo "<span class='allClosed'><a href='completedTasks.php'>>> 已完成的任务...</a></span>";
echo "</div>";
echo "<br><br>";
//public tasks
$tasks1 = NULL;
$tasks2 = NULL;
$tasks = NULL;
if($_COOKIE['login_isAdmin'] == 2){
	$tasks1 = $wpdb->get_results("SELECT * FROM tasks WHERE complete=0 AND expiration>0 AND rEmail='all' AND (team IN(SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") OR team=0 ) ORDER BY expiration ASC ", ARRAY_A);
	$tasks2 = $wpdb->get_results("SELECT * FROM tasks WHERE complete=0 AND expiration IS NULL AND rEmail='all' AND (team IN(SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") OR team=0 ) ORDER BY expiration ASC ", ARRAY_A);
}else{
	return;
}
if(is_array($tasks1)){
	$tasks = $tasks1;
	if(is_array($tasks2))
		$tasks = array_merge($tasks,$tasks2);
}else{
	$tasks = $tasks2;
}
echo "<div class='label'><strong>公共任务</strong></div>";
echo "<div class='hr'></div>";
echo "<div class='task_list'>";	
if(is_array($tasks)){
	foreach($tasks as $task){
		echo "<div><a href='javascript:void(0);' onclick=\"show(".$task['id'].",'task');\">".$task['title']."</a>&nbsp;";
		echo getExpiration($task['expiration']);
		echo "</div>";
	}
}else{
	echo "<img src='../img/ok.gif'/>没有未完成的任务<br>";
}
echo "<span class='allClosed'><a href='completedPublicTasks.php'>>> 已完成的公共任务...</a></span>";
echo "</div>";
//get expiration
function getExpiration($time){
	global $today;
	if(!$time)
		return "<span class='green'>请尽快处理</span>";;
	
	$time = strtotime($time)+24*3600;
	if($today >= $time){
		$expiration = ceil(($today-$time)/24/3600);//num of day
		echo "<span class='red'>滞后".$expiration."天</span>";
	}else{
		$expiration = ceil(($time-$today)/24/3600);
		if($expiration == 1)
			echo "<span class='green'>今天内处理</span>";
		else
			echo "<span class='green'>".$expiration."天内处理</span>";
	}
}
?>
