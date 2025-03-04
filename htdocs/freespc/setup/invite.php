<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置 &rsaquo; 邀请成员</title>
</head>

<body>
<?php
showMenuBar('setup');
?>

<!--body-->
<div class="tip">	
	<div class="on"><strong>邀请成员</strong></div>		
	<div class="of" onclick="window.location.href='team.php'"><strong>创建Team</strong></div>	
	<div class="of" onclick="window.location.href='createChart.php'"><strong>创建Chart</strong></div>
	<div class="of" onclick="window.location.href='myTeams.php'"><strong>我的Team</strong></div>		
	<div class="of" onclick="window.location.href='memberInfo.php'"><strong><?php if($_COOKIE['login_isAdmin'] == 2)echo '个人信息';else echo '管理员';?></strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>				
</div>
<div class="container" style="border-top:none;">	
	<div class="title"><strong>通过Email邀请成员加入SPC项目</strong></div>
	<div class="hr"></div>
	<div id="invite_tip"><a href='members.php'>查看所有成员</a></div>
	<div style="float:left; margin-top:25px;width:850px">

	<form action="invite_handle.php" method="post" id="invite_form">
	<div><div class="item">Email：</div>
	<textarea name="emails" id="emails"><?php
if(!empty($_GET["resend"])){
	global $wpdb;
	require_db();

	$emails = $_POST['emails'];
	$emailList = explode('|',$emails);
	foreach($emailList as $memberId){
		if(!empty($memberId)){
			$email = $wpdb->get_var("SELECT email FROM members WHERE id=$memberId");
			echo $email;
			echo " \n";
		}
	}
}
?></textarea>
	<div id="emails_error_tip" class="error_tip"><img src="../img/warning.gif" />请填写要邀请的Email地址</div>
	<div class="email_tip">一次填写多个Email地址请用回车换行分隔</div>
	</div>
	<div style="clear:both"></div>
	<p>
	<div><div class="item">邮件内容：</div><textarea name="content" id="content">我们公司最近开始实施SPC了，邀请您加入SPC项目团队。一起为我们的品质加油吧！</textarea>
	<div id="content_error_tip" class="error_tip"><img src="../img/warning.gif" />请填写邀请内容</div>
	</div>
	<div style="clear:both"></div>
	<p>
	<div><div class="item">&nbsp;</div><input name="invite" id="invite_bt" type="button" class="bt" value="发出邀请"/></div>
	</form>
	</div>
	<div style="clear:both"></div>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>

<!--<script type="text/javascript" src="<?php //echo $ABS_PATH?>jui/util/connection-min.js"></script>-->
<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/setup/invite.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/setup.css" />
<style>
.error_tip{
	margin-left:15px;
	margin-bottom:15px;
	width:300px;
	padding:5px;
}
</style>
