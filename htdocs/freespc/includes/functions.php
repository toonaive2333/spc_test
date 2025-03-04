<?php
/**
 * 公共函数库
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(dirname(__FILE__)).'/');
}
  
/**
 * 格式化die函数
 */
function tx_die( $message, $title = '出现错误', $htmlTitle = 'Warning' ) {
	global $ABS_PATH;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>FreeSPC &rsaquo; <?php echo $htmlTitle?></title>
	<link rel="stylesheet" href="<?php echo $ABS_PATH?>css/util.css" type="text/css" />
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
		#message{
			margin-top:20px;
		}
		code{
			color:#339999;
		}
	</style>
</head>
<body>
	<table align="center"><tr><td>
	<div class="logo"><img src="<?php echo $ABS_PATH?>img/logo.jpg" /></div>
	<div class="container" id="container">
		<div class="t1"><?php echo $title ?></div>
		<div class="hr"></div>
		<div id="message"><?php echo $message; ?></div>
	</div>
	</td></tr></table>
</body>
</html>
<?php
	die();
}

/**
 * 载入数据库类文件
 */
function require_db() {
	global $wpdb;	
	require_once( ABSPATH . 'includes/wp-db.php' );	
}

/**
 * 跳转页面
 */
function redirect($url){
	header("location:$url");
}

/**
 * 显示菜单栏
 */
function showMenuBar($active_menu){
global $ABS_PATH;
?>
<!--header-->
<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/util/yahoo-min.js"></script>
<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/util/event-min.js"></script>
<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/util/dom-min.js"></script>
<script type="text/javascript" src="<?php echo $ABS_PATH?>jui/util/util.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $ABS_PATH?>css/util.css" />
<div id="header">
	<div id="herder_bar">
		<div id="logo"><img src="<?php echo $ABS_PATH?>img/logo2.jpg" />
			<div id="company"><?php echo COMPANY?></div>
		</div>
		<div style="float:right">
			<div id="log_tip"><a href="<?php echo $ABS_PATH?>logout.php">退出</a>(<?php echo $_COOKIE['login_name'];?>)</div>
			<div style="clear:both"></div>
			<div id="menu">
				<div id="<?php if($active_menu == 'record')echo 'active_menu';?>"><a href="<?php echo $ABS_PATH?>record/">录入数据</a></div>
				<div id="<?php if($active_menu == 'chart')echo 'active_menu';?>"><a href="<?php echo $ABS_PATH?>chart/">查看Chart</a></div>
				<div id="<?php if($active_menu == 'ooc')echo 'active_menu';?>"><a href="<?php echo $ABS_PATH?>ooc/">OOC报告</a></div>
				<div id="<?php if($active_menu == 'cpk')echo 'active_menu';?>"><a href="<?php echo $ABS_PATH?>cpk/">CPK报告</a></div>
				<div id="<?php if($active_menu == 'event')echo 'active_menu';?>"><a href="<?php echo $ABS_PATH?>event/">事件管理</a></div>
				<div id="<?php if($active_menu == 'setup')echo 'active_menu';?>"><a href="<?php echo $ABS_PATH?>setup/">设置</a></div>
			</div>
		</div>
	</div>
</div>
<div id="loadingTip">
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td width="3" height="25"></td>
    <td>
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td><img id="tip_logo"/></td>
			<td id="tip" nowrap="nowrap"></td>
		  </tr>
		</table>
	</td>
    <td width="3"></td>
  </tr>
  <tr>
    <td height="3"><img src="<?php echo $ABS_PATH?>img/tip_corner_left.jpg" /></td>
    <td></td>
    <td><img src="<?php echo $ABS_PATH?>img/tip_corner_right.jpg" /></td>
  </tr>
</table>
</div>
<!--end of header-->
<?php
}

/**
 * 显示底部栏
 */
function showBottom(){
?>
<!--header-->
<div id="bottom">
&copy; tonxon.com
</div>
<!--end of header-->
<?php
}
//接受ajax escape来的中文
function unescape($str) { 
	 $str = rawurldecode($str); 
	 preg_match_all("/%u.{4}|&#x.{4};|&#d+;|.+/U",$str,$r); 
	 $ar = $r[0]; 
	 foreach($ar as $k=>$v) { 
			  if(substr($v,0,2) == "%u") 
					   $ar[$k] = iconv("UCS-2","GBK",pack("H4",substr($v,-4))); 
			  elseif(substr($v,0,3) == "&#x") 
					   $ar[$k] = iconv("UCS-2","GBK",pack("H4",substr($v,3,-1))); 
			  elseif(substr($v,0,2) == "&#") { 
					   $ar[$k] = iconv("UCS-2","GBK",pack("n",substr($v,2,-1))); 
			  } 
	 } 
	 return join("",$ar); 
}

//string -> date(int),just for the datetime from mysql
function stringToDate($str){
	//2009-01-01 12:00:00
	$year = substr($str,0,4);
	$month = substr($str,5,2);
	$day = substr($str,8,2);
	$hour = substr($str,11,2);
	$minutes = substr($str,14,2);
	$seconds = substr($str,17,2);
	return mktime($hour,$minutes,$seconds,$month,$day,$year);
}
//compare date
function compareDate($d1,$d2){
	$gap = abs($d1-$d2);
	if(floor($gap/3600/24)>0)
		return round($gap/3600/24)."天";
	if(floor($gap/3600)>0)
		return round($gap/3600)."小时";
	if(floor($gap/60)>0)
		return round($gap/60)."分钟";
		
	return $gap."秒";
}
//date format
function justDate($dateTime){
	return date("Y/m/d",strtotime($dateTime));
}
function justTime($dateTime){
	return date("H:i:s",strtotime($dateTime));
}
?>