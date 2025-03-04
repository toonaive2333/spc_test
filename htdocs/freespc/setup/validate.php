<?php
ob_start();
require_once( '../load.php' );

function showError(){
	tx_die("请确认您输入的网址是否与您邮件中的相同，如果您收到多份邀请邮件，请以最后的那份为准。<br><br>如果您始终无法激活账号，请与".COMPANY."SPC项目管理员联系。<br><br>","激活账号出现错误");
}

$email =  trim($_GET['e']);
$validCode = trim($_GET['c']);

if( empty($email) || empty($validCode) )
	showError();

require_db();

$valid = $wpdb->get_var("SELECT valid FROM members WHERE email='$email' AND validCode='$validCode'");
if( empty($valid) )
	showError();
if( $valid == 2 )
	tx_die("您的账号已经激活过，请不要重复操作。<br><br><br><br><form action='$ABS_PATH'><input type=submit value='返回'  class='bt'/>","激活账号出现错误");
if( $valid == 1 ){
	$wpdb->update('members', array( 'valid'=>2 ),array('email'=>$email));
	$memberInfo = $wpdb->get_row("SELECT * FROM members WHERE email='$email'",ARRAY_A);		
	setcookie('login_isAdmin',2,time()+3600);
	setcookie('login_id',$memberInfo['id'],time()+3600);
	setcookie('login_email',$memberInfo['email'],time()+3600);
	setcookie('login_name',$memberInfo['nickname'],time()+3600);

	tx_die("您的账号已激活，以下是您的初始账号信息，请及时更改：<br><br>账号：$email<br>密码：<strong style='color:#FF6600'>".$memberInfo['password']."</strong><br>名字：".$memberInfo['nickname']."<br><br><br><form action='".$ABS_PATH."setup/memberInfo.php' method=post><input type=submit value='修改账号'  class='bt'/><br><br>","成功激活账号","激活成功");
}
?>