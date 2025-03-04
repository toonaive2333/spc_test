<?php 
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect("memberInfo.php");
	
function displayHeader(){
	global $ABS_PATH;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FreeSPC &rsaquo;  修改信息</title>
</head>
<!--body-->
<body>
<?php
	showMenuBar('setup');
?>
<div class="tip">	
<?php if($_COOKIE['login_isAdmin'] == 1){?>
	<div class="of" onclick="window.location.href='invite.php'"><strong>邀请成员</strong></div>		
	<div class="of" onclick="window.location.href='team.php'"><strong>创建Team</strong></div>		
<?php }?>		
	<div class="of" onclick="window.location.href='createChart.php'"><strong>创建Chart</strong></div>
	<div class="of" onclick="window.location.href='myTeams.php'"><strong>我的Team</strong></div>	
	<div class="on"><strong>管理员</strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>				
</div>
<?php
}
//--------end of function displayHeader
	
if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;
switch($step){
	case 0:
	displayHeader();
?>
<div class="container" style="border-top:none; padding-bottom:50px;">	
	<div class="title"><strong>修改组织名称、管理邮箱等信息</strong></div>
	<div class="hr"></div>
	<br>
	<form action="adminInfo.php?step=1" method="post">
	<ul>
		<li><div class="item">公司或组织名称：</div><input type="text" name="company" class="inputText" value="<?php echo COMPANY;?>"></li>
		<p>
		<li><div class="item">Email服务器：</div><input name="smtp" type="text" class="inputText" value="<?php echo SMTP;?>"/></li>
		<p>
		<li><div class="item">端 口：</div><input name="port" type="text" class="inputText" value="25"/></li>
		<p>
		<li><div class="item">Email地址：</div><input type="text" name="email" class="inputText" value="<?php echo EMAIL;?>"/></li>
		<p>
		<li><div class="item">Email密码：</div><input type="password" name="email_psw" class="inputText"/></li>
		<p>
		<li><div class="item">&nbsp;</div><input id="send_bt" type="submit" class="bt" value="修改" />&nbsp;<input name="" type="reset"  class="bt"/></li>
	</ul>
	</form>
</div>
<!--end of body-->
<?php
	break;
	case 1:
	$company  = trim($_POST['company']);
	$smtp  = trim($_POST['smtp']);
	$port  = trim($_POST['port']);
	$email  = trim($_POST['email']);
	$emailPsw  = trim($_POST['email_psw']);
	
	if(empty($company) || empty($smtp) || empty($port) || empty($email) || empty($emailPsw))
		tx_die("所有配置项都必须填写完整<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
	if( strpos($email,'@') == '' )
		tx_die("请正确填写邮箱地址<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
	$exploder = explode('@',$email);
	$emailUname = $exploder[0];
	
	require_once('../includes/sendmail.php');
	configMailIni($smtp,$port,$emailUname,$emailPsw);
	if(!testMail($email)){
		tx_die("您可能没有正确填写设置项，请返回确认<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
	}else{			
		displayHeader();
	}
?>
<div id="container" class="container">	
<form method="post" action="adminInfo.php?step=2">
<div class="t1">测试邮件已发送</div>
<div class="hr"></div>
<div style="padding-left:20px; margin:10px;">
  <input name="company" type="hidden" value="<?php echo $company?>"/>
   <input name="smtp" type="hidden" value="<?php echo $smtp?>"/>
  <input name="email" type="hidden" value="<?php echo $email?>"/>
  <input name="email_psw" type="hidden" value="<?php echo $emailPsw?>"/>
  <br/>依照您的配置，一封邮件已经发送到您的邮箱<code><?php echo $email?></code>，如果配置完全正确，您将会在几分钟内收到该邮件。请及时确认您的新邮件，该邮件可能在您的垃圾邮件中。<br/><br/>如果您始终未收到该邮件，请返回上一步重新设置：<br/><br/><input type=button value='返回'  class='bt' onclick='history.go(-1)'/><br/><br/><br/>如果收到该邮件，请点击完成所有设置：<br/><br/><input type=submit value='完成'  class='bt'/>
</div>
</form>
</div>
<?php		
	break;
	
	case 2:	
	$company  = trim($_POST['company']);
	$smtp  = trim($_POST['smtp']);
	$email  = trim($_POST['email']);
	$emailPsw  = trim($_POST['email_psw']);
	
	//邮件配置项写入config.php
	$configCacheFile = file('../config_cache.php');
	$handle = fopen('../config.php', 'w');
	foreach ($configCacheFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('COMPANY'":
				fwrite($handle, str_replace("companynamehere", $company, $line));
				break;
			case "define('SMTP', '":
				fwrite($handle, str_replace("smtphere", $smtp, $line));
				break;
			case "define('EMAIL', ":
				fwrite($handle, str_replace("emailhere", $email, $line));
				break;
			case "define('EMAIL_PS":
				fwrite($handle, str_replace("emailpasswordhere", $emailPsw, $line));
				break;
			default:
				fwrite($handle, $line);
		}
	}
	fclose($handle);
	chmod('../config.php', 0666);
	
	tx_die("修改成功<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-2)'/>",'修改成功',"操作完成");
	break;
}
showBottom();
?>
</body>
</html>

<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/setup.css" />
<style type="text/css">
ul{
	list-style-type:none;
}
li{
	height:35px;
}
.item{
	width:150px;
}
</style>
