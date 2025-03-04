<?php
$needAuthenticate = true;
require_once('../load.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>事件管理 > 已结案的事件</title>
</head>

<body>
<?php 
require_once('pop.php');
showMenuBar('event');
?>
<script>var result = "";</script>
<!--body-->
<div class="tip">
	<div class="on" onclick="window.location.href='index.php'"><strong>事件管理</strong></div>
	<div class="of" onclick="window.location.href='tasks.php'"><strong>任务</strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>
</div>
<div class="container" style="border-top:none;height:300px;padding-top:5px;">
<div class="path" style="width:160px"><a href="javascript:history.go(-1)">事件管理</a> > 已结案事件</div>
<div class='hr'></div>
<?php
global $wpdb;
require_db();
//pager
$pageSize = 20;
if($_GET['p']){
	$current = $_GET['p'];
}else{
	$current = 1;
}
if($_GET['t']){
	$total = $_GET['t'];
}else{
	if($_COOKIE['login_isAdmin'] == 2){
		$total = $wpdb->get_var("SELECT count(*) FROM events WHERE status=1  AND (team IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") OR team=0)  ORDER BY lastTime DESC");
	}else{
		$total = $wpdb->get_var("SELECT count(*) FROM events WHERE status=1 ORDER BY lastTime DESC");
	}
}
$totalPage = floor(($total-1)/$pageSize)+1;
$first = $current-5;
if($first<1) $first=1;
$last = $current+5;
if($last>$totalPage) $last=$totalPage;

function pager(){
	global $totalPage,$first,$last,$current,$total;
	if($first>1){
		echo("<a class=pager href=?p=1&t=$total>");
		echo("1..");
		echo("</a>");
	}
	if($current>1){
		echo("<img src='../img/prev.jpg'/>");
		echo("<a class=pager href=?p=".($current-1)."&t=$total>");
		echo("上一页");
		echo("</a>");
		echo("&nbsp;");
	}
	if($last > 1){
		for($i=$first;$i<=$last;$i++){
			if($i==$current){
				echo("<span style='color:#777777'><b>".$i."</b></span>");
			}else{
				echo("<a class=pager href=?p=".$i."&t=$total>");
				echo($i);
				echo("</a>");
			}
			if($i<$last){
				echo("&nbsp;|&nbsp;");
			}
		}
	}
	if($current<$totalPage){
		echo("&nbsp;");
		echo("<a class=pager href=?p=".($current+1)."&t=$total>");
		echo("下一页");
		echo("</a>");
		echo("<img src='../img/next.jpg'/>");
	}
	
	if($last<$totalPage){
		echo("&nbsp;");
		echo("<a class=pager href=?p=".$totalPage."&t=$total>");
		echo("..".$totalPage);
		echo("</a>");
	}
}

//
$start = $pageSize*($current-1);
if($_COOKIE['login_isAdmin'] == 2){
	$events = $wpdb->get_results("SELECT * FROM events WHERE status=1  AND (team IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") OR team=0) ORDER BY lastTime DESC Limit $start,$pageSize", ARRAY_A);
}else{
	$events = $wpdb->get_results("SELECT * FROM events WHERE status=1 ORDER BY lastTime DESC Limit $start,$pageSize", ARRAY_A);
}

if(is_array($events)){
echo "<div class='event_list'>";
	foreach($events as $event){
		echo "<a href='event.php?a=c&id=".$event['id']."'>".$event['title']."</a>";
		echo "<span class='updater_tip'>&nbsp;&nbsp;".$event['updater']." | ".justDate($event['lastTime'])."更新</span>";
		echo "<br>";
	}
echo "</div>";
}
?>
<br />
<div class="pager">
<?php pager();?>
</div>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>
<script type="text/javascript" src="../jui/util/connection-min.js"></script>
<script type="text/javascript" src="../jui/event/events.js"></script>
<link rel="stylesheet" type="text/css" href="../css/event.css"/>