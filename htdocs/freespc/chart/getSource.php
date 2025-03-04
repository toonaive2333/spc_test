<?php
$needAuthenticate = true;
require_once('../load.php');
$chartTypes = array('Xbar-R','Xbar-S','I-MR','P-Chart','NP-Chart','U-Chart','C-Chart');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>原始数据</title>
</head>

<body>
<?php

$id = $_COOKIE["chart_id"];
$type = $_COOKIE["chart_type"];
$sampleSize = $_COOKIE['sample_size'];
$minDate = $_COOKIE["chart_minTime"];
$maxDate = $_COOKIE["chart_maxTime"];
$chartName = $_COOKIE["chart_name"];
//table header
echo "<span style='font-size:14px'><b>".$chartName."</b>（".$chartTypes[$type-1]."）<br></span>";
echo "<span style='font-size:12px'>".$minDate." - ".$maxDate."</span>";
echo "<table width=100% bordercolor=#666666 border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;font-size:12px'>
<tr align=center style='font-weight:bold;background-color:#DDEEFF;' height=20><td> </td><td>Time</td>";
switch($type){
	case TYPE_XR:
	case TYPE_XS:	
		for($i=1;$i<=$sampleSize;$i++){
			echo "<td>Sample_$i</td>";
			echo "<td>ID_$i</td>";
		}
		echo "<td>Xbar</td>"; 
		if($type == TYPE_XR)
			echo "<td>Range</td>";
		else
			echo "<td>StDev</td>";
	break;
	case TYPE_IMR:
		echo "<td>Sample</td>";
		echo "<td>ID</td>";
		echo "<td>MR</td>";
	break;
	case TYPE_P:
		echo "<td>Sample count</td>";
		echo "<td>Subgroup size</td>"; 
		echo "<td>ID</td>";
		echo "<td>Proportion</td>";
	break;
	case TYPE_NP:
		echo "<td>Sample count</td>";
		echo "<td>Subgroup size</td>"; 
		echo "<td>ID</td>";
	break;
	case TYPE_U:
		echo "<td>Sample count</td>";
		echo "<td>Unit size</td>"; 
		echo "<td>ID</td>";
		echo "<td>Sample count/Unit</td>";
	break;
	case TYPE_C:
		echo "<td>Sample count</td>";
		echo "<td>ID</td>";
	break;
}
echo "<td>LCL</td><td>CL</td><td>UCL</td><td>Tests Failed</td></tr>";
//table detail
global $wpdb;
require_db();
$datas = $wpdb->get_results("SELECT * FROM chart_$id WHERE data_time BETWEEN '$minDate' AND '$maxDate' ORDER BY data_time ASC LIMIT 1000", ARRAY_A);
if(is_array($datas)){
	$row = 1;
	switch($type){
		case TYPE_XR:
		case TYPE_XS:
			foreach($datas as $data){
				echo "<tr align=center><td>".$row++."</td><td>".$data['data_time']."</td>";	
				for($i=1;$i<=$sampleSize;$i++){
					echo "<td>".$data["x_$i"]."</td>";
					echo "<td>".$data["product_$i"]."</td>";
				}
				echo "<td>".$data['xbar']."</td>"; 
				echo "<td>".$data['stat_value']."</td>";
				echo "<td>".$data['lcl']."</td>";
				echo "<td>".(($data['ucl']+$data['lcl'])/2)."</td>";
				echo "<td>".$data['ucl']."</td>";
				$against = $data['against'];
				if($against == 9)
					$against = 1;					
				echo "<td>$against</td></tr>";
			}		
		break;		
		case TYPE_IMR:
			foreach($datas as $data){
				echo "<tr align=center><td>".$row++."</td><td>".$data['data_time']."</td>";	
				echo "<td>".$data['x_1']."</td>"; 
				echo "<td>".$data['product_1']."</td>";
				echo "<td>".$data['stat_value']."</td>";
				echo "<td>".$data['lcl']."</td>";
				echo "<td>".$data['cl']."</td>";
				echo "<td>".$data['ucl']."</td>";
				$against = $data['against'];
				if($against == 9)
					$against = 1;					
				echo "<td>$against</td></tr>";
			}		
		break;
		case TYPE_P:
		case TYPE_U:
			foreach($datas as $data){
				echo "<tr align=center><td>".$row++."</td><td>".$data['data_time']."</td>";	
				echo "<td>".$data['ng_count']."</td>"; 
				echo "<td>".$data['total_count']."</td>";
				echo "<td>".$data['batch']."</td>";
				echo "<td>".$data['rate']."</td>";
				echo "<td>".$data['lcl']."</td>";
				echo "<td>".$data['cl']."</td>";
				echo "<td>".$data['ucl']."</td>";
				$against = $data['against'];				
				echo "<td>$against</td></tr>";
			}		
		break;
		case TYPE_NP:
		case TYPE_C:
			foreach($datas as $data){
				echo "<tr align=center><td>".$row++."</td><td>".$data['data_time']."</td>";	
				echo "<td>".$data['ng_count']."</td>"; 
				echo "<td>".$data['total_count']."</td>";
				echo "<td>".$data['batch']."</td>";
				echo "<td>".$data['lcl']."</td>";
				echo "<td>".$data['cl']."</td>";
				echo "<td>".$data['ucl']."</td>";
				$against = $data['against'];					
				echo "<td>$against</td></tr>";
			}		
		break;
	}
}
echo "</table>";
?>
</body>
</html>