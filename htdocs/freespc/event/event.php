<?php
$needAuthenticate = true;
require_once('../load.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>事件管理</title>
</head>

<body class="yui-skin-sam">
<?php 
$id = $_GET['id'];
global $wpdb;
require_db();
$event = $wpdb->get_row("SELECT * FROM events WHERE id=$id", ARRAY_A);
if(!is_array($event)){
	tx_die("参数错误！");
}
echo "<script>var teamId=".$event['team'].";var currentEvent = $id;var status = ".$event['status'].";var showEvent = 0;var lastModify = '".$event['updater']." |&nbsp;&nbsp;".justDate($event['lastTime'])."';</script>";
showMenuBar('event');
?>
<!--body-->
<div class="tip">
<div class="on" onclick="window.location.href='index.php'"><strong>事件管理</strong></div>
<div class="of" onclick="window.location.href='tasks.php'"><strong>任务</strong></div>
<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>
</div>
<div class="container" style="border-top:none;height:300px;padding-top:5px;">
<div class="path" style="width:250px"><a href="index.php">事件管理</a>
<?php if($_GET['a']) echo " > <a href='javascript:history.go(-1);'>已结案事件</a>";?> > 事件明细</div>
<div class="label" id="eventTitle"><strong><?php echo $event['title']?></strong></div><div><img id="arrow" src="../img/arrow_down.gif" /></div>
<div style="clear:both;"></div>
<div id="description" style="display:none;"><?php echo $event['description']?><br/>
<span style="color:#006699">负责Team:</span>
<?php
if($event['team'] == 0){
	echo "所有Team";
}else{
	$team = $wpdb->get_var("SELECT name FROM teams WHERE id=".$event['team']);
	echo $team;
}
?>
<br />
<?php 
if($event['source']){
	echo "<a href='javascript:void(0)' onclick='showSource()'>查看引用数据</a>&nbsp;";
	echo "<script>var source = '".$event['source']."';</script>";
}?>
(<?php echo $event['creator']?>于<?php echo $event['createTime']?>创建)</div>
<div style="margin-top:10px"><img src="../img/loading_tiny.gif" id="loading_1"/><div id="closeAlter"><input id="closed" type="checkbox" value="1"/><span id="closerFlag">未结案</span> <span id="lastModify"></span></div></div>
<div><?php require_once('buttons.php');?></div>
<div style="clear:both;margin-bottom:-13px;"></div>
<div id="details"></div>
<div style="clear:both"></div>
<br /><br />
<input type="button"  class="bt" value="返回"  style="width:100px" onclick="javascript:history.go(-1);"/>
<br />
</div>
<!--end of body-->
<?php 
showBottom();
require_once('itemWindow.php');
?>

</body>
</html>
<script type="text/javascript" src="../jui/util/connection-min.js"></script>
<script type="text/javascript" src="../jui/event/event.js"></script>
<link rel="stylesheet" type="text/css" href="../css/event.css"/>