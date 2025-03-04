<?php
//These three defines are required to allow us to use require_wp_db() to load the database class while being wp-content/wp-db.php aware
define('ABSPATH', dirname(dirname(__FILE__)).'/');
$ABS_PATH = '/freespc/';

require_once('../includes/functions.php');

if (!file_exists('../config-sample.php'))
	tx_die('对不起，您可能已将文件<code>config-sample.php</code>删除。<br>请从您下载的压缩包中查找该文件，并复制到<code>X:\\xampp\htdocs\freespc\</code>(X为xampp的安装盘符)目录下，然后刷新本网页。','需要<code>config-sample.php</code>文件');

$configFile = file('../config-sample.php');

if ( !is_writable('../'))
	tx_die("对不起, 目录没有写入权限。<br>请将该目录的权限设置为可读写，或者手动创建<code>config.php</code>文件。");

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

function display_header(){
	global $ABS_PATH;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>FreeSPC &rsaquo; 配置</title>
	<link rel="stylesheet" href="<?php echo $ABS_PATH?>css/util.css" type="text/css"/>
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
		.tip1{
			text-align:center;
			width:550px;
			border:#FFCC00 1px solid;
			background-color:#FFFFCC;
			clear:both;			
			font-size:12px;
			margin:auto;
			padding:5px;
			margin-bottom:10px;
			margin-top:10px;
			line-height:18px;
		}
		code{
			color:#339999;
		}
	</style>
</head>
<body>
<div class="logo"><img src="<?php echo $ABS_PATH?>img/logo.jpg" /></div>
<?php
}//end function display_header();

//if( ini_get('sendmail_path') == '' )
		//tx_die("对不起，您的系统环境尚未设置好，请按照以下步骤进行设置：<br><ul><li>修改php.ini文件：用记事本打开X:\\xampp\apache\bin\php.ini文件(X为xampp的安装盘符)，查找“sendmail_path”，将“;sendmail_path = \"X:\\xampp\sendmail\sendmail.exe -t\"”前的分号“;”去掉，保存该文件。</li><li>重启Apache：打开xampp控制面板(X:\\xampp\\xampp-control.exe)，点击stop/start按钮将Apache停止再启动。<br><br>&nbsp;&nbsp;&nbsp;<img src='".$ABS_PATH."img/xampp_control_panel.jpg'/><br><br></li><li><a href='setup-config.php'>刷新</a>本页面。</li></ul>");
		
