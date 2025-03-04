<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");

$id = $_GET['id'];
if(empty($id))
	tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
	
global $wpdb;
require_db();
$team = $wpdb->get_row("SELECT * FROM teams WHERE id=$id", ARRAY_A);
if(empty($team))
	tx_die("参数错误<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置 &rsaquo; 创建Team</title>
</head>

<body>
<?php
showMenuBar('setup');
?>

<!--body-->
<div class="tip">	
	<div class="of" onclick="window.location.href='invite.php'"><strong>邀请成员</strong></div>		
	<div class="on" onclick="window.location.href='team.php'"><strong>创建Team</strong></div>	
	<div class="of" onclick="window.location.href='createChart.php'"><strong>创建Chart</strong></div>
	<div class="of" onclick="window.location.href='myTeams.php'"><strong>我的Team</strong></div>
	<div class="of" onclick="window.location.href='memberInfo.php'"><strong><?php if($_COOKIE['login_isAdmin'] == 2)echo '个人信息';else echo '管理员';?></strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>			
</div>
<div class="container" style="border-top:none;">	

  <form id="team_form" action="addTeam.php?id=<?php echo $id?>" method="post">
	<div class="title"><strong>修改Team</strong></div>
	<div class="hr"></div>
	<p>
	<div><div class="item">Team名称：</div><input type="text" name="name" class="inputText" value="<?php echo $team['name']?>" style="width:600px"/></div>
	<p>
	<div><div class="item">说明：</div><textarea name="description" id="description"><?php echo $team['description']?></textarea></div><div style="clear:both"></div>
	<p>
	<div class="item">Team成员：</div>
	<div id="members">
<?php
global $wpdb;
require_db();

$membersInTeam = $wpdb->get_col("SELECT member_id FROM members_team WHERE team_id=$id");

$members = $wpdb->get_results("SELECT * FROM members WHERE valid=2 ORDER BY email", ARRAY_A);
if(is_array($members)){
	foreach($members as $member){		
		echo "<input type='checkbox' name='members[]' value='".$member['id']."' ";
		if(in_array($member['id'],$membersInTeam))
			echo "checked";
		echo "/>";
		echo $member['nickname']." (";
		echo $member['email'].")<br>";	
	}
};
?>	
	</div>
	<div style="clear:both"></div>	
	<div class="item">&nbsp;</div><div class="ck"><input id="ckecker" type="checkbox"/>全选</div>
	<div style="clear:both"></div>	
	<p>
	<br />
	<div><div class="item">&nbsp;</div><input name="next" type="submit" class="bt" value="下一步" />&nbsp;&nbsp;<input type=button value='取消'  class='bt' onclick='history.go(-1)'/></div>
  </form>
  
  <div style="clear:both"></div>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>

<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/setup/team.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/setup.css" />
<style>
.item{
	padding-right:0px;
	width:140px;
}
#description{
	width:600px;
	height:60px;
}
</style>