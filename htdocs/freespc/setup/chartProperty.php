<?php
$needAuthenticate = true;
require_once('../load.php');

global $wpdb;
require_db();
$id = $_GET['id'];
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置 &rsaquo; 我的Team</title>
</head>

<body>
<?php showMenuBar('setup');?>
<!--body-->
<div class="tip">
<?php if($_COOKIE['login_isAdmin'] == 1){?>
	<div class="of" onclick="window.location.href='invite.php'"><strong>邀请成员</strong></div>		
	<div class="of" onclick="window.location.href='team.php'"><strong>创建Team</strong></div>
<?php }?>
	<div class="of" onclick="window.location.href='createChart.php'"><strong>创建Chart</strong></div>
	<div class="on" onclick="window.location.href='myTeams.php'"><strong>我的Team</strong></div>	
	<div class="of" onclick="window.location.href='memberInfo.php'"><strong><?php if($_COOKIE['login_isAdmin'] == 2)echo '个人信息';else echo '管理员';?></strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>			
</div>
<div class="container" style="border-top:none;">
<div id="current" class="title">我的Team</div><img id="arrow" src="../img/arrow_down.gif" />&nbsp;&nbsp;<span id="refresh" title="刷新"><img src="../img/refresh.gif" /></span>
<div id="charts_menu" style="visibility:hidden;">
<?php
$teams = "";
if($_COOKIE['login_isAdmin'] == 2)
	$teams = $wpdb->get_results("SELECT * FROM teams WHERE id IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") ORDER BY CONVERT(name USING gbk)", ARRAY_A);
else
	$teams = $wpdb->get_results("SELECT * FROM teams ORDER BY CONVERT(name USING gbk)", ARRAY_A);
if(is_array($teams)){
	foreach($teams as $team){
		echo "<div class='menu_title' onclick='showChartList(".$team['id'].")'>".$team['name']."</div><div class='new_chart'>&nbsp;<a href='createChart.php?team_id=".$team['id']."'>新建Chart</a></div>";
		echo "<ul id='charts_".$team['id']."'>";
			$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=".$team['id']." ORDER BY CONVERT(name USING gbk)", ARRAY_A);
			if(is_array($charts)){
				foreach($charts as $chart){
					echo "<li>";
					echo "<a href=# onclick='showChart(".$chart['id'].",\"".$chart['name']."\")'>".$chart['name']."</a>";
					echo "</li>";
				}
			}
		echo "</ul>";
	}
}	
?>  
</div>
<div>
<iframe frameborder="0" height="600px" id="chartProperty_window" name="chartProperty_window"></iframe>
</div>
</div>

<!--end of body-->
<?php showBottom();?>
</body>
</html>

<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/setup/chartProperty.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/setup.css" />
<?php
if(!empty($id)){
	$name = $wpdb->get_var("SELECT name FROM charts WHERE id=$id");
	if(!empty($name)){
		echo "<script>showChart($id);</script>";
	}
}else{
	echo "<script>popMenu();</script>";
}
?>