<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");
require_once('../includes/sendmail.php');
$validateUrl = "http://".$_SERVER[HTTP_HOST]."/freespc/setup/validate.php?";
$sendMails = array();
$validMails = array();

$allSuccess = 1;
$emails = strtolower(trim($_POST['emails']));
$content = trim($_POST['content']);

if(empty($emails) || empty($content))
	tx_die("输入不能为空，请返回确认<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");

$emails = preg_replace('/\s(?=\s)/', '', $emails);
$emails = preg_replace('/[\s]/', '#', $emails);
$emailList = explode('#',$emails);

$title = "邀请您加入".COMPANY."的SPC项目团队";

//$content = toHtml($content);
$content .= "\n\n";
$content .= "要接受此邀请并激活您的帐户，请访问 ";

global $wpdb;
require_db();

foreach($emailList as $email){
	$pattern = "^([A-Za-z0-9\.|-|_]{1,60})([@])";
	$pattern .="([A-Za-z0-9\.|-|_]{1,60})(\.)([A-Za-z]{2,3})$";
	if(!ereg($pattern,$email)){
		$allSuccess = 0;
	}else{		
		//如果该email已激活，则跳过；如果邀请过但未激活，则重发
		$valid = $wpdb->get_var("SELECT valid FROM members WHERE email='$email'");
		if( $valid == 2 ){
			$validMails[] = $email;
			continue;
		}
		
		if( $valid == 1 )
			$wpdb->query("DELETE FROM members WHERE email='$email'");
		
		$validCode = getRandCode();
		$exploder = explode('@',$email);
		$emailUname = $exploder[0];
		$data = array( 'email'=>$email, 'password'=>$validCode, 'nickname'=>$emailUname, 'validCode'=>$validCode );
		$wpdb->insert( 'members', $data );
		
		$url = $validateUrl;
		$url = $url."e=".$email."&c=".$validCode;

		//发送邀请邮件
		$c = $content;		
		$c .= "$url";
		$c .= "\n";
		$c .= "（如果上面的网址无法通过点击进入，请将该网址复制并粘贴至浏览器的地址栏中）";
		$c .= "\n\n";
		$c .= "---------------------------------------------------------------------------\n";
		$c .= COMPANY;
		
		//$title = mb_convert_encoding($title, 'gb2312', 'utf-8');
		sendmail($email, $title, $c, "");
		$sendMails[] = $email;
		sleep(1);
	}
}

if(count($sendMails)==0 && count($validMails)==0)
	tx_die("您的Email拼写可能存在问题，请返回确认<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
	
$out = "";
if(count($sendMails)>0){
	$out .= "<span style='color:green'><strong>邀请邮件已发送至以下Email:</strong><br></span>";
	foreach($sendMails as $s_email){
		$out .= $s_email."<br>";
	}
}
if(count($validMails)>0){
	$out .= "<span style='color:#FF3300'><strong><br>以下Email已激活，请不要重复邀请:</strong><br></span>";
	foreach($validMails as $v_email){
		$out .= $v_email."<br>";
	}
}

$out .= "<br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>";
tx_die($out,"邀请邮件已发送","操作完成");



function getRandCode(){
	$possible = "0123456789abcdefghijklmnopqrstuvwxyz";
	$code = "";
	while(strlen($code) < 8){
		$code .= substr($possible, (rand() % strlen($possible)), 1);
	}
	return($code); 
}
?>