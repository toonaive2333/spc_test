<?php
ob_start();
require_once( 'load.php' );

if(isset($_POST['isAdmin'])){
	$isAdmin = $_POST['isAdmin'];
	$email = strtolower(trim($_POST['email']));
	$password = trim($_POST['password']);
	(empty($email) || empty($password)) && tx_die("请正确填写登录Email及密码<br><br><br><br><form action='' method='post'><input type=submit value='返回'  class='bt'/></form>");
	
	if($isAdmin == 1){
		if(!($email == EMAIL && $password == EMAIL_PSW))
			tx_die("请正确填写登录Email及密码<br><br><br><br><form action='' method='post'><input type=submit value='返回'  class='bt'/></form>");		
		setcookie('login_isAdmin',1,time()+3600*24);
		setcookie('login_id',0,time()+3600*24);
		setcookie('login_email','0',time()+3600*24);
		setcookie('login_name','管理员',time()+3600*24);
	}else if($isAdmin == 0){
		global $wpdb;
		require_db();
	
		$memberInfo = $wpdb->get_row("SELECT * FROM members WHERE email='$email' AND password='$password'",ARRAY_A);
		if( empty($memberInfo['valid']) )
			tx_die("请正确填写登录Email及密码<br><br><br><br><form action='$ABS_PATH'><input type=submit value='返回'  class='bt'/>");
		if( $memberInfo['valid']==1 )
			tx_die("您的账号还没有激活<br><br><br><br><form action='$ABS_PATH'><input type=submit value='返回'  class='bt'/>");
			
		setcookie('login_isAdmin',2,time()+3600*24);
		setcookie('login_id',$memberInfo['id'],time()+3600*24);
		setcookie('login_email',$memberInfo['email'],time()+3600*24);
		setcookie('login_name',$memberInfo['nickname'],time()+3600*24);
	}
	redirect($ABS_PATH);
}else{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登录</title>
<link rel="stylesheet" type="text/css" href="css/util.css" />
<style type="text/css">
.logo{
	margin:auto;
	width:700px;
	padding-top:40px;
	padding-bottom:10px;
}
#container{
	width:660px;
}
.form{	
	margin:0px auto;
	width:600px;
	padding:20px;
}
.form ul{
	list-style-type:none;
}
.form li{
	height:35px;
}
.item{
	width:150px;
}
</style>
</head>

<body>
<div class="logo"><img src="img/logo.JPG" /></div>
<div class="container" id="container">
	<div class="t1">请登录</div>
	<div class="hr"></div>
	<div class="form">
		<form method="post" action="">
		<ul>
		<li><div class="item">登录方式：</div><input type="radio" name="isAdmin" value="0" checked="checked"/>普通 <input type="radio" name="isAdmin" value="1" />管理员</li>
		<p>
		<li><div class="item">登录Email：</div><input name="email" type="text" class="inputText"/></li>
		<p>
		<li><div class="item">密 码：</div><input type="password" name="password" class="inputText"/></li>
		<p>
		<li><div class="item">&nbsp;</div><input name="Submit" type="submit" class="bt" value="登录" /></li>
		</ul>
		</form>
	</div>
</div>
<?php 
}
?>
</body>
</html>
