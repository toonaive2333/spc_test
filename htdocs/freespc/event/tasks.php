<?php
$needAuthenticate = true;
require_once('../load.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>事件管理 > 任务</title>
</head>

<body>
<?php 
showMenuBar('event');
?>
<!--body-->
<div class="tip">
	<div class="of" onclick="window.location.href='index.php';"><strong>事件管理</strong></div>
	<div class="on"><strong>任务</strong></div>
	<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>
</div>
<div class="container" style="border-top:none;height:300px;padding-top:5px;">
<div class="path" style="width:60px">任务</div>
<div id="tasks"></div>
<br/>
</div>
<!--end of body-->
<script>
var showEvent = 1;
</script>
<?php 
showBottom();
require_once('itemWindow.php');
?>
</body>
</html>
<script type="text/javascript" src="../jui/util/connection-min.js"></script>
<script type="text/javascript" src="../jui/event/tasks.js"></script>
<link rel="stylesheet" type="text/css" href="../css/event.css"/>