<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置 &rsaquo; 成员列表</title></head>

<body>
<?php
showMenuBar('setup');
?>

<!--body-->
<div class="tip">	
	<div class="on" onclick="window.location.href='invite.php'"><strong>邀请成员</strong></div>		
	<div class="of" onclick="window.location.href='team.php'"><strong>创建Team</strong></div>	
	<div class="of" onclick="window.location.href='createChart.php'"><strong>创建Chart</strong></div>
	<div class="of" onclick="window.location.href='myTeams.php'"><strong>我的Team</strong></div>	
	<div class="of" onclick="window.location.href='memberInfo.php'"><strong><?php if($_COOKIE['login_isAdmin'] == 2)echo '个人信息';else echo '管理员';?></strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>				
</div>
<div class="container" style="border-top:none;">	
	<div class="title"><strong>所有成员</strong></div>
	<div class="hr"></div>
	<div id="valid_members">
<?php
global $wpdb;
require_db();

$members = $wpdb->get_results("SELECT * FROM members WHERE valid=2 ORDER BY email", ARRAY_A);
if(is_array($members)){
	foreach($members as $member){
		echo $member['nickname']." (";
		echo $member['email'].")<br>";		
	}
}
?>	
	</div>
	<div id="invalid_members">
	<form id="team_form" action="members_handle.php" method="post">	
	<strong>以下成员尚未激活:</strong><br />	
<?php
$members = $wpdb->get_results("SELECT * FROM members WHERE valid=1 ORDER BY email", ARRAY_A);
if(is_array($members)){
	foreach($members as $member){
		echo "<input type='checkbox' name='members[]' value='".$member['id']."'/>";
		echo "<span>";
		echo $member['email']."<br>";
		echo "</span>";				
	}
}
?>	
	<div class="ck" style="padding:0px"><input id="ckecker" type="checkbox"/>全选</div><br/><br/>
	<input type=submit value='重发邀请'  class='bt_2' style="width:70px;" name="resend"/>&nbsp;&nbsp;<input type=submit value='删除'  class='bt_2' name="delete"/>
	</form>
	</div>	
<div class="hr"></div>
<br /><form action='invite.php' method='post'><input type=submit value='返回'  class='bt'/></form>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>
<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/setup/team.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/setup.css" />
