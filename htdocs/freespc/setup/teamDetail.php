<?php
$needAuthenticate = true;
require_once('../load.php');
//$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");

$id = $_GET['id'];
if(empty($id))
	tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置 > 我的Team > Team信息</title>
</head>

<body>
<?php
showMenuBar('setup');
?>

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
<div class="top_tip" style="width:160px"><a href="myTeams.php">我的Team</a> > Team信息</div>
<?php 
global $wpdb;
require_db();

$team = $wpdb->get_row("SELECT * FROM teams WHERE id=$id", ARRAY_A);
$memberCount = $team['members'];
$chartCount = $team['charts'];
?>

<div style="width:500px; float:left">
	<div class="title"><strong><?php echo $team['name']?></strong></div>
	<div style="clear:both" class="description"><?php echo $team['description']?></div>
	<div id="last_time">最后更新：<?php echo $team['lastTime']?></div>
<?php
if($_COOKIE['login_isAdmin'] == 1){
?>
	<div id="edit_delete">
		<a href='teamEdit.php?id=<?php echo $id?>'><img border=0 title='修改' src='<?php echo $ABS_PATH?>img/edit.gif'>修改</a>&nbsp;&nbsp;
		<a href='#' id="delete_bt"><img border=0 title='删除' src='<?php echo $ABS_PATH?>img/delete.gif'>删除</a>
	</div>
	
	<div id="confirm" style="display:none"><div style="float:left"><img border=0 title='删除' src='<?php echo $ABS_PATH?>img/warn.gif'></div><div>删除Team的同时将删除属于该Team的所有Chart，<br />您确定要删除吗？</div><br /><form action='teamDelete.php?id=<?php echo $id?>' method='post'><input type=submit value='确定'  class='bt_2'/>&nbsp;&nbsp;<input type=button id='cancel_bt' value='取消'  class='bt_2' /></form></div>
<?php
}
?>
</div>
<div style="clear:both">
<form action='myTeams.php' method='post'><input type=submit value='返回'  class='bt'/></form>
</div>
<div class="hr" style="margin-bottom:15px;"></div>
<div style="clear:both"></div>
<?php
$members = $wpdb->get_results("SELECT members.* FROM members,members_team WHERE members_team.member_id=members.id AND members_team.team_id=$id", ARRAY_A);
?>
<div>
  <div style="width:350px; float:left">
	<div class="title"><strong>Team成员( <?php echo $memberCount?> )</strong></div>
	<div id="" style="clear:left">
	<?php 
	if(is_array($members)){
		foreach($members as $member){
			echo $member['nickname'];
			echo " (".$member['email'].")";
			echo "<br>";
		}
	}
	?>
	</div>
  </div>
  
  <div style="width:600px; float:right">
	<div class="title"><strong>Charts( <?php echo $chartCount?> )</strong> <a href="createChart.php?team_id=<?php echo $id?>">新建Chart</a></div>
	<div id="chart_list" style="clear:left">
	<?php
		$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=$id", ARRAY_A); 
		if(is_array($charts)){
			foreach($charts as $chart){
				echo "<a href='chartDetail.php?a=c&id=".$chart['id']."'>";
				echo $chart['name'];
				echo "</a><br>";
			}
		}
	?>
	</div>
  </div>
  
</div>
<div style="clear:both"></div>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>

<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/setup/teamDetail.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/setup.css" />