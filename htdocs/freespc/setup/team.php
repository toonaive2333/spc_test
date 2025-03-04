<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");

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

  <form id="team_form" action="addTeam.php?action=new" method="post" id='team_form'>
	<div class="title"><strong>创建新Team</strong></div>
	<div class="hr"></div>
	<p>
	<div style="float:left"><div class="item">Team名称：</div><input type="text" id="name" name="name" class="inputText" style="width:600px"/></div><div id="team_error_tip" class="error_tip"><img src="../img/warning.gif" />请填写Team名称</div>	
	<div style="clear:both"></div>	
	<p>
	<div style="float:left"><div class="item">描述：</div><textarea name="description" id="description"></textarea></div><div id="description_error_tip" class="error_tip"><img src="../img/warning.gif" />请填写描述</div>
	<div style="clear:both"></div>	
	<p>
	<div class="item">Team成员：</div>
	<div id="members">
<?php
global $wpdb;
require_db(); 

$members = $wpdb->get_results("SELECT nickname,email,valid,id FROM members ORDER BY valid DESC, email", ARRAY_A);
if(is_array($members)){
	foreach($members as $member){
		if($member['valid'] == 2){
			echo "<input type='checkbox' name='members[]' value='".$member['id']."'/>";
			echo $member['nickname']." (";
			echo $member['email'].")<br>";
		}else if($member['valid'] == 1){
			echo "<input type='checkbox' name='members[]' disabled='disabled'/>";
			echo "<span>";
			echo $member['nickname']." (";
			echo $member['email'].")<br>";
			echo "</span>";
		}
		
	}
}else{
	echo "<span style='font-size:12px'>还没有激活成员，请<a href='invite.php'>邀请成员</a>并要求其尽快激活。</span>";
}
?>	
	</div>
	<div style="clear:both"></div>	
	<div class="item">&nbsp;</div><div class="ck"><input id="ckecker" type="checkbox"/>全选</div>
	<div style="clear:both"></div>	
	<p>
	<br />
	<div><div class="item">&nbsp;</div><input id="send_bt" type="button" class="bt" value="下一步" /></div>
  </form>

  <div style="clear:both;"></div>
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
.error_tip{
	margin-left:15px;
	width:130px;
}
</style>