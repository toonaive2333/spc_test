<?php
$needAuthenticate = true;
require_once('../load.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>事件管理</title>
</head>

<body>
<?php 
require_once('pop.php');
showMenuBar('event');
?>
<script>var result = "";</script>
<!--body-->
<div class="tip">
	<div class="on"><strong>事件管理</strong></div>
	<div class="of" onclick="window.location.href='tasks.php'"><strong>任务</strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>
</div>
<div class="container" style="border-top:none;height:300px;padding-top:5px;">
<div id="addBt" style="width:100px">新增事件</div>
<div style="clear:both;height:10px;"></div>
<div id="eventList"></div>
<?php
global $wpdb;
require_db();
if($_COOKIE['login_isAdmin'] == 2){
	$events = $wpdb->get_results("SELECT * FROM events WHERE status=1  AND (team IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") OR team=0) ORDER BY lastTime DESC LIMIT 10", ARRAY_A);
}else{
	$events = $wpdb->get_results("SELECT * FROM events WHERE status=1 ORDER BY lastTime DESC LIMIT 10", ARRAY_A);
}
if(is_array($events)){
echo "<div class='label'><strong>最近结案事件</strong></div>";
echo "<div class='hr'></div>";
echo "<div class='event_list'>";
	$count = 0;
	foreach($events as $event){
		echo "<div><a href='event.php?id=".$event['id']."'>".$event['title']."</a>";
		echo "<span class='updater_tip'>&nbsp;&nbsp;".$event['updater']." | ".justDate($event['lastTime'])."更新</span></div>";
		$count++;
	}
	if($count > 10){
		echo "<div class='allClosed'><a href='closedEvents.php'>>> 所有结案事件...</a></div>";
	}
echo "</div>";

}
?>
<br />
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>
<script type="text/javascript" src="../jui/util/connection-min.js"></script>
<script type="text/javascript" src="../jui/event/events.js"></script>
<link rel="stylesheet" type="text/css" href="../css/event.css"/>