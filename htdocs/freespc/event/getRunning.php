<?php
require_once('../load.php');
global $wpdb;
require_db();
if($_COOKIE['login_isAdmin'] == 2){
	$events = $wpdb->get_results("SELECT * FROM events WHERE status=0  AND (team IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") OR team=0)ORDER BY lastTime DESC", ARRAY_A);
}else{
	$events = $wpdb->get_results("SELECT * FROM events WHERE status=0 ORDER BY lastTime DESC", ARRAY_A);
}
if(is_array($events)){
echo "<div class='label'><strong>运行中事件</strong></div>";
echo "<div class='hr'></div>";
echo "<div class='event_list'>";
	foreach($events as $event){
		echo "<div><a href='event.php?id=".$event['id']."'>".$event['title']."</a>";
		echo "<span class='updater_tip'>&nbsp;&nbsp;".$event['updater']." | ".justDate($event['lastTime'])."更新</span></div>";
	}
echo "</div><br /><br />";
}
?>

