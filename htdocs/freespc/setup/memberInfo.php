<?php
ob_start();
$needAuthenticate = true;
require_once( '../load.php' );
$_COOKIE['login_isAdmin'] == 1 && redirect("adminInfo.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FreeSPC &rsaquo;  修改资料</title>
</head>
<!--body-->
<body>
<?php	
if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;
	
switch($step){
	case 0:
	showMenuBar('setup');
	
	global $wpdb;
	require_db();
	$email = $wpdb->get_var("SELECT email FROM members WHERE id=".$_COOKIE['login_id']);	
?>
<div class="tip">	
	<div class="of" onclick="window.location.href='createChart.php'"><strong>创建Chart</strong></div>
	<div class="of" onclick="window.location.href='myTeams.php'"><strong>我的Team</strong></div>	
	<div class="on"><strong>个人信息</strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>	
</div>
<div class="container" style="border-top:none; padding-bottom:50px;">	
	<div class="title"><strong>修改您的密码、姓名等信息</strong></div>
	<div class="hr"></div>
	<br>
	<form action="memberInfo.php?step=1" method="post">
	<ul>
		<li><div class="item">账号Email：</div><?php echo $email;?></li>
		<p>
		<li><div class="item">新密码：</div><input type="password" name="password" class="inputText"/></li>
		<p>
		<li><div class="item">重复密码：</div><input type="password" name="password_d" class="inputText"/></li>
		<p>
		<li><div class="item">姓 名：</div><input type="text" name="name" class="inputText" value="<?php echo $_COOKIE['login_name'];?>" maxlength="30"/></li>
		<p>
		<li><div class="item">&nbsp;</div><input id="send_bt" type="submit" class="bt" value="修改" /></li>
	</ul>
	</form>
</div>
<!--end of body-->
<?php
break;
	case 1:
	$name  = trim($_POST['name']);
	$emailPsw  = trim($_POST['password']);
	$emailPsw_d  = trim($_POST['password_d']);
	
	if( $emailPsw !== $emailPsw_d)
		tx_die("两次密码应该一致<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");	
	
	$data = array();
	if($emailPsw)
		$data =  $data+array( 'password' => $emailPsw );
	if($name)
		$data =  $data+array( 'nickname' => $name );
	if(count($data)>0){
		global $wpdb;
		require_db();
		$where =  array( 'id' => $_COOKIE['login_id']);
		$wpdb -> update('members', $data, $where);
		setcookie('login_name',$name);
	}
	
	tx_die("修改成功<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>","修改成功","操作完成");
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