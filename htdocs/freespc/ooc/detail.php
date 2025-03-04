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
<div class="on" onclick="window.location.href='index.php'"><strong>待处理OOC</strong></div>	
<div class="of" onclick="window.location.href='history.php'"><strong>OOC查询</strong></div>
<div class="l"><img src="<?php echo $ABS_PATH?>img/bqd_or.gif" width="5" height="30" /></div>			
</div>
<div class="container" style="border-top:none;height:160px">
<?php
$chartTypes = array('Xbar-R','Xbar-S','I-MR','P-Chart','NP-Chart','U-Chart','C-Chart');
global $wpdb;
require_db();
$chartId = $_GET['chartId'];
$chartType = $_GET['type'];
$chartName = urldecode($_GET['name']);
echo "<strong>$chartName</strong>(".$chartTypes[$chartType-1].")";
echo "<table width=100% bordercolor=#999999 border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;'>
<tr align=center style='font-weight:bold;background-color:#DDEEFF;'  height=20><td>Delay</td><td width=100>Tests Failed</td><td>Chart</td>";
switch($chartType){
	case TYPE_XR:
		echo "<td>Xbar</td>"; 
		echo "<td>Range</td>";
	break;
	case TYPE_XS:	
		echo "<td>Xbar</td>"; 
		echo "<td>StDev</td>";
	break;
	case TYPE_IMR:
		echo "<td>Sample</td>";
		echo "<td>MR</td>";
	break;
	case TYPE_P:
		echo "<td>Sample count</td>";
		echo "<td>Subgroup size</td>"; 
		echo "<td>Proportion</td>";
	break;
	case TYPE_NP:
		echo "<td>Sample count</td>";
		echo "<td>Subgroup size</td>"; 
	break;
	case TYPE_U:
		echo "<td>Sample count</td>";
		echo "<td>Unit size</td>"; 
		echo "<td>Sample count/Unit</td>";
	break;
	case TYPE_C:
		echo "<td>Sample count</td>";
	break;
}
echo "<td>LCL</td><td>CL</td><td>UCL</td></tr>";
$details = $wpdb->get_results("SELECT * FROM chart_$chartId WHERE against>0 AND status<2 ORDER BY data_time", ARRAY_A);
if(is_array($details)){	
	switch($chartType){
		case TYPE_XR:
		case TYPE_XS:				
			foreach($details as $point){
				$time = $point['data_time'];
				echo "<tr align=center><td>".compareDate(time(),strtotime($time))."($time)</td>";
				$against = $point['against'];
				if($against == 9)
					$against = 1; 
				echo "<td>Rule $against</td>"; 
				echo "<td><a target=_blank href='../chart/accessChart.php?chartId=".$chartId."&pointId=".$point['id']."'><img src='../img/little_chart2.gif' border=0/></a></td>";
				echo "<td>".$point['xbar']."</td>"; 
				echo "<td>".$point['stat_value']."</td>";
				echo "<td>".$point['lcl']."</td>";
				echo "<td>".(($point['ucl']+$point['lcl'])/2)."</td>";
				echo "<td>".$point['ucl']."</td></tr>";
			}
		break;
		case TYPE_IMR:
			foreach($details as $point){
				$time = $point['data_time'];
				echo "<tr align=center><td>".compareDate(time(),strtotime($time))."($time)</td>";
				$against = $point['against'];
				if($against == 9)
					$against = 1; 
				echo "<td>Rule $against</td>"; 
				echo "<td><a target=_blank href='../chart/accessChart.php?chartId=".$chartId."&pointId=".$point['id']."'><img src='../img/little_chart2.gif' border=0/></a></td>";
				echo "<td>".$point['x_1']."</td>"; 
				echo "<td>".$point['stat_value']."</td>";
				echo "<td>".$point['lcl']."</td>";
				echo "<td>".(($point['ucl']+$point['lcl'])/2)."</td>";
				echo "<td>".$point['ucl']."</td></tr>";
			}
		break;
		case TYPE_P:
		case TYPE_U:
			foreach($details as $point){
				$time = $point['data_time'];
				echo "<tr align=center><td>".compareDate(time(),strtotime($time))."($time)</td>";
				$against = $point['against'];
				if($against == 9)
					$against = 1; 
				echo "<td>Rule $against</td>"; 
				echo "<td><a target=_blank href='../chart/accessChart.php?chartId=".$chartId."&pointId=".$point['id']."'><img src='../img/little_chart2.gif' border=0/></a></td>";
				echo "<td>".$point['ng_count']."</td>";
				echo "<td>".$point['total_count']."</td>";
				echo "<td>".$point['rate']."</td>";
				echo "<td>".$point['lcl']."</td>";
				echo "<td>".$point['cl']."</td>";
				echo "<td>".$point['ucl']."</td></tr>";
			}
		break;
		case TYPE_NP:
			foreach($details as $point){
				$time = $point['data_time'];
				echo "<tr align=center><td>".compareDate(time(),strtotime($time))."($time)</td>";
				$against = $point['against'];
				if($against == 9)
					$against = 1; 
				echo "<td>Rule $against</td>"; 
				echo "<td><a target=_blank href='../chart/accessChart.php?chartId=".$chartId."&pointId=".$point['id']."'><img src='../img/little_chart2.gif' border=0/></a></td>";
				echo "<td>".$point['ng_count']."</td>";
				echo "<td>".$point['total_count']."</td>";
				echo "<td>".$point['lcl']."</td>";
				echo "<td>".$point['cl']."</td>";
				echo "<td>".$point['ucl']."</td></tr>";
			}
		break;
		case TYPE_C:
			foreach($details as $point){
				$time = $point['data_time'];
				echo "<tr align=center><td>".compareDate(time(),strtotime($time))."($time)</td>";
				$against = $point['against'];
				if($against == 9)
					$against = 1; 
				echo "<td>Rule $against</td>"; 
				echo "<td><a target=_blank href='../chart/accessChart.php?chartId=".$chartId."&pointId=".$point['id']."'><img src='../img/little_chart2.gif' border=0/></a></td>";
				echo "<td>".$point['ng_count']."</td>";	
				echo "<td>".$point['lcl']."</td>";
				echo "<td>".$point['cl']."</td>";
				echo "<td>".$point['ucl']."</td></tr>";			
			}
		break;
	}	
}
?>
</table>
<br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>
<div style="clear:both"></div>
</div>
<!--end of body-->
<?php showBottom();?>
</body>
</html>