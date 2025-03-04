<?php
$needAuthenticate = true;
require_once('../load.php');
$chartTypes = array('Xbar-R','Xbar-S','I-MR','P-Chart','NP-Chart','U-Chart','C-Chart');

global $wpdb;
require_db();
$teamId = $_POST['teamId'];
$action = $_GET['t'];
if($action == "detail"){
	$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=$teamId  ORDER BY CONVERT(name USING gbk)", ARRAY_A);
	$hasOOC = false;
	if(is_array($charts)){
		foreach($charts as $chart){
			$ooc = $wpdb->get_var("SELECT count(*) FROM chart_".$chart['id']." WHERE against>0 AND status<2 ");
			if($ooc>0){
				$hasOOC = true;
				break;
			}
		}
		if($hasOOC){
	?>
	<table width="100%" border="1" bordercolor="#999999" style="border-collapse:collapse;" class="ooc_table">
		<tr style="background-color:#DDEEFF;" align="center" height="20"><td><strong>Chart</strong></td><td><strong>OOC count</strong></td><td><b>Detail</b></td><td><strong>Delay</strong></td><td><strong>Chart Type</strong></td></tr>
	<?php 
		foreach($charts as $chart){
			$ooc = $wpdb->get_var("SELECT count(*) FROM chart_".$chart['id']." WHERE against>0 AND status<2 ");
			if($ooc>0){
				echo "<tr align=center><td>";
				echo $chart['name'];
				echo "</td><td>";
				$ooc = $wpdb->get_var("SELECT count(*) FROM chart_".$chart['id']." WHERE against>0 AND status<2 ");
				echo $ooc;	
				echo "</td><td><a href='detail.php?chartId=".$chart['id']."&type=".$chart['chart_type']."&name=".urlencode($chart['name'])."'>查看</a>";	
				echo "</td><td>";
				$earlist = $wpdb->get_var("SELECT data_time FROM chart_".$chart['id']." WHERE against>0 AND status<2 ORDER BY data_time ASC LIMIT 1");
				$earlistDate = strtotime($earlist);
				echo compareDate(time(),$earlistDate);				
				echo "</td><td>";	
				echo $chartTypes[$chart['chart_type']-1];		
				echo "</td></tr>";
			}
		}	
	?>
	</table>
	<?php
		}else{
			echo "<table><tr><td>&nbsp;&nbsp;<img src='../img/ok.gif'></td><td style='font-size:12px'>无未处理OOC</td></tr></table>";
		}
	}
}else if($action == "total"){
	$charts = $wpdb->get_results("SELECT * FROM charts WHERE team=$teamId ORDER BY CONVERT(name USING gbk)", ARRAY_A);
	$totalCount = 0;
	$totalOOCCount = 0;
	if(is_array($charts)){
		foreach($charts as $chart){
			$results = $wpdb->get_results("SELECT status,against FROM chart_".$chart['id']." WHERE against>0",ARRAY_A);
			if(is_array($results)){
				foreach($results as $result){
					if($result['against']>0){
						if($result['status']<2){
							$totalOOCCount++;
						}
					}
				}
			}
		}
	}
	if($totalOOCCount <= 0)
		echo "&nbsp;&nbsp;<img src='../img/ok2.gif'>无未处理OOC";
	else
		echo "&nbsp;&nbsp;共有<code>".$totalOOCCount."</code>个OOC未处理 <a href='../ooc/#t$teamId'>查看</a>";
}
?>

<br>