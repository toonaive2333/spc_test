<?php
$needAuthenticate = true;
require_once('../load.php');
global $wpdb;
require_db();
$id = $_GET['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>查看Chart</title>
</head>

<body>
<?php
showMenuBar('chart');
?>

<!--body-->

<div id="selector">
<div id="current" class="title">选择Chart</div><div><img id="arrow" src="../img/arrow_down.gif" /> <a name="t" id="timeRange"></a></div>
<div id="charts_menu" style="visibility:hidden">
<?php
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
				foreach($charts as $chart){
					echo "<li>";
					echo "<a href='#t' onclick='setChart(".$chart['id'].",".$chart['chart_type'].",\"".$chart['name']."\")'>".$chart['name']."</a>";
					echo "</li>";
				}
			}
		echo "</ul>";
	}
}	
?>
</div></div>

<div class="container">	
<div id="bar">
<form method="post" target="_blank" id="getSourceForm"></form>
	<div id="minDate" title="起始时间"></div>
	<div><select id="minTime"></select></div>
	<div style="font-size:16px">&nbsp;~&nbsp;</div>
	<div id="maxDate" title="结束时间"></div>
	<div><select id="maxTime"></select></div>
	<div><input id="prev" type="button" value="< 向前移动" title="向前移动"/></div>
	<div>
		<select id="changer" title="选择移动时间"><option value="1d">1天</option><option value="7d" selected>1周</option><option value="1m">1月</option></select>
	</div>
	<div><input id="next" type="button" value="向后移动 >" title="向后移动"/></div>		
	<div><input id="showBt" type="button" value="显示Chart" title="显示Chart"/>
	</div>
	<div><input id="sourceBt" type="button" value="&nbsp导出数据&nbsp;" title="导出数据"/>
	</div>
	<div><input id="propertyBt" type="button" value="Chart属性" title="查看Chart属性"/>
	</div>
	<div><div id="errorPan" class="error_tip"><img src="../img/warning.gif" /><strong>出现错误：</strong>	<span id="error_content"></span></div></div>
	
	<div id="calendar_container"></div>
	<div id="closer"><img src="../jui/calendarBar/close.gif" width="11" height="11"/></div>
</div>
<div id="hr"></div>
<div id="chart_container"></div>	
</div>
</body>
</html>
<link rel="stylesheet" type="text/css" href="../jui/calendarBar/style.css"/>
<link rel="stylesheet" type="text/css" href="../css/chart.css" />
<script type="text/javascript" src="../jui/util/connection-min.js"></script>
<script src="../jui/chart/flash_manager.js"></script>
<script src="../jui/chart/chart.js"></script>
<script type="text/javascript" src="../jui/calendarBar/calendar.js"></script>
<script type="text/javascript" src="../jui/calendarBar/calendarBar.js"></script>
<?php 
$chart_minTime = $_COOKIE["chart_minTime"];
$chart_maxTime = $_COOKIE["chart_maxTime"];
$type = $_COOKIE["chart_type"];
$chartId = $_COOKIE['chart_id'];
$chartName = $_COOKIE["chart_name"];

if(!empty($chartId)){
	$mins = explode(" ",$chart_minTime);
	$maxs = explode(" ",$chart_maxTime);
	$minDate = $mins[0];
	$maxDate = $maxs[0];
	$minTime = $mins[1];
	$maxTime = $maxs[1];
	echo "<script>";	
	echo "Event.onDOMReady(function(){setTimeout(function(){";
		echo "setChart($chartId,$type,'$chartName');";
		echo "setMinDate('$minDate');";
		echo "setMaxDate('$maxDate');";
		echo "setMinTime('$minTime');";
		echo "setMaxTime('$maxTime');";
		echo "Dom.get('showBt').click();";
	echo "},1000);});";
	echo "</script>";
}
?>