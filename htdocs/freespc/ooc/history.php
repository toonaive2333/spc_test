<?php
$needAuthenticate = true;
require_once('../load.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OOC History</title>
</head>

<body>
<?php 
require_once('../event/pop.php');
showMenuBar('ooc');
?>
<!--body-->
<div class="tip">
<div class="of" onclick="window.location.href='index.php'"><strong>待处理OOC</strong></div>	
<div class="on"><strong>OOC查询</strong></div>
<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>			
</div>
<div class="container" style="border-top:none;height:160px;">
<div id="selector">
<div id="current" class="title">选择Chart(s)</div><div><img id="arrow" src="../img/arrow_down.gif" /></div>
<div id="charts_menu" style="visibility:hidden; ">
<?php
global $wpdb;
require_db();
$teams = "";
if($_COOKIE['login_isAdmin'] == 2)
	$teams = $wpdb->get_results("SELECT * FROM teams WHERE id IN (SELECT team_id FROM members_team WHERE member_id=".$_COOKIE['login_id'].") ORDER BY CONVERT(name USING gbk)", ARRAY_A);
else
	$teams = $wpdb->get_results("SELECT * FROM teams ORDER BY CONVERT(name USING gbk)", ARRAY_A);
if(is_array($teams)){
	foreach($teams as $team){
		echo "<div class='menu_title' onclick='showChartList(".$team['id'].")'>".$team['name']."</div>";
		echo "<ul id='charts_".$team['id']."'>";
			$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=".$team['id']." ORDER BY CONVERT(name USING gbk)", ARRAY_A);
			if(is_array($charts)){
					echo "<li>";
					echo "<a href='#' onclick='setTeam(".$team['id'].",\"".$team['name']."\");'>- - --选择全部-- - -</a>";
					echo "</li>";
				foreach($charts as $chart){
					echo "<li>";
					echo "<a href='#' onclick='setChart(".$chart['id'].",\"".$chart['name']."\",".$chart['chart_type'].",".$team['id'].")'>".$chart['name']."</a>";
					echo "</li>";
				}
			}
		echo "</ul>";
	}
}	
?>
</div>
</div>
<div id="bar">
	<div id="minDate" title="起始时间"></div>
	<div><select id="minTime"></select></div>
	<div style="font-size:16px">&nbsp;~&nbsp;</div>
	<div id="maxDate" title="结束时间"></div>
	<div><select id="maxTime"></select></div>
	<div><input id="showBt" type="button" value="查询OOC" title="查询历史OOC"/>
	</div>
	<div><div id="errorPan" class="error_tip"><img src="../img/warning.gif" /><strong>出现错误：</strong>	<span id="error_content"></span></div></div>
	<div><input id="prev" type="button" value="< 向前移动" title="向前移动"/></div>
	<div>
		<select id="changer" title="选择移动时间"><option value="1d">1天</option><option value="7d" selected>1周</option><option value="1m">1月</option></select>
	</div>
	<div><input id="next" type="button" value="向后移动 >" title="向后移动"/></div>		
	<div id="calendar_container"></div>
	<div id="closer"><img src="../jui/calendarBar/close.gif" width="11" height="11"/></div>
</div>
<div style="clear:both"></div>
<div id="results"></div>
<input id="eventBt" type="button" value="&nbsp;添加到事件管理&nbsp;" title="添加到时间管理" class="bt"/>
<div style="clear:both"></div>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>
<link rel="stylesheet" type="text/css" href="../jui/calendarBar/style.css"/>
<link rel="stylesheet" type="text/css" href="../css/ooc.css"/>
<script type="text/javascript" src="../jui/util/connection-min.js"></script>
<script src="../jui/ooc/history.js"></script>
<script type="text/javascript" src="../jui/calendarBar/calendar.js"></script>
<script type="text/javascript" src="../jui/calendarBar/calendarBar.js"></script>