switch($step) {	
	case 0:
	display_header();
?>
<div id="container" class="container">
<div class="t1">请设置数据库</div>
<div class="hr"></div>
<div class="tip1">如果您安装xampp后没有修改mysql数据库，请尝试直接点击进入下一步。</div>
<div style="padding-left:100px;">
<form method="post" action="setup-config.php?step=1">
<table>
	<tr>
		<td height="40">数据库名：</td>
		<td><input name="dbname" id="dbname" type="text" size="25" value="xampp" class="inputText"/></td>
		<td> </td>
	</tr>
	<tr>
		<td height="40">数据库用户名：</td>
		<td><input name="uname" id="uname" type="text" size="25" value="root" class="inputText"/></td>
		<td></td>
	</tr>
	<tr>
		<td height="40">数据库密码：</td>
		<td><input name="pwd" id="pwd" type="password" size="25" value="root" class="inputText"/></td>
		<td></td>
	</tr>
	<tr>
		<td height="40">数据库服务器：</td>
		<td><input name="dbhost" id="dbhost" type="text" size="25" value="db" class="inputText"/></td>
		<td></td>
	</tr>
</table>
<p class="step"><input name="submit" type="submit" value="下一步" class="bt" /></p>
</form>
</div>
</div>
<?php
	break;

	case 1:
	$dbname  = trim($_POST['dbname']);
	$uname   = trim($_POST['uname']);
	$dbpw = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	
	//创建数据库
	if(empty($dbname)) {
		tx_die("请正确输入数据库名<br><br><br><br><form action='setup-config.php' method='post'><input type=submit value='返回'  class='bt'/></form>");
	} else {
		if(!@mysql_connect($dbhost, $uname, $dbpw)) {
			error_log("Database connection error: " . mysql_error());
			$errno = mysql_errno();
			$error = mysql_error();
			if($errno == 1045) {
				tx_die("无法连接数据库，请检查数据库用户名或者密码是否正确<br>$error<br><br><br><br><form action='setup-config.php'  method='post'><input type=submit value='返回'  class='bt'/></form>");
			} elseif($errno == 2003) {
				tx_die("无法连接数据库，请检查数据库是否启动，数据库服务器地址是否正确<br>$error<br><br><br><br><form action='setup-config.php' method='post'><input type=submit value='返回'  class='bt'/></form>");
			} else {
				tx_die("数据库创建失败<br>$error<br><br><form action='setup-config.php' method='post'><input type=submit value='返回'  class='bt'/></form>");
			}
		}
		
		//创建数据库
		mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname` default character set utf8 collate utf8_general_ci");
		
		//创建teams数据表
		$sql_create = "CREATE TABLE IF NOT EXISTS teams(";
    	$sql_create = $sql_create . "id int(3) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "name varchar(50) not null,";
		$sql_create = $sql_create . "description text,";
		$sql_create = $sql_create . "members int(3) not null default 0,";
		$sql_create = $sql_create . "charts int(3) not null default 0,";
    	$sql_create = $sql_create . "lastTime timestamp)";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);
		
		//创建members数据表
		$sql_create = "CREATE TABLE IF NOT EXISTS members(";
    	$sql_create = $sql_create . "id int(3) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "email varchar(50) not null,";
		$sql_create = $sql_create . "UNIQUE INDEX (email),";
		$sql_create = $sql_create . "password varchar(20) BINARY,";
		$sql_create = $sql_create . "nickname varchar(30),";
		$sql_create = $sql_create . "valid int(1) not null default 1,";
		$sql_create = $sql_create . "validCode char(8),";
    	$sql_create = $sql_create . "lastTime timestamp)";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);		
		
		//创建charts数据表
		$sql_create = "CREATE TABLE IF NOT EXISTS charts(";
    	$sql_create = $sql_create . "id int(4) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "name varchar(100) not null,";
		$sql_create = $sql_create . "team int(4),";
		$sql_create = $sql_create . "chart_type int(1),";
    	$sql_create = $sql_create . "lastTime timestamp)";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);
		
		//members与teams对应关系表
		$sql_create = "CREATE TABLE IF NOT EXISTS members_team(";
    	$sql_create = $sql_create . "id int(5) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "member_id int(3),";
		$sql_create = $sql_create . "team_id int(4),";
		$sql_create = $sql_create . "INDEX (team_id))";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);
		
		//创建events数据表
		$sql_create = "CREATE TABLE IF NOT EXISTS events(";
    	$sql_create = $sql_create . "id int(6) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "team int(4),";
		$sql_create = $sql_create . "title varchar(255) not null,";
		$sql_create = $sql_create . "description text,";
		$sql_create = $sql_create . "source text,";
		$sql_create = $sql_create . "status int(1) not null default 0,";//0:running,1:close
		$sql_create = $sql_create . "creator varchar(30),";
    	$sql_create = $sql_create . "createTime datetime,";
		$sql_create = $sql_create . "updater varchar(30),";
		$sql_create = $sql_create . "lastTime datetime)";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);
		
		//创建files数据表,files与events关联
		$sql_create = "CREATE TABLE IF NOT EXISTS files(";
    	$sql_create = $sql_create . "id int(8) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "event int(8),";
		$sql_create = $sql_create . "fileName varchar(255),";
		$sql_create = $sql_create . "linkName varchar(20),";		
		$sql_create = $sql_create . "description text,";
		$sql_create = $sql_create . "updater varchar(30),";
		$sql_create = $sql_create . "lastTime datetime)";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);
		
		//创建reports数据表,reports与events关联
		$sql_create = "CREATE TABLE IF NOT EXISTS reports(";
    	$sql_create = $sql_create . "id int(8) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "event int(8),";
		$sql_create = $sql_create . "title varchar(255),";
		$sql_create = $sql_create . "content text,";
		$sql_create = $sql_create . "updater varchar(30),";
		$sql_create = $sql_create . "lastTime datetime)";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);
		
		//创建meetings数据表,meetings与events关联
		$sql_create = "CREATE TABLE IF NOT EXISTS meetings(";
    	$sql_create = $sql_create . "id int(8) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "event int(8),";
		$sql_create = $sql_create . "title varchar(255),";		
		$sql_create = $sql_create . "content text,";
		$sql_create = $sql_create . "updater varchar(30),";
		$sql_create = $sql_create . "lastTime timestamp)";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);
		
		//创建tasks数据表,tasks与events关联
		$sql_create = "CREATE TABLE IF NOT EXISTS tasks(";
    	$sql_create = $sql_create . "id int(8) UNSIGNED not null AUTO_INCREMENT,primary key(id),";
		$sql_create = $sql_create . "event int(8),";
		$sql_create = $sql_create . "team int(4),";
		$sql_create = $sql_create . "title varchar(255),";
		$sql_create = $sql_create . "creator varchar(30),";
		$sql_create = $sql_create . "cEmail varchar(50),";
		$sql_create = $sql_create . "createTime datetime,";
		$sql_create = $sql_create . "responser varchar(30),";
		$sql_create = $sql_create . "rEmail varchar(50),";
		$sql_create = $sql_create . "description text,";
		$sql_create = $sql_create . "complete int(1) not null default 0,";
		$sql_create = $sql_create . "expiration datetime,";
		$sql_create = $sql_create . "updater varchar(30),";
		$sql_create = $sql_create . "uEmail varchar(50),";
		$sql_create = $sql_create . "lastTime timestamp)";
		mysql_query("use `$dbname`;");
		mysql_query($sql_create);
				
		if(mysql_errno()) {
			tx_die("无法创建新的数据库，请检查数据库名称填写是否正确<br>".mysql_error()."<br><br><br><br><form action='setup-config.php' method='post'><input type=submit value='返回'  class='bt'/></form>");
		}
		mysql_close();
	}
	
	//Mysql配置项写入config.php
	$handle = fopen('../config_cache.php', 'w');

	foreach ($configFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('DB_NAME'":
				fwrite($handle, str_replace("putyourdbnamehere", $dbname, $line));
				break;
			case "define('DB_USER'":
				fwrite($handle, str_replace("'usernamehere'", "'$uname'", $line));
				break;
			case "define('DB_PASSW":
				fwrite($handle, str_replace("'yourpasswordhere'", "'$dbpw'", $line));
				break;
			case "define('DB_HOST'":
				fwrite($handle, str_replace("localhost", $dbhost, $line));
				break;
			default:
				fwrite($handle, $line);
		}
	}
	fclose($handle);
	chmod('../config_cache.php', 0666);	

	display_header();
?>
<div id="container" class="container">	
<form method="post" action="setup-config.php?step=2">
<div class="t1">请设置公司或组织名称</div>
<div class="hr"></div>
<div style="padding-left:100px; margin:20px;">公司或组织名称：<input name="company" type="text" class="inputText"/><br /></div>
<div class="t1">请配置管理邮箱</div>
<div class="hr"></div>
<div class="tip1">如果您使用过邮件客户端(如：Outlook、Foxmail等)，您将很熟悉以下配置项。<br />如果您的公司或组织没有内部邮件系统，建议您<a target="_blank" href="http://www.foxmail.com">申请使用Foxmail邮箱</a></div>
<div style="padding-left:100px;">
<table>
<tr><td  height="40">Email服务器：</td><td><input name="smtp" type="text" class="inputText" value="smtp.yourcompany.com"/><span style="font-size:12px; color:#666666"> Foxmail的邮件服务器为smtp.foxmail.com</span></td></tr>
<tr><td  height="40">端口：</td><td><input name="port" type="text" class="inputText" value="465"/><span style="font-size:12px; color:#666666"> 现代邮箱通常使用465(SSL)或587(TLS)端口</span></td></tr>
<tr><td  height="40">Email地址：</td><td><input name="email" type="text" class="inputText" value="yourname@yourcompany.com"/></td></tr>
<tr><td  height="40">Email密码：</td><td><input name="email_psw" type="password" class="inputText" /></td></tr>
</table><p><input name="Submit" type="submit" class="bt" value="下一步" /></p>
</form>
</div>
<?php
	break;
	
	case 2:
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
	$configFile = configMailIni($smtp, $port, $emailUname, $emailPsw,$email);
	error_log("邮件配置文件路径: $configFile");
	error_log("尝试发送邮件到: $email, 使用SMTP: $smtp, 端口: $port");
	
	if(!testMail($email)){
	    // 获取更详细的错误信息
	    $error = error_get_last();
	    $errorMsg = $error ? $error['message'] : '未知错误';
	    error_log("邮件发送失败: $errorMsg");
	    tx_die("您可能没有正确填写设置项，请返回确认<br>错误信息: $errorMsg<br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");
	}else{
			
		display_header();
	}	
?>
<div id="container" class="container">	
<form method="post" action="setup-config.php?step=3">
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
	
	case 3:
	
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
	
	redirect($ABS_PATH.'setup/invite.php');
	break;
}
?>
</body>
</html>
