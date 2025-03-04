<?php
$needAuthenticate = true;
require_once('../load.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OOC</title>
</head>

<body>
<?php showMenuBar('ooc');?>
<!--body-->
<div class="tip">
<div class="on"><strong>待处理OOC</strong></div>	
<div class="of" onclick="window.location.href='history.php'"><strong>OOC查询</strong></div>
<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>			
</div>
<div class="container" style="border-top:none;height:160px;">
<?php
global $wpdb;
require_db();
$teams = "";
if($_COOKIE['login_isAdmin'] == 2)
	$teams = $wpdb->get_results("SELECT * FROM teams WHERE id IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") ORDER BY CONVERT(name USING gbk)", ARRAY_A);
else
	$teams = $wpdb->get_results("SELECT * FROM teams ORDER BY CONVERT(name USING gbk)", ARRAY_A);
echo "<script>var teams = new Array(".count($teams).");</script>";
if(is_array($teams)){
	$i = 0;
	foreach($teams as $team){
		echo "<b>".$team['name']."</b><a name=t".$team['id']."></a>";
		echo "<div id='team_".$team['id']."'><img src='../img/loading_tiny.gif'/></div>";
		echo "<script>teams[".($i++)."] = ".$team['id'].";</script>";
	}
}	
?>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>
<script>var ooc_type = "detail";</script>
<script type="text/javascript" src="../jui/util/connection-min.js"></script>
<script src="../jui/ooc/ooc.js"></script>
<link rel="stylesheet" type="text/css" href="../css/ooc.css"/>